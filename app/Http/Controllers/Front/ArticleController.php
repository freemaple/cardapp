<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Cache\Post as PostCache;
use App\Models\Post\Post as PostModel;
use App\Models\Post\Category as CategoryModel;
use App\Cache\Category as CategoryCache;
use App\Cache\Notice as NoticeCache;
use App\Libs\Service\PostService;

class ArticleController extends BaseController
{
    /**
     * 文库
     *
     * @return void
    */
    public function index(Request $request)
    {
        $categorys = CategoryCache::allCategory();
        $recomBeautyPost = PostCache::recomBeautyPost();
        $notice = NoticeCache::notice('article');
        $posts = PostService::getCategoryPost(0, 10);
        $view = view('article.index',[
            'title' => '文库',
            'description' => '文库',
            'keywords' => '',
            'recomBeautyPost' => $recomBeautyPost,
            'categorys' => $categorys,
            'notice' => $notice,
            'posts' => $posts
        ]);
        return $view;
    }

    /**
     *分类文章
     *
     * @return void
    */
    public function categorypost(Request $request, $id)
    {
        $category = [];
        if($id >0){
            $category = CategoryModel::where('id', '=', $id)->first();
            if($category == null){
                return redirect(\Helper::route('home'));
            }
        }
        $name =  isset($category['name']) ? $category['name'] : '';
        $posts = PostService::getCategoryPostList($id);
        $categorys = CategoryCache::allCategory();
        $view = view('article.category',[
            'title' => '文库' .$name ,
            'description' => '分类' . $name,
            'keywords' => '',
            'category' => $category,
            'categorys' => $categorys,
            'posts' => $posts
        ]);
        return $view;
    }

     /**
     *投稿文集
     *
     * @return void
    */
    public function beautyPost(Request $request)
    {
        $posts = PostService::getBeautyPost();
        $view = view('article.beauty',[
            'title' => '文集',
            'description' => '文集',
            'keywords' => '',
            'posts' => $posts
        ]);
        return $view;
    }
}