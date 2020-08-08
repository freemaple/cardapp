<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Libs\Service\ProductDispalyService;
use App\Models\Product\Category as ProductCategoryModel;
use App\Cache\ProductCategory as ProductCategoryCache;


class CategoryController extends BaseController
{

    /**
     * view
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $id)
    {
         if($id > 0){
            $category = ProductCategoryModel::where('id', '=', $id)->first();
         } else {
            $category = ['id' => 'all', 'name' => '全部'];
         }
       

        $product_category = ProductCategoryCache::getTopCategory();

        //获取分类产品
        $products = ProductDispalyService::getCategoryProduct($id);

        return view('category.view')->with([
            'category' => $category,
            'products' => $products,
            'categorys' => $product_category
        ]);
    }
}
