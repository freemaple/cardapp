<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Cache\Banner as BannerCache;

use App\Libs\Service\ProductDispalyService;
use App\Cache\ProductCategory as ProductCategoryCache;
use App\Cache\Home as HomeCache;

class SiteController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $view = View('site.index');

        $banners = BannerCache::home();

        $products = HomeCache::shareProduct();

        $store_products = HomeCache::getHomeStoreProduct();

        $product_category = ProductCategoryCache::getTopCategory();

        $store_products = $store_products->toArray();

        $share_data = [
            'title' => '人人有赏个人网页',
            'content' => '人人有赏个人网页...让创业更简单'
        ];

        $view->with([
            'title' => "人人有赏个人名片网页 自媒体 新零售 让创业更简单！",
            'banners' => $banners,
            'products' => $products,
            'store_products' => $store_products,
            'categorys' => $product_category,
            'share_data' => $share_data
        ]);

        return $view;
    }

    /**
     * 用户列表
     *
     * @return void
    */
    public function iphoneApp(Request $request)
    {
        $view = View('site.iphoneapp');

        $share_data = [
            'title' => '人人有赏个人网页',
            'content' => '人人有赏个人网页...让创业更简单'
        ];

        $view->with([
            'title' => "人人有赏个人名片网页 自媒体 新零售 让创业更简单！",
            'share_data' => $share_data
        ]);

        return $view;
    }

     /**
     * 用户列表
     *
     * @return void
    */
    public function viewdhistory(Request $request)
    {
        $view = View('site.viewdhistory');

        $share_data = [
            'title' => '人人有赏个人网页',
            'content' => '人人有赏个人网页...让创业更简单'
        ];

        $view->with([
            'title' => "浏览记录",
            'share_data' => $share_data
        ]);

        return $view;
    }
}