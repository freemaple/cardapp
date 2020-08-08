<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Cache\Post as PostCache;
use App\Cache\Category as CategoryCache;
use App\Cache\User as UserCache;
use App\Models\User\User as UserModel;
use App\Models\Post\Post as PostModel;
use App\Models\Card\Card as CardModel;
use App\Models\Card\CardPost as CardPostModel;
use App\Models\Post\Reprint as PostReprintModel;
use App\Models\Store\Store as StoreModel;
use App\Libs\Service\CardService;
use App\Libs\Service\PostService;

class PostController extends BaseController
{
    /**
     * 文章
     *
     * @return void
    */
    public function view(Request $request, $number)
    {
        $session_u = Auth::user();
        $post = PostCache::getPost($number);
        if($post == null){
            //返回视图
            $view = view('post.no_exits');
            $view->with('title', '文章不存在或者已经下架');
            return $view;
        }
        $post_id = $post['id'];
        $u_id = $request->uid;
        if(!empty($session_u) && $session_u['id'] == $post['user_id']){
            $user = $session_u;
        }
        else if($u_id != null){
            $user = UserModel::where('u_id', $u_id)->first();
        }
        if(empty($user)){
            $user = UserCache::info($post['user_id']);
        }
        $user_id = !empty($user) ? $user['id'] : 0;
        $card = CardModel::where('user_id', '=', $user_id)
        ->orderBy('card.is_default', 'desc')
        ->orderBy('card.created_at', 'desc')->first();
        $post['tt'] =  PostService::descriptionText($post['description']);
        $post['tt'] = substr($post['tt'], 0, 1500);
        $post['tt-src'] = "http://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&per=0&pit=8&spd=7&ie=UTF-8&text=" . urlencode($post['tt']);
        $card_qrcode = CardService::qrcode($card, 120);
        $u_data = [$post['post_number']];
        if($u_id != null){
            $u_data['uid'] = $u_id;
        }
        $share_data = [
            'title' => $post['name'],
            'content' => $post['name'],
            'url' => \Helper::route('post_view', $u_data),
            'image' => \HelperImage::storagePath($post['image'])
        ];
        $ad_image = '';
        $store = StoreModel::where('user_id', $user_id)->first();
        $date = date('Y-m-d H:i:s');
        $store_enable = 0;
        if(!empty($store) && $store['status'] == '2'){
            $expire_date = $store->expire_date;
            if($expire_date != null && $date <= $expire_date){
                $store_enable = 1;
            }
        }

        if($store_enable){
            $ad_link = \Helper::route('store_view', [$store['id']]);
            if(!empty($store['banner'])){
                $ad_image = \HelperImage::storagePath($store['banner']);
            }
        } else {
            $ad_link = \Helper::route('shop');
        }
        if(empty($ad_image)){
            $ad_image = \Helper::asset_url('/media/images/default_store_banner.png');
        }

        $description = PostService::descriptionImage($post['description']);

        $post['description'] = $description;

        $view = view('post.view',[
            'title' => $post['name'],
            'description' => $post['name'],
            'keywords' => '',
            'post' => $post,
            'user' => $user,
            'card' => $card,
            'card_qrcode' => $card_qrcode,
            'share_data' => $share_data,
            'ad_link' => $ad_link,
            'ad_image' => $ad_image
        ]);
        return $view;
    }

     /**
     * 文章
     *
     * @return void
    */
    public function reprintview(Request $request, $id)
    {
        $session_u = Auth::user();
        $reprint_post = PostReprintModel::where('id', '=', $id)->first();
        if($reprint_post == null){
            //返回视图
            $view = view('post.no_exits');
            $view->with('title', '文章不存在或者已经下架');
            return $view;
        }
        $post = PostModel::where('id', '=', $reprint_post['post_id'])->where('delete', '!=', '1')->first();
        if($post == null){
            //返回视图
            $view = view('post.no_exits');
            $view->with('title', '文章不存在或者已经下架');
            return $view;
        }

        $post = PostCache::getPost($post['post_number']);
        if($post == null){
            //返回视图
            $view = view('post.no_exits');
            $view->with('title', '名片不存在或者已经下架');
            return $view;
        }
        if(!empty($session_u) && $session_u['id'] == $post['user_id']){
            $user = $session_u;
        }
        else {
            $user = UserCache::info($reprint_post['user_id']);
        }

        $user_id = !empty($user->id) ? $user->id : 0;
        
        $card = CardModel::where('is_default', '=', '1')->where('enable', '1')->where('user_id', $user_id)->first();
        if($card == null){
            $card = CardModel::where('enable', '1')->where('user_id', $user_id)->first();
        }
        $post['tt'] =  PostService::descriptionText($post['description']);
        $post['tt'] = substr($post['tt'], 0, 1500);
        $post['tt-src'] = "http://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&per=0&pit=8&spd=7&ie=UTF-8&text=" . urlencode($post['tt']);
        
        $store = StoreModel::where('user_id', $user_id)->first();
        $date = date('Y-m-d H:i:s');
        $store_enable = 0;
        if(!empty($store) && $store['status'] == '2'){
            $expire_date = $store->expire_date;
            if($expire_date != null && $date <= $expire_date){
                $store_enable = 1;
            }
        }

        $ad_image = '';

        if($store_enable){
            $ad_link = \Helper::route('store_view', [$store['id']]);
            $ad_image = \HelperImage::storagePath($store['banner']);
        } else {
            $ad_link = \Helper::route('shop');
        }
        if(empty($ad_image)){
            $ad_image = \Helper::asset_url('/media/images/default_store_banner.png');
        }

        $description = PostService::descriptionImage($post['description']);

        $post['description'] = $description;

        if(!empty($session_u) && $session_u['id'] == $post['user_id']){
            $share_url = \Helper::route('post_view', [$post['post_number']]);
        } else {
            $share_url = \Helper::route('post_reprint_view', [$reprint_post['id']]);
        }

        $share_data = [
            'title' => $post['name'],
            'content' => $post['name'],
            'url' => $share_url,
            'image' => \HelperImage::storagePath($post['image'])
        ];

        $view = view('post.view',[
            'title' => $post['name'],
            'description' => $post['name'],
            'keywords' => '',
            'post' => $post,
            'user' => $user,
            'card' => $card,
            'reprint_post' => $reprint_post,
            'ad_link' => $ad_link,
            'ad_image' => $ad_image,
            'share_data' => $share_data
        ]);
        return $view;
    }

    /**
     * 添加文章
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $user = Auth::user();
        $musics = MusicdCache::getMusic();
        $view = view('account.post.save',[
            'user' => $user,
            'title' => '添加文章',
            'description' => '',
            'keywords' => '',
            'musics' => $musics,
            'save_type' => 'add'
        ]);
        return $view;
    }

    /**
     * 编辑文章
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $number)
    {
        $user = Auth::user();
        $post = $user->post()->where('post_number', $number)->first();
        if($post == null){
            return;
        }
        $images = $post->image()->orderBy('id', 'desc')->limit(2)->get();
        $post = $post->toArray();
        $musics = MusicdCache::getMusic();
        $post_music = [];
        if($post['music_id']){
            $post_music = MusicModel::where('id', $post['music_id'])->first();
        }
        $view = view('account.post.save',[
            'user' => $user,
            'title' => '保存文章',
            'description' => '',
            'keywords' => '',
            'post' => $post,
            'images' => $images,
            'save_type' => 'edit',
            'musics' => $musics,
            'post_music' => $post_music
        ]);
        return $view;
    }

     /**
     * 浏览记录
     * @param  Request $request 
     * @return string           
     */
    public function viewed(Request $request){
        $data = $request->all();
        //数据校验
        $validator = \Validator::make($data, [
            'id' => 'required',
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->id;
        $post = PostModel::where('id', '=', $id)
        ->where('delete', '!=', '1')
        ->first();
        if($post == null){
            $result['code'] = "no_exits";
            $result['message'] = '';
            return $result;
        }
        if($post != null){
            $pkey = "post_view:" . $id;
            $user_post_view = \Cookie::get($pkey, '');
            if($user_post_view == null){
                $post->view_number = $post->view_number + 1;
                $post->save();
                \Cookie::queue($pkey, 1, 0.5);
            }
        }
        $result['code'] = "Success";
        $result['message'] = 'Success';
        return $result;
    }
}