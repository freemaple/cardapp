<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Libs\Service\ProductDispalyService;
use App\Models\Product\Product as ProductModel;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Product\Wish as ProductWishModel;

class ShopController extends BaseController
{
    /**
     * 自营产品
     *
     * @return void
    */
    public function index(Request $request)
    {
        $pageSize = 100;
        $products = ProductDispalyService::getShareProduct($pageSize);
        $products = $products->toArray();
        $view = view('shop.index',[
            'title' => '我的共享店铺',
            'products' => $products
        ]);
        return $view;
    }
}