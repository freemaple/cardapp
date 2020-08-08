<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Libs\Service\PostService;

use App\Models\Post\Post as PostModel;
use App\Models\Post\Category as CategoryModel;
use App\Cache\Post as PostCache;
use App\Cache\Category as CategoryCache;
use App\Cache\Notice as NoticeCache;

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
        $notice = NoticeCache::notice('post');
        $posts = PostService::getCategoryPost(0, 10);

        $data = [
            'title' => '文库',
            'description' => '文库',
            'keywords' => '',
            'recomBeautyPost' => $recomBeautyPost,
            'categorys' => $categorys,
            'notice' => $notice,
            'posts' => $posts
        ];

        $result = ['code' => 'Success'];
        $result['data'] = $data;
        return response()->json($result);
    }

     /**
     *分类文章
     *
     * @return void
    */
    public function category(Request $request, $id)
    {
        $result = ['code' => '2x1'];
        $category = [];
        if($id >0){
            $category = CategoryModel::where('id', '=', $id)->first();
        }
        $name =  isset($category['name']) ? $category['name'] : '';
        $categorys = CategoryCache::allCategory();
        $data = [
            'title' => '文库' .$name,
            'description' => '分类' . $name,
            'keywords' => '',
            'category' => $category,
            'categorys' => $categorys
        ]);

        $result['code'] = 'Success';

        $result['data'] = $data;

        return response()->json($result);
    }


    /**
     * 美文
     * @param  Request $request 
     * @return string           
     */
    public function getBeautyPost(Request $request){
        //保存基本信息
        $posts = PostService::getBeautyPost();
        $view = view('article.block.post', ['posts' => $posts])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
        $result['data'] = [];
        return response()->json($result);
    }
}
