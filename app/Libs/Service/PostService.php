<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Post\Post as PostModel;
use App\Models\Post\BeautyPost as BeautyPost;
use App\Models\Post\PostImage as PostImageModel;
use App\Models\Card\CardPost as CardPostModel;
use App\Models\Card\Card as CardModel;
use App\Models\Card\Info as CardInfoModel;
use App\Models\Post\Category as CategoryModel;
use App\Cache\Post as PostCache;
use App\Models\Music\Music as MusicModel;
use  App\Libraries\Storage\Card as CardStorage;
use App\Models\Post\PostVideo as PostVideoModel;

class PostService
{
    /**
     * 保存用户基本信息
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public static function saveInfo($user = null, $request){
        $result = [];
        if($request->save_type == 'add'){
            $post = new PostModel();
            $post->user_id = $user->id;
            $post->post_number = static::generateNumber();
        } else {
            $id =  $request->id;
            $post = $user->post()->where('id', $id)->first();
            if($post == null){
                $result['code'] = "no_exits";
                $result['message'] = '文章不存在!';
                return $result;
            }
        }
        $post->category_id = $request->category_id ? $request->category_id : 0;
        $post->name = trim($request->name);
        $post->save();
        $description = trim($request->description);
        if($description){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc ->loadHTML('<?xml encoding="UTF-8">' . $description);//$str为一段HTML代码
            libxml_clear_errors();
            libxml_use_internal_errors($libxml_previous_state);
            $description_image = $doc->getElementsByTagName('img');
            if(count($description_image) > 0){
                foreach ($description_image as $key => $i) {
                    $src = $i->getAttribute('src');
                    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $src, $matches)){
                        $res = static::base64upload($src);
                        $page_image = new PostImageModel();
                        $page_image->post_id = $post->id;
                        $page_image->type = '2';
                        $page_image->image = $res['filepath'];
                        $page_image->save();
                        $i->setAttribute('src', $page_image->image);
                    }
                }
                $doc->encoding = 'UTF-8';
                $description = $doc->saveHTML();
            }
        }
        $post->description = $description;

        $post->background_color = trim($request->background_color);
        if(isset($request->music_id)){
            $post->music_id = intval($request->music_id) ? $request->music_id : 0;
        }
        $images = $request->imgs;
        $images = json_decode($images, true);
        $post->public = $request->public == '1' ? '1' : '0';
        $post->save();
        if(isset($request->card_ids)){
            $card_ids = $request->card_ids;
            $card_ids = json_decode($card_ids, true);
            if(!empty($card_ids)){
                $post_cards = $post->cardPost()->get();
                foreach ($card_ids as $key => $card_id) {
                    $card = CardModel::find($card_id);
                    $card_post = CardPostModel::where('post_id', $post->id)->where('card_id', $card_id)->first();
                    if($card != null && $card_post == null){
                        $CardPostModel = new CardPostModel();
                        $CardPostModel->card_id = $card_id;
                        $CardPostModel->post_id = $post->id;
                        $CardPostModel->save();
                    }
                }
                foreach ($post_cards as $key => $post_card) {
                    if(!in_array($post_card['card_id'], $card_ids)){
                        $post_card->delete();
                    }
                }
            }
        }
        if(isset($request->imgs) && is_array($images)){
            $image_model = PostImageModel::where('post_id', $post->id)->where('type', '=', '1');
            $old_images = $image_model->get()->toArray();
            $remove_imges = [];
            foreach ($images as $key => $image) {
                if($image['name'] != null){
                    $p = PostImageModel::where('post_id', $post->id)->where('image', $image['name'])->where('type', '=', '1')->first();
                }
                if($image['src']){
                    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $image['src'], $matches)){
                        $res = static::base64upload($image['src']);
                        $page_image = new PostImageModel();
                        $page_image->post_id = $post->id;
                        $page_image->image = $res['filepath'];
                        $page_image->save();
                        if(!empty($p)){
                            $remove_imges[] = $image['name'];
                        }
                    }
                }
            }
            try{
                foreach ($old_images as $key => $o_image) {
                    if(in_array($o_image['image'], $remove_imges)){
                        $p = PostImageModel::where('post_id', $post->id)->where('image', $o_image['image'])
                        ->where('type', '=', '1')->first();
                        $p->delete();
                        $image = $o_image['image'];
                        $CardStorage = new CardStorage('post');
                        $CardStorage->deleteFile($image);
                    }
                }
            } catch(\Exception $e){

            }
        }
        $video = $request->file('video');
        //dd($video);
        if($video && $video->isValid()){
            //获取上传文件的大小
            $size = $video->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 10*1024*1024){
                $result['code'] = '2x1';
                $result['message'] = '上传文件不能超过10M';
                return $result;
            }
            $CardStorage = new CardStorage('post_video');
            $filepath = $CardStorage->saveUpload($video);
            $post_id = $post->id;
            $PostVideoModel = PostVideoModel::where('post_id', '=', $post_id)->first();
            if($PostVideoModel == null){
                $PostVideoModel = new PostVideoModel();
                $PostVideoModel->user_id = $user->id;
                $PostVideoModel->post_id = $post_id;
            }
            $PostVideoModel->file = $filepath;
            $PostVideoModel->save();
        }
        PostCache::clearPostCache($post['post_number']);
      	$result['code'] = "Success";
        $result['message'] = '保存成功';
        return $result;
    }

    public static function descriptionImage($description){
        if($description){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc ->loadHTML('<?xml encoding="UTF-8">' . $description);//$str为一段HTML代码
            libxml_clear_errors();
            libxml_use_internal_errors($libxml_previous_state);
            $image = $doc->getElementsByTagName('img');
            $doc->encoding = 'UTF-8';
            if(count($image)){
                foreach ($image as $key => $i) {
                    $src = $i->getAttribute('src');
                    $src = \HelperImage::storagePath($src);
                    $i->setAttribute('src', $src);
                }
                $description = $doc->saveHTML();
            }
        }
        return $description;
    }

    public static function clearHtml($str) { 
        $str = trim($str); //清除字符串两边的空格
        $str = preg_replace("/[\s]{2,}/","",$str);
        $str = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags($str));
        $str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
        $str = preg_replace("/\r\n/","",$str); 
        $str = preg_replace("/\r/","",$str); 
        $str = preg_replace("/\n/","",$str); 
        $str = preg_replace("/ /","",$str);
        $str = preg_replace("/  /","",$str);  //匹配html中的空格
        return trim($str); //返回字符串
    }

    public static function descriptionText($description){
        if($description){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc ->loadHTML('<?xml encoding="UTF-8">' . $description);//$str为一段HTML代码
            libxml_clear_errors();
            libxml_use_internal_errors($libxml_previous_state);
            $doc->encoding = 'UTF-8';
            $description = $doc->textContent;
            $description = static::clearHtml($description);
        }
        return $description;
    }

    /**
     * 生成编号
     */
    public static function generateNumber(){
        $time_str = time();
        $md5_str = md5(rand(1, 10000));
        $number = $time_str.'C'.substr($md5_str, 0, 5);
        return $number;
    }

    //获取文章
    public static function getPost($post_number){
        $post = PostModel::where('post_number', $post_number)->where('delete', '!=', '1')->first();
        if($post == null){
            return $post;
        }
        if($post != null){
            $post_image = $post->images()->where('type', '=', '1')->first();
            $post['image'] = !empty($post_image) ? $post_image['image'] : '';
            $post_video = $post->videos()->first();
            $post['video'] = !empty($post_video) ? $post_video['file'] : '';
        }
        $post_music = [];
        if($post['music_id']){
            $post_music = MusicModel::where('id', $post['music_id'])->first();
            if($post_music != null){
                $post_music = $post_music->toArray();
            }
        }
        $post['post_music'] = $post_music;
        $post = $post->toArray();
        return $post;
    }

     /**
     * 上传产品图片
     */
    public static function base64upload($base64_image_content, $directory = 'post') {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $matches)){
            //图片路径地址    
            $fullpath = 'storage/' . $directory;
            if(!is_dir($fullpath)){
                mkdir($fullpath, 0777, true);
            }
            $image_file = \Image::make($base64_image_content);
            $width = $image_file->width();
            $height = $image_file->height();
            if($width > 1024){
                $h = 1024 / $width * $height;
                $image_file = $image_file->resize(1024, $h);
                $base64_image_content = $image_file->encode('data-url');
            }
            $type = $matches[2];
            $content_arr = explode($matches[0], $base64_image_content);
            $img = base64_decode($content_arr[1]);
            $filename = md5(date('YmdHis').rand(1000, 999999)). '.jpg';
            $filepath = 'post/' . $filename;
            $savepath = storage_path() . '/app/static/' . $filepath;
        
            //服务器文件存储路径
            if (file_put_contents($savepath, $img)){
                $result['status'] = 1;
                $result['filename'] = $filename;
                $result['filepath'] = $filepath;
                $result['filelink'] = \HelperImage::storagePath($filepath);
                return $result;
            }else{
                $result['status'] = 0;
                $result['message'] = '保存失败';
                return $result;
            }
        } else {
            $result['status'] = 0;
            $result['message'] = '不是有效的图片';
            return $result;
        }
    }

    /**
     * 名片二维码
     *
     * @return \Illuminate\Http\Response
     */
    public static function qrcode($post, $size){
        $post_link = Helper::route('post_view', $post['post_number']);
        $post_qrcode = Helper::qrcode1($post_link, $size);
        return  'data:image/png;base64,' . base64_encode($post_qrcode);
    }


    /**
     * 美文
     *
     * @return \Illuminate\Http\Response
     */
    public static function beautyPost($limit){
        $beauty_post = BeautyPost::select('post.*')->join('post', 'post.id', '=', 'beauty_post.post_id')
        ->where('beauty_post.status', '1')
        ->where('post.status', '1')
        ->where('public', '=', '1')
        ->where('in_article', '=', '1')
        ->where('post.delete', '!=', '1')
        ->orderBy('sort', 'desc')->limit($limit)->get();
        $posts_result = [];
        foreach ($beauty_post as $key => $post) {
            $posts_result[] = PostCache::getPost($post['post_number']);
        }
        return $posts_result;
    }

    public static function getCategoryPost($id, $limit){
        if($id >0){
            $category = CategoryModel::where('id', '=', $id)->first();
            if($category == null){
                return null;
            }
        }
        $pagesize = config('paginate.category_post', 100);
        $posts = PostModel::where('status', '=', '1')
        ->where('delete', '!=', '1')
        ->where('in_article', '=', '1')
        ->where('public', '=', '1');
        if($id > 0){
           $posts =  $posts->where('category_id', $id);
        }
        $posts = $posts->orderBy('post.created_at', 'desc')->limit($limit)->get();
        $posts_result = [];
        if(count($posts) > 0){
            foreach ($posts as $key => $post) {
                $posts_result[] = PostCache::getPost($post['post_number']);
            }
        }
        return $posts_result;
    }

     public static function getCategoryPostList($id){
        if($id >0){
            $category = CategoryModel::where('id', '=', $id)->first();
            if($category == null){
                return null;
            }
        }
        $pagesize = config('paginate.category_post', 50);
        $posts = PostModel::where('status', '=', '1')
        ->where('post.status', '1')
        ->where('in_article', '=', '1')
        ->where('public', '=', '1')
        ->where('post.delete', '!=', '1');
        if($id > 0){
           $posts =  $posts->where('category_id', $id);
        }
        $posts = $posts->orderBy('post.created_at', 'desc')->paginate($pagesize);
        $posts_result = [];
        foreach ($posts as $key => $post) {
            $posts_result[] = PostCache::getPost($post['post_number']);
        }
        return $posts_result;
    }

    public static function getSearchPost($keyword){
        $pagesize = config('paginate.search_post', 50);
        $posts = PostModel::select('post.post_number')->where('status', '=', '1')
        ->leftjoin('card_post', 'card_post.post_id', '=', 'post.id')
        ->leftjoin('card_info', 'card_info.card_id', '=', 'card_post.id')
        ->where(function ($query) use($keyword) {
            $query->where('post.name', 'like', '%'. sprintf("%s", $keyword). '%');
            $query->orWhere('card_info.organization', 'like', '%'. sprintf("%s", $keyword). '%');
        })
        ->where('post.status', '1')
        ->where('public', '=', '1')
        ->where('in_article', '=', '1')
        ->where('post.delete', '!=', '1')
        ->orderBy('post.created_at', 'desc')
        ->groupBy('post.post_number', 'post.created_at')
        ->paginate($pagesize);
        $posts_result = [];
        foreach ($posts as $key => $post) {
            $posts_result[] = PostCache::getPost($post['post_number']);
        }
        return $posts_result;
    }

    public static function getUserPostList($request, $pageSize){

        $user = \Auth::user();

        $card_id = $request->card_id;

        $type = $request->type;

        $name = $request->name;

        $card_ids = [];

        if($type != '2'){
            if($card_id > 0){
                $card = CardModel::where('id', $card_id)->first();
                if($card != null && $card['syn_card_id'] > 0){
                    $card_ids[] = $card['syn_card_id'];
                }
            } else {
                $user_cards = $user->card()->get();
                foreach ($user_cards as $ukey => $u_card) {
                    $card_ids[] = $u_card['id'];
                    if($u_card['syn_card_id'] > 0){
                        $card_ids[] = $u_card['syn_card_id'];
                    }
                }
            }
            if(!empty($card_ids)){
                $posts = PostModel::select('post.id')->join('card_post', 'card_post.post_id', '=', 'post.id')
                ->where('post.delete', '!=', '1')
                ->whereIn('card_post.card_id', $card_ids);
            } else {
                $posts = $user->post()->select('post.id');
            }
            if($name != ''){
                $posts = $posts->where('post.name', 'like', '%'. sprintf("%s", $name). '%');
            }
            $posts = $posts->where('post.delete', '!=', '1')->orderBy('post.id', 'desc')->groupBy('post.id')->paginate($pageSize);
            foreach ($posts as $key => $post) {
                $post = PostModel::where('id', $post['id'])->first();
                $posts_image = $post->images()->where('type', '=', '1')->first();
                if($posts_image != null){
                    $post->image = $posts_image['image'];
                    $post->image = \HelperImage::storagePath($post->image);
                }
                $posts[$key] = $post;
            }
        } else {
            $posts = PostModel::select('post.*', 'post_reprint.id as post_reprint_id')->join('post_reprint', 'post_reprint.post_id', '=', 'post.id')->where('post_reprint.user_id', '=', $user->id);
            if($name != ''){
                $posts = $posts->where('post.name', 'like', '%'. sprintf("%s", $name). '%');
            }
            $posts = $posts->where('post.delete', '!=', '1')->orderBy('post_reprint.id', 'desc')->paginate($pageSize);
            foreach ($posts as $key => $post) {
                $posts_image = $post->images()->where('type', '=', '1')->first();
                if($posts_image != null){
                    $post->image = $posts_image['image'];
                    $post->image = \HelperImage::storagePath($post->image);
                }
            }
        }
        return $posts;
    }

    //获取美文
    public static function getBeautyPost(){
        $pagesize = config('paginate.beauty_post', 50);
        $posts = PostModel::select('post.post_number')
        ->join('beauty_post', 'beauty_post.post_id', '=', 'post.id')
        ->where('beauty_post.status', '=', '1')
        ->where('post.status', '=', '1')
        ->where('post.delete', '!=', '1')
        ->where('public', '=', '1')
        ->orderBy('beauty_post.sort', 'desc')
        ->orderBy('beauty_post.created_at', 'desc')
        ->paginate($pagesize);
        $posts_result = [];
        foreach ($posts as $key => $post) {
            $posts_result[] = PostCache::getPost($post['post_number']);
        }
        return $posts_result;
    }
}