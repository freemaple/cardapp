<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\PostService;
use Auth;
use App\Libs\Service\ProductDispalyService;
use App\Models\Product\Category as ProductCategoryModel;
use App\Cache\ProductCategory as ProductCategoryCache;

class CategoryController extends BaseController
{

     /**
     * 获取文章
     * @param  Request $request 
     * @return string           
     */
    public function getAllCategory(Request $request){
        $result = ['code' => 'Success'];
        //保存基本信息
        $category = ProductCategoryCache::getTopCategory();
        
        $result = ['code' => 'Success'];

        $result['data'] = ['category' => $category];
        
        return response()->json($result);
    }
	
    /**
     * 保存名片
     * @param  Request $request 
     * @return string           
     */
    public function getPost(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'category_id' => 'required|int|min:0'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->category_id;
        //保存基本信息
    	$posts = PostService::getCategoryPost($id, 8);
        $view = view('article.block.c_post', ['posts' => $posts, 'category_id' => $id])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
    	return response()->json($result);
    }

    /**
     * 保存名片
     * @param  Request $request 
     * @return string           
     */
    public function getPostList(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'category_id' => 'required|int|min:0'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->category_id;
        //保存基本信息
        $posts = PostService::getCategoryPostList($id);
        $view = view('article.block.post', ['posts' => $posts, 'category_id' => $id])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
        $result['data'] = [];
        return response()->json($result);
    }

    /**
     *分类产品
     * @param  Request $request 
     * @return string           
     */
    public function getProductList(Request $request, $id){

        $result = ['code' => ''];

        $product_category = ProductCategoryCache::getTopCategory();

        $sort = $request->sort;

        //获取分类产品
        $products = ProductDispalyService::getCategoryProduct($id, $sort);

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
