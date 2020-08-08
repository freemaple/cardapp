<?php

namespace App\Http\Controllers\Front;

use App\Libs\Service\PostService;
use App\Libs\Service\ProductDispalyService;
use Illuminate\Http\Request;
use Helper;

class SearchController extends BaseController
{

    /**
     * 搜索产品
     *
     * @return void
    */
    public function index(Request $request)
    {

        $keyword = trim($request->keyword);

        if($keyword == ''){
            return redirect(\Helper::route('home'));
        }

        $pageSize = config('paginate.product', 100);

        $sort = trim($request->sort);

        if($sort == null){
            $sort = 'new';
        }

        //搜索列表
        $products = ProductDispalyService::getSearchProduct($keyword);

        $view = view('search.index', [
            'title' => '搜索',
            'products' => $products,
            'keyword' => $keyword
        ]);

        if($keyword != null){
            $view->title = '搜索-' . $keyword;
        }

        return $view;
    }

    /**
     * 搜索文章
     *
     * @return void
    */
    public function post(Request $request)
    {

        $keyword = trim($request->keyword);

        if($keyword == ''){
            return redirect(\Helper::route('home'));
        }

        $pageSize = config('paginate.post', 100);

        $sort = trim($request->sort);

        if($sort == null){
            $sort = 'new';
        }

        //搜索文章列表
        $posts = PostService::getSearchPost($keyword);

        $view = view('search.post', [
            'title' => '搜索',
            'posts' => $posts,
            'keyword' => $keyword
        ]);

        if($keyword != null){
            $view->title = '搜索-' . $keyword;
        }

        return $view;
    }
}