<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;


use App\Libs\Service\ProductDispalyService;
use App\Cache\ProductCategory as ProductCategoryCache;
use App\Cache\Home as HomeCache;
use App\Cache\Banner as BannerCache;
use App\Cache\Notice as NoticeCache;
use Helper;

class SiteController extends BaseController
{

     /**
     * 首页
     * @param  Request $request 
     * @return string           
     */
    public function home(Request $request){
        
        $banners = BannerCache::home();

        $category = HomeCache::homecategory();

        $products = HomeCache::shareProduct();

        $share_data = [
            'title' => '乐分享',
            'content' => '乐分享,让创业更简单'
        ];

        $menus = $this->getMenus();

        $data = [
            'title' => "乐分享 让创业更简单！",
            'banners' => $banners,
            'categorys' => $category,
            'menus' => $menus,
            'share_data' => $share_data,
            'products' => $products,
            'vip_gift_image' => Helper::asset_url('/media/images/vip_gift.jpg'),
            'titles' => [
                'list1' => '我的共享店铺',
                'list2' => '热销精品'
            ]
        ];

        $result = ['code' => 'Success'];
        $result['data'] = $data;
        return response()->json($result);
    }

    private function getMenus(){
        $menus = [
            [
                'name' => '文库',
                'link' => '/pages/article/index',
                'icon' => Helper::asset_url('/media/images/icon/art.png') 
            ],[
                'name' => '分类',
                'link' => '/pages/category/view',
                'icon' => Helper::asset_url('/media/images/icon/cate1.png') 
            ],[
                'name' => '金麦大礼包',
                'link' => '/pages/account/vipUpgrade',
                'icon' => Helper::asset_url('/media/images/icon/gift.png') 
            ],[
                'name' => '商学院',
                'link' => '/pages/help/school',
                'icon' => Helper::asset_url('/media/images/icon/sch.png') 
            ],[
                'name' => '同城商家',
                'link' => '/pages/merchant/index',
                'icon' => Helper::asset_url('/media/images/icon/mer.png') 
            ]
        ];
        return $menus;
    }

    /**
     * 首页产品
     * @param  Request $request 
     * @return string           
     */
    public function products(Request $request){
        $is_self = $request->is_self;
        $store_products = ProductDispalyService::getHomeStoreProduct($is_self);
        if($store_products){
            $store_products = $store_products->toArray();
        }
        $result = ['code' => 'Success'];
        if($request->type == 'app'){
            $result['data'] = $store_products;
        } else {
            $view = view('shop.block.products', ['products' => $store_products['data']])->render();
            $result['view'] = $view;
            $result['data'] = [];
        }
       
        return response()->json($result);
    }

    /**
     * 首页产品
     * @param  Request $request 
     * @return string           
     */
    public function selfProducts(Request $request){
        $products = ProductDispalyService::getSelfProduct(100);
        if($products){
            $products = $products->toArray();
        }
        $view = view('shop.block.products', ['products' => $products['data']])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
        $result['data'] = [];
        return response()->json($result);
    }

    /**
     * 首页产品
     * @param  Request $request 
     * @return string           
     */
    public function homescreen(Request $request){
        $home_screen = NoticeCache::notice('home_screen');
        $view = view('site.block.screen', ['home_screen' => $home_screen])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
        $result['data'] = [];
        return response()->json($result);
    }


    /**
     * 首页产品
     * @param  Request $request 
     * @return string           
     */
    public function getViewdProducts(Request $request){
        $goods_ids = $request->goods_ids;
        $goods_ids = explode(',', $goods_ids);
        $products = ProductDispalyService::getViewdProducts($goods_ids);
        $result = ['code' => 'Success'];
        if($request->type == 'app'){
            $result['data'] = $products;
        } else {
            if($request->type == 'list'){
                $view = view('shop.block.products', ['products' => $products])->render();
            } else {
                $view = view('site.block.product_list', ['products' => $products])->render();
            }
            
            $result['view'] = $view;
            $result['data'] = [];
        }
       
        return response()->json($result);
    }

     /**
     * 首页产品
     * @param  Request $request 
     * @return string           
     */
    public function shop(Request $request){
        $pageSize = 50;
        $products = ProductDispalyService::getShareProduct($pageSize);
        $result = ['code' => 'Success'];
        if($request->type == 'app'){
            $products = $products->toArray();
            $result['data'] = $products;
        } else {
            $view = view('shop.block.products', ['products' => $products])->render();
            $result['view'] = $view;
            $result['data'] = [];
        }
       
        return response()->json($result);
    }
}
