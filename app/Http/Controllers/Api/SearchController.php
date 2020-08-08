<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Libs\Service\PostService;
use App\Libs\Service\ProductDispalyService;

class SearchController extends BaseController
{

    /**
     * 保存名片
     * @param  Request $request 
     * @return string           
     */
    public function getPostList(Request $request){

        $keyword = trim($request->keyword);

        //搜索文章列表
        $posts = PostService::getSearchPost($keyword);

        $result = ['code' => 'Success'];

        if($request->type == 'app'){

            $result['data'] = $posts;

        } else {
            $view = view('article.block.post', ['posts' => $posts])->render();
            $result['view'] = $view;
            $result['data'] = [];
        }
        return response()->json($result);
    }

     /**
     * 搜索列表
     * @param  Request $request 
     * @return string           
     */
    public function getProductList(Request $request){

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

        $result = ['code' => 'Success'];

        if($request->type == 'app'){

            $result['data'] = $products;

        } else {
            $view = view('shop.block.products', ['products' => $products])->render();
            $result['view'] = $view;
            $result['data'] = [];
        }

        
        return response()->json($result);
    }
}
