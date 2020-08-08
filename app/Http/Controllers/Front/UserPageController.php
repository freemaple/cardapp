<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use Auth;
use Helper;
use App\Libs\Service\PostService;
use App\Cache\Category as CategoryCache;
use  App\Models\Post\Reprint as ReprintModel;
use App\Models\Music\Music as MusicModel;
use App\Cache\Music as MusicdCache;

class UserPageController extends BaseController
{

    /**
     * 文章列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $form = ['card_id' => '', 'type' => '', 'name' => ''];

        $request_data = $request->all();

        $form = array_merge($form, $request_data);

        $user = Auth::user();

        $pageSize = 20;

        $posts = PostService::getUserPostList($request, $pageSize);

        $cards = $user->card()->get();

        $posts->appends($request->all());

        $pager = $posts->links();

        $view = view('account.post.index',[
            'user' => $user,
            'title' => '文章管理',
            'description' => '',
            'keywords' => '',
            'posts' => $posts,
            'cards' => $cards,
            'form' => $form,
            'pager' => $pager
        ]);
        return $view;
    }

    /**
     * 添加文章
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $card_id = trim($request->card_id);
        $user = Auth::user();
        $categorys = CategoryCache::allCategory();
        $card_id = $request->card_id;
        $cards = $user->card()->get();
        $is_card = false;
        foreach ($cards as $key => $card) {
            if($card['id'] == $card_id){
                $card['is_select'] = '1';
                $is_card = true;
            }
        }
        if(!$is_card && !empty($cards)){
           $cards[0]['is_select'] = '1';  
        }
        $musics = MusicdCache::getMusic();
        $post_music = [];
        $view = view('account.post.save',[
            'user' => $user,
            'title' => '添加文章',
            'description' => '',
            'keywords' => '',
            'save_type' => 'add',
            'categorys' => $categorys,
            'card_id' => $card_id,
            'cards' => $cards,
            'musics' => $musics,
            'post_music' => $post_music
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
            return ;
        }
        $post_card_ids = $post->cardPost()->pluck('card_id');
        $post_card_ids = $post_card_ids->toArray();
        $posts_image = $post->images()->where('type', '=', '1')->first();
        if($posts_image != null){
            $post->image = $posts_image['image'];
        }

        $post = $post->toArray();
        $categorys = CategoryCache::allCategory();
        $cards = $user->card()->get();

        foreach ($cards as $key => $card) {
            if(in_array($card['id'], $post_card_ids)){
                $card['is_select'] = '1';
            }
        }
        $musics = MusicdCache::getMusic();
        $post_music = [];
        if($post['music_id']){
            $post_music = MusicModel::where('id', $post['music_id'])->first();
        }

        $description = PostService::descriptionImage($post['description']);

        $post['description'] = $description;

        $view = view('account.post.save',[
            'user' => $user,
            'title' => '编辑文章',
            'description' => '',
            'keywords' => '',
            'post' => $post,
            'save_type' => 'edit',
            'categorys' => $categorys,
            'cards' => $cards,
            'musics' => $musics,
            'post_music' => $post_music
        ]);
        return $view;
    }

     /**
     * 转载文章
     *
     * @return \Illuminate\Http\Response
     */
    public function postReprintAdd(Request $request)
    {
        $user = Auth::user();
        if($user == null){
            return redirect(Helper::route('auth_login', ['register']));
        }
        if($user['is_vip'] != '1' || $user['level_status'] <=0){
            return redirect(Helper::route('account_vipUpgrade'));
        }
        $view = view('account.post.reprint.add',[
            'user' => $user,
            'title' => '转载文章',
            'description' => '',
            'keywords' => '',
            'post' => $post,
            'save_type' => 'edit',
            'categorys' => $categorys,
            'cards' => $cards
        ]);
        return $view;
    }
}
