<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Store\Store as StoreModel;
use App\Models\User\IntegralSend;
use App\Models\Site\Config as SiteConfig;

class StoreService
{

    //获取名片
    public static function getStoreProduct($store, $enable = '0', $off_product_id = 0, $pagesize = null){
        $products = $store->products();
        if($enable == '1'){
            $products =  $products->where('product.is_sale', '1')
            ->where('product.enable', '=', '1');
        }
        if($off_product_id > 0){
            $products->where('product.id', '!=', $off_product_id);
        }
        $products = $products->where('product.deleted', '!=', '1')
        ->orderBy('enable', 'desc')
        ->orderBy('id', 'desc');
        if($pagesize != null){
            $products = $products->paginate($pagesize);
        } else {
            $products = $products->get();
        }
        foreach ($products as $key => $product) {
            $stock = 0;
            $product_sku = $product->skus()->where('deleted', '!=', '1')->get();
            foreach ($product_sku as $key => $sku) {
                $stock += $sku['stock'];
            }
            $product->stock = $stock;
        }
        $products = ProductDispalyService::getProductListItem($products);
        return $products;
    }
    public static function createDefaultStore($user){
        $user_id = $user->id;
        $store = StoreModel::where('user_id', $user_id)->first();
        if($store == null){
            $store = new StoreModel();
            $store->user_id = $user_id;
            $store->name = '我的店铺';
            $store->save();
        }
    }
    public static function createVIPDefaultStore($user){
        $user_id = $user->id;
        $store = StoreModel::where('user_id', $user_id)->first();
        if($store == null){
            $store = new StoreModel();
            $store->user_id = $user_id;
            $store->name = '我的店铺';
            $store->save();
        }
    }
    public static function paymentStore($user){
        $is_open = false;
        $user_id = $user->id;
        $store = StoreModel::where('user_id', $user_id)->first();
        if($store == null){
            $store = new StoreModel();
            $store->user_id = $user_id;
            $store->name = '我的店铺';
            $is_open = true;
        }
        else if($store->is_pay != '1'){
            $is_open = true;
        }
        $next_time = strtotime(date("Y-m-d", strtotime("+1 day")));
        $new_expire_time = strtotime('+1year', $next_time);
        $store->expire_date = date('Y-m-d H:i:s', $new_expire_time);
        $store->open_time = date("Y-m-d H:i:s");
        $store->is_pay = '1';
        $store->store_status = '1';
        $store->save();
        return [
            "is_open" => $is_open,
            "store" => $store
        ];
    }

    public static function autoOpenStore($user){
        if($user->store_level < 1){
            $user->store_level = '1';
            $user->is_auto_open_store = '1';
            $user->save();
        }
        return static::paymentStore($user);
    }

     //店铺有效性
    public static function isVisable($store){
        if($store['status'] != '2'){
            return false;
        }
        $expire_date = strtotime($store['expire_date']);
        $now = time();
        if($expire_date < $now){
            return false;
        }
        return true;
    }
}