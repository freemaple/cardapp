<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Libs\Service\ProductDispalyService;
use App\Models\Product\Product as ProductModel;
use App\Models\Product\Wish as ProductWishModel;

class WishController extends BaseController
{
    /**
     * 产品评论列表
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function index(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        $pageSize = config('paginate.wish.list', 100);
        $products = ProductDispalyService::getWishProduct($user_id, $pageSize);
        $products = $products->toArray();
        $view = view('account.wish.index',[
            'title' => '收藏列表',
            'products' => $products
        ]);
        return $view;
    }

}