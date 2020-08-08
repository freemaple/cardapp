<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use Helper;
use App\Libs\Service\CardService;
use Auth;
use App\Models\Theme\Background;
use App\Models\User\User as UserModel;
use App\Models\Post\Post as PostModel;
use App\Models\Card\Card as CardModel;
use App\Models\Card\CardAlbum as CardAlbumModel;
use App\Cache\Post as PostCache;
use App\Cache\Card as CardCache;
use  App\Libraries\Storage\Card as CardStorage;

class CardController extends BaseController
{

    /**
     * 名片主页页面
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $cards = $user->card()->orderBy('id','desc')->get();

        $cards = $cards->toArray();

        foreach ($cards as $key => $card) {
            if(!empty($card['syn_card_id'])){
                $syn_card = CardModel::where('id', $card['syn_card_id'])->first();
                if($syn_card != null){
                    $syn_card = $syn_card->toArray();
                }
                $cards[$key]['syn_card'] = $syn_card;
            }
            $cards[$key]['qr'] = $this->qrcode($card, 100);
        }

        $sys_cards = CardService::getSynCardList(100, null);

        $result = [];

        $result['code'] = 'Success';

        $result['data'] = [
            'user' => $user,
            'title' => '名片列表',
            'description' => '',
            'keywords' => '',
            'cards' => $cards,
            'sys_cards' => $sys_cards
        ];
        return response()->json($result);
    }

    /**
     * 名片二维码
     *
     * @return \Illuminate\Http\Response
     */
    public function qrcode($card){
        $user = Auth::user();
        $card_link = Helper::route('card_view', [$card['card_number']]);
        $card_qrcode = Helper::qrcode1($card_link, 150);
        return  'data:image/png;base64,' . base64_encode($card_qrcode);
    }

	
    /**
     * 保存名片
     * @param  Request $request 
     * @return string           
     */
    public function saveInfo(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required|max:64',
            'organization' => 'letter|max:100',
            'department' => 'letter|max:100',
            'position' => 'letter|max:100'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
    	$user = Auth::user();
        //保存基本信息
    	$result = CardService::saveInfo($user, $request);
    	return response()->json($result);
    }

    /**
     * 设置默认名片
     * @param  Request $request 
     * @return string           
     */
    public function setdefault(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required|int|min:1'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->id;
        $user = Auth::user();
        $card = CardModel::where('id', '=', $id)->where('user_id', $user->id)->first();
        if($card == null){
            $result['message'] = '名片不存在！';
            return response()->json($result);
        }
        //注册事务操作
        $return_result = \DB::transaction(function() use ($card, $user) {
            $card->is_default = '1';
            $card->save();
            CardModel::where('user_id', $user->id)->where('is_default', '!=', '0')->where('id', '!=', $card->id)->update(['is_default' => '0']);
            return true;
        });
        $result['code'] = 'Success';
        $result['message'] = '保存成功';
        return response()->json($result);
    }

    /**
     * 保存个性化
     * @param  Request $request 
     * @return string           
     */
    public function saveCustom(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $user = Auth::user();
        $id =  $request->id;
        $card = $user->card()->where('id', $id)->first();
        if($card == null){
            $result['code'] = "no_exits";
            $result['message'] = '名片不存在!';
            return $result;
        }
        if(isset($request->background_image)){
            $card->background_image = $request->background_image;
        }
        if(isset($request->music)){
            $card->music = trim($request->music);
        }
        $card->save();
        $result['code'] = "Success";
        $result['message'] = '保存成功';
        return response()->json($result);
    }

    /**
     * 名片设置
     * @param  Request $request 
     * @return string           
     */
    public function setting(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required|int|min:1',
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->id;
        $user = Auth::user();
        $card = $user->card()->where('id', '=', $id)->first();
        if($card == null){
            $result['code'] = "no_exits";
            $result['message'] = '名片不存在';
        }
        $update_card = false;
        if(isset($request->enable)){
            $update_card = true;
            $card->enable = $request->enable == '1' ? '1' : '0';
            $card->save();
        }
        if($update_card){
            CardCache::clearCardCache($card['card_number']);
        }
        $result['code'] = "Success";
        $result['message'] = '保存成功';
        return response()->json($result);
    }

     /**
     * 删除名片相册
     * @param Request $request [description]
     */
    public function contributeCard(Request $request){

        $result = [];

        $user = Auth::user();

        $card_id = $request->card_id;

        $card = CardModel::where('id', '=', $card_id)->where('user_id', $user->id)->first();
        if($card == null){
            $result['message'] = '名片不存在！';
            return response()->json($result);
        }

        $result['code'] = 'error';
        $result['message'] = '禀奏皇上，投稿人数过多，审核缓冲中...';
        return response()->json($result);
    }

     /**
     * 名片设置
     * @param  Request $request 
     * @return string           
     */
    public function synCard(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'card_id' => 'required|int|min:1'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $card_id = $request->card_id;
        $user = Auth::user();
        $card = $user->card()->where('id', '=', $card_id)->first();
        if($card == null){
            $result['code'] = "no_exits";
            $result['message'] = '名片不存在';
        }
        $syn_card_id = $request->syn_card_id;
        $syn_card = CardModel::where('id', $syn_card_id)->first();
        if($syn_card == null){
            $result['code'] = "no_exits";
            $result['message'] = '名片不存在';
        }
        $card->syn_card_id = $syn_card_id;
        if(empty($card->background_image) && !empty($syn_card['background_image'])){
            $card->background_image = $syn_card['background_image'];
        }
        $card->save();
        CardCache::clearCardCache($card['card_number']);
        $result['code'] = "Success";
        $result['message'] = '同步成功';
        return response()->json($result);
    }

    /**
     * 取消同步名片
     * @param  Request $request 
     * @return string           
     */
    public function cancelSynCard(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'card_id' => 'required|int|min:1'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $card_id = $request->card_id;
        $user = Auth::user();
        $card = $user->card()->where('id', '=', $card_id)->first();
        if($card == null){
            $result['code'] = "no_exits";
            $result['message'] = '名片不存在';
        }
        $card->syn_card_id = 0;
        $card->save();
        CardCache::clearCardCache($card['card_number']);
        $result['code'] = "Success";
        $result['message'] = '取消同步成功';
        return response()->json($result);
    }

    /**
     * 名片文章
     * @return array
     */
    public function post(Request $request, $id){

        $result = [];

        $pagesize = 50;

        $card = CardModel::where('id', '=', $id)->first();
        if($card == null){
            return $result;
        }
        $syn_card_id = $card->syn_card_id;
        $card_ids = [];
        $card_ids[] = $card['id'];
        if($syn_card_id > 0){
            $card_ids[] = $syn_card_id;
        }
        $user_id = $card['user_id'];
        $card_user = UserModel::where('id', $user_id)->first();
        $posts = PostModel::join('card_post', 'card_post.post_id', 'post.id')
        ->select('post.id', 'post_number')
        ->whereIn('card_id', $card_ids)
        ->where('post.delete', '!=', '1')
        ->orderBy('post.id', 'desc')
        ->paginate($pagesize);
        $posts_result = [];
        foreach ($posts as $key => $post) {
            $posts_result[] = PostCache::getPost($post['post_number']);
        }
        $posts = $posts->toArray();
        unset($posts['data']);
        $view = view('card.block.post_list')->with('posts', $posts_result)
        ->with('card_user', $card_user)
        ->render();
        $result['code'] = "Success";
        $result['view'] = $view;
        $result['data'] = $posts;
        return response()->json($result);
    }

    /**
     * 名片屏保
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function screen(Request $request){
        $img = $request->img;
        $postion = $request->postion;
        $user = \Auth::user();
        $path = storage_path() . '/app/static/' . $img;
        // 修改指定图片的大小
        
        $img = \Image::make($path);
        $width = $img->width();
        $height = $img->height();
        $p = 640 / $width;
        $width = 640;
        $height = $height * $p;
        $img = $img->resize($width, $height);
        $po = 640 / 260;
        $card = $user->card()->where('is_default', '1')->where('enable', '1')->first();
        $card_qrcode = CardService::qrcode($card, 80 * $po);
        // 插入水印, 水印位置在原图片的右下角, 距离下边距 10 像素, 距离右边距 15 像素
        $image_data = $img->insert($card_qrcode, 'top-left', floor($postion['left'] * $po), floor($postion['top'] * $po))->encode('data-url');
        $base64_code = $image_data->encoded;
        $result = [];
        $result['code'] = 'Success';
        $result['data'] = $base64_code;
        return response()->json($result);
    }

    /**
     * 名片相册
     * @param Request $request [description]
     */
    public function cardAlbum(Request $request){

        $result = [];

        $card_id = $request->card_id;

        $card = CardModel::where('id', '=', $card_id)->first();
        if($card == null){
            $result['message'] = '名片不存在！';
            return response()->json($result);
        }

        $card_album = $card->albums()->get();

        if($card_album != null && count($card_album) > 0){
            $card_album = $card_album->toArray();
        }
    
        $result = [];
        $result['code'] = 'Success';
        $result['data'] = $card_album;
        $result['message'] = '保存成功';
        return response()->json($result);
    }

    /**
     * 添加名片相册
     * @param Request $request [description]
     */
    public function addCardAlbum(Request $request){

        $result = [];

        $card_id = $request->card_id;

        $card = CardModel::where('id', '=', $card_id)->first();
        if($card == null){
            $result['message'] = '名片不存在！';
            return response()->json($result);
        }
        $user = Auth::user();

        $card_album_count = $card->albums()->count();

        if(count($card_album_count) >= 12){
            $result['code'] = '2x1';
            $result['message'] = '内存不足了，只能添加12张';
        }

        //检查文件上传
        $file = $request->file('image');
        if(!$file || !$file->isValid()){
            $result['code'] = '2x1';
            $result['message'] = 'This is not a valid image.';
            return response()->json($result);
        }
        //获取上传文件的大小
        $size = $file->getSize();
        if($size > 5*1024*1024){
            $result['code'] = '2x1';
            $result['message'] = '上传文件不能超过5M';
            return response()->json($result);
        }
        $path = $file->path();
        $type = $file->getClientMimeType();
        list($width, $height, $type, $attr) = getimagesize($path);
        $UserStorage = new CardStorage('card_album');
        if($width > 1024){
            $h = 1024 / $width * $height;
            $img = \Image::make($file);
            $filepath = 'card_album/' . md5(time()) . '.jpg';
            $img = $img->resize(1024, $h)->save(storage_path() . '/app/static/' . $filepath);
        } else {
            $filepath = $UserStorage->saveUpload($file);
        }
        $card_album = new CardAlbumModel();
        $card_album->card_id = $card_id;
        $card_album->image = $filepath;
        $card_album->save();
        CardCache::clearCardCache($card['card_number']);
        $result['code'] = 'Success';
        $result['message'] = '保存成功';
        return response()->json($result);
    }

     /**
     * 删除名片相册
     * @param Request $request [description]
     */
    public function removeCardAlbum(Request $request){

        $result = [];

        $user = Auth::user();

        $card_id = $request->card_id;

        $card = CardModel::where('id', '=', $card_id)->where('user_id', $user->id)->first();
        if($card == null){
            $result['message'] = '名片不存在！';
            return response()->json($result);
        }

        $card_album_id = $request->card_album_id;   

        $card_album = $card->albums()->where('card_album.id', $card_album_id)->first();

        if($card_album == null){
            $result['message'] = '名片相册不存在！';
            return response()->json($result);
        }

        if($card_album != null){
            $old_album_image = $card_album['image'];
            $res = $card_album->delete();
             if($old_album_image != null){
                $UserStorage = new CardStorage('card_album');
                $UserStorage->deleteFile($old_album_image);
            }
        }

        $result['code'] = 'Success';
        $result['message'] = '删除成功！';
        return response()->json($result);
    }
}
