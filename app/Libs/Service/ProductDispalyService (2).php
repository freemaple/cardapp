<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Store\Store as StoreModel;
use App\Cache\Product as ProductCache;
use App\Models\Product\Sku as ProductSkuModel;
use App\Models\Product\OptionValue as OptionValueModel;
use App\Models\Product\Attribute as ProductAttributeModel;
use App\Models\Order\Reviews as ReviewsModel;
use App\Models\Product\Wish as ProductWishModel;

class ProductDispalyService
{
    //获取自营产品
    public static function getSelfProduct($pageSize = 0, $limit = 0){
        $products = ProductModel::where('enable', '1')
        ->where('deleted', '!=', '1')
        ->where('is_sale', '1')
        ->where('is_self', '1')
        ->orderBy('created_at', 'desc')
        ->orderBy('sales_numbers', 'desc')
        ->orderBy('rating', 'desc');
        if($pageSize == 0){
            $products = $products->limit($limit)->get();
        } else {
            $products = $products->paginate($pageSize);
        }
        if($products == null){
            return $products;
        }
        $products = static::getProductListItem($products);
        return $products;
    }

    //获取分享产品
    public static function getHomeShareProduct($limit = 0){
        $products = ProductModel::where('enable', '1')
        ->where('deleted', '!=', '1')
        ->where('is_sale', '1')
        ->where('is_shared', '1')
        ->orderBy(\DB::raw('RAND()'))
        ->orderBy('created_at', 'desc');
        $products = $products->limit($limit)->get();
        if($products == null){
            return $products;
        }
        $products = static::getProductListItem($products);
        return $products;
    }

    //获取分享产品
    public static function getShareProduct($pageSize = 0, $limit = 0){
        $products = ProductModel::where('enable', '1')
        ->where('deleted', '!=', '1')
        ->where('is_sale', '1')
        ->where('is_shared', '1')
        ->orderBy('created_at', 'desc')
        ->orderBy('sales_numbers', 'desc')
        ->orderBy('rating', 'desc');
        if($pageSize == 0){
            $products = $products->limit($limit)->get();
        } else {
            $products = $products->paginate($pageSize);
        }
        if($products == null){
            return $products;
        }
        $products = static::getProductListItem($products);
        return $products;
    }

    //获取产品
    public static function findProduct($id){
        $product = ProductModel::where('id', $id)->where('deleted', '0')->where('is_sale', '1')->first();
        if($product == null){
            return $product;
        }
        $images = $product->images()->get();
        if($images != null){
            $images = $images->toArray();
        }
        $main_image = !empty($images) ? $images[0]['image'] : '';
        $main_image = \HelperImage::storagePath($main_image);
        $product['main_image'] = $main_image;
        foreach ($images as $ikey => $i) {
           $images[$ikey]['image'] = \HelperImage::storagePath($i['image']);
        }
        $product['images'] = $images;
        $skus = $product->skus()->where('deleted', '!=', '1')->get();
        if(empty($skus)){
            return null;
        }
        $product['main_sku'] = $skus[0];
        $sku_min_price = $skus[0]['price'];
        $sku_min_market_price = $skus[0]['market_price'];
        $min_share_integral = 0;
        $max_share_integral = 0;
        foreach ($skus as $key => $sku) {
            if($sku['share_integral'] > 0 && $sku['share_integral'] > $max_share_integral){
                $max_share_integral = $sku['share_integral'];
            }
            if($sku['share_integral'] > 0 && $sku['share_integral'] < $min_share_integral || $min_share_integral == 0){
                $min_share_integral = $sku['share_integral'];
            }
            if($sku['price'] < $sku_min_price){
                $sku_min_price = $sku['price'];
                $sku_min_market_price = $sku['market_price'];
                $product['main_sku'] = $sku;
            }
            $attributes = $product->attribute()->select('product_attribute.id', 'product_attribute.option_id', 'product_attribute.option_value_id', 'option.name as option_name','option.description as option_description', 'product_attribute.option_value')->where('product_id', '=', $id)->where('product_sku_id', $sku->id)
            ->join('option', 'option.id', '=', 'product_attribute.option_id')
            ->where('deleted', '!=', '1')
            ->get();
            $attributes = $attributes->toArray();
            $skus[$key]->attributes = $attributes;
            $skus[$key]->image = \HelperImage::storagePath($sku['image']);
        }
        $config = config('order.share_commission');
        $product['self_amount'] = $max_share_integral * $config['self'];
        $product['share_amount'] = $max_share_integral * $config['share'];
        $product['share_amount_min'] = $min_share_integral * $config['share'];
        $product['share_amount_max'] = $product['main_sku']['price'] * 0.1 + $product['min_share_integral'] * 0.2;
        if($product['share_amount_min'] > $product['share_amount_max']){
            $product['share_amount_min'] = $product['share_amount_max'] / 2;
        }
        $product['min_share_integral'] = $min_share_integral;
        $product['max_share_integral'] = $max_share_integral;
        $product['sku_min_price'] = $sku_min_price;
        $product['sku_min_market_price'] = $sku_min_market_price;
        if($product['share_amount_max'] > $product['main_sku']['price']){
            $product['share_amount_max'] = $product['main_sku']['price'] * 0.1;
        }
        if($skus != null){
            $skus = $skus->toArray();
        }
        $product['skus'] = $skus;
        $attributes = $product->attribute()
        ->select('product_attribute.id', 'product_attribute.option_id', 'product_attribute.option_value_id', 'option.name as option_name', 'product_attribute.option_value', 'option.description as option_description')
        ->join('option', 'option.id', '=', 'product_attribute.option_id')
        ->where('product_attribute.deleted', '!=', '1')
        ->get();
        $product_attributes = [];
        foreach ($attributes as $akey => $a) {
            $option_id = $a['option_id'];
            if(!isset($product_attributes[$option_id])){
                $product_attributes[$option_id] = [
                    'option_id' => $a['option_id'],
                    'option_name' => $a['option_name'],
                    'option_description' => $a['option_description'],
                    'attributes' => []
                ];
            }
            $is_flag = true;
            foreach ($product_attributes[$option_id]['attributes'] as $key => $avalue) {
                if($a['option_value'] == $avalue['option_value']){
                    $is_flag = false;
                }
            } 
            if($is_flag){
                $product_attributes[$option_id]['attributes'][] = $a->toArray();
            }
        }
        $product['attribute'] = $product_attributes;
        return $product;
    }

    //产品sku属性
    public static function findProductSkuSpc($sku){
        $attributes = ProductAttributeModel::select('product_attribute.*', 'option.name as option_name', 'option.description as option_description')
        ->join('option', 'option.id', '=', 'product_attribute.option_id')
        ->where('product_sku_id', $sku['id'])
        ->where('product_id', $sku['product_id'])
        ->get();
        $spec = '';
        foreach ($attributes as $akey => $value) {
            $spec .= $value['option_description'] . "：" . $value['option_value'];
            if($akey < count($attributes) - 1){
                $spec .= "，";
            }
        }
        return $spec;
    }

    //获取首页产品
    public static function getHomeStoreProduct($is_self = null){
        $pageSize = 100;
        $products = ProductModel::select('product.*')->where('product.deleted', '!=', '1')
        ->where('product.enable', '1')
        ->where('product.is_sale', '1');

        if($is_self !== null){
            $products->is_self = $is_self;
        }

        if($is_self != '1'){
            $products = $products->leftjoin('store_account', 'store_account.user_id', '=', 'product.user_id');
            $products = $products->whereRaw("(product.is_self = '1' or  (store_account.status = '2' and store_account.expire_date > now()))");
        }
        $products = $products->orderBy('product.sales_numbers', 'desc')
        ->orderBy('product.rating', 'desc')
        ->orderBy('product.wish_number', 'desc')
        ->orderBy('product.view_number', 'desc')
        ->orderBy('product.created_at', 'desc')
        ->paginate($pageSize);
        if($products == null){
            return $products;
        }
        $products = static::getProductListItem($products);
        return $products;
    }

    public static function getProductListItem($products){
        $config = config('order.share_commission');
        foreach ($products as $key => $product) {
            unset($products[$key]['admin_id']);
            $sku = ProductCache::defaultSKU($product);
            $main_image = !empty($sku['image']) ? $sku['image'] : '';
            $main_image = \HelperImage::storagePath($main_image);
            unset($sku['admin_id']);
            if($sku['max_share_integral'] > 0){
                $product['self_amount'] = $config['self'] * $sku['max_share_integral']; 
            }
            if($sku['max_share_integral'] > 0){
                $product['share_amount'] = $config['share'] * $sku['max_share_integral']; 
            }
            if($sku['min_share_integral'] > 0){
                $product['share_amount_min'] = $config['share'] * $sku['min_share_integral'];
                $product['share_amount_max'] = $sku['price'] * 0.1 + $sku['min_share_integral'] * 0.2; 
                if($product['share_amount_max'] < $product['share_amount_min']){
                    $product['share_amount_min'] = $product['share_amount_max'] / 2;
                }
            }
            $product['sku'] = $sku;
           
            $product['image'] = $main_image;
            if($product['is_self'] == '1'){
                $product['store_name'] = '自营';
            } else {
                $store = ProductCache::store($product);
                if(!empty($store)){
                    $product['store_name'] = str_limit($store['name'], 8);
                    $product['store_id'] = $store['id'];
                } else {
                    $product['store_name'] = '自营';
                }
            }
        }
        //dd($products->toArray());
        return $products;
    }

    public static function getSearchProduct($keyword, $sort = null){
        $pagesize = config('paginate.search_product', 100);
        $products = ProductModel::select('product.*')
        ->leftjoin('store_account', 'store_account.user_id', 'product.user_id')
        ->whereRaw("(product.is_self = '1' or  (store_account.status = '2' and store_account.expire_date > now()))")
        ->where('enable', '1')
        ->where('deleted', '!=', '1')
        ->whereRaw("(product.name like '%". sprintf("%s", $keyword). "%' or store_account.name like '%". sprintf("%s", $keyword). "%')");
        if($sort == 'sales_numbers'){
            $products = $products->orderBy('sales_numbers', 'desc');
        } else if($sort == 'created'){
            $products = $products->orderBy('created_at', 'desc');
        } else if($sort == 'rating'){
            $products = $products->orderBy('rating', 'desc');
        } else {
            $products = $products->orderBy('sales_numbers', 'desc');
            $products = $products->orderBy('created_at', 'desc');
        }
        $products = $products->paginate($pagesize);
        $products = static::getProductListItem($products);
        return $products;
    }

    public static function getCategoryProduct($category_id, $sort = null){
        $pagesize = config('paginate.search_product', 100);
        $products = ProductModel::select('product.*')
        ->leftjoin('store_account', 'store_account.user_id', 'product.user_id')
        ->whereRaw("(product.is_self = '1' or  (store_account.status = '2' and store_account.expire_date > now()))")
        ->where('deleted', '!=', '1')
        ->where('enable', '1')
        ->where('is_sale', '1');
        if($category_id > 0){
            $products = $products->where('product.category_id', '=', $category_id);
        }
        if($sort == 'all'){
            $products = $products->orderBy(\DB::raw('product.sales_numbers + product.rating'))
            ->orderBy('product.wish_number', 'desc')
            ->orderBy('product.view_number', 'desc')
            ->orderBy('product.created_at', 'desc');
        }
        else if($sort == 'sales_numbers'){
            $products = $products->orderBy('sales_numbers', 'desc');
        } else if($sort == 'created'){
            $products = $products->orderBy('created_at', 'desc');
        } else if($sort == 'rating'){
            $products = $products->orderBy('rating', 'desc');
        } else {
            $products = $products->orderBy('sales_numbers', 'desc');
            $products = $products->orderBy('created_at', 'desc');
        }
        $products = $products->paginate($pagesize);
        $products = static::getProductListItem($products);
        return $products;
    }

    public static function getProductReview($product_id, $pageSize, $paginate = false){
        $product_reviews = ReviewsModel::select('user.fullname','user.avatar', 'order_reviews.created_at', 'order_reviews.review_text', 'order_product.spec', 'order_product.image')
        ->leftjoin('order_product', 'order_product.id', '=', 'order_reviews.order_product_id')
        ->leftjoin('user', 'user.id', '=', 'order_reviews.user_id')
        ->where('order_reviews.product_id', $product_id);
        if($paginate){
            $product_reviews = $product_reviews->paginate($pageSize);
        } else {
            $product_reviews = $product_reviews->limit($pageSize)->get();
        }
    
        foreach ($product_reviews as $key => $product_review) {
            $image = $product_reviews[$key]['image'];
            $product_reviews[$key]['image'] = \HelperImage::storagePath($image);
            $avatar = $product_review['avatar'];
            $product_reviews[$key]['avatar'] = \HelperImage::getavatar($avatar);
        }
        return $product_reviews;
    }

    public static function getProductReviewCount($product_id){
        $count = ReviewsModel::where('order_reviews.product_id', $product_id)->count();
        return $count;
    }

      //获取自营产品
    public static function getWishProduct($user_id, $pageSize){
        $products = ProductModel::select('product_wish.id as wish_id', 'product.*')
        ->join('product_wish', 'product.id', '=', "product_wish.product_id")
        ->where('product_wish.user_id', $user_id)
        ->where('enable', '1')
        ->where('deleted', '!=', '1')
        ->where('is_sale', '1')
        ->orderBy('product_wish.id', 'desc')
        ->paginate($pageSize);
        if($products == null){
            return $products;
        }
        $products = static::getProductListItem($products);
        return $products;
    }

     //获取自营产品
    public static function getActivityProduct($activity_category_id, $pageSize = 0, $limit = 0){
        $products = ProductModel::select('product.*')->where('enable', '1')
        ->join('activity_category_product', 'activity_category_product.product_id', 'product.id')
        ->where('product.deleted', '!=', '1')
        ->where('activity_category_id', $activity_category_id)
        ->where('product.is_sale', '1')
        ->orderBy(\DB::raw('RAND()'))
        ->orderBy('activity_category_product.created_at', 'desc')
        ->orderBy('activity_category_product.created_at', 'desc')
        ->orderBy('product.sales_numbers', 'desc')
        ->orderBy('product.rating', 'desc');
        if($pageSize == 0){
            $products = $products->limit($limit)->get();
        } else {
            $products = $products->paginate($pageSize);
        }
        if($products == null){
            return $products;
        }
        $products = static::getProductListItem($products);
        return $products;
    }


      //获取自营产品
    public static function getViewdProducts($goods_ids){
        $products = ProductModel::where('enable', '1')
        ->where('deleted', '!=', '1')
        ->where('is_sale', '1')
        ->whereIn('id', $goods_ids);
        $products = $products->limit(50)->get();
        if($products == null){
            return $products;
        }
        $products = static::getProductListItem($products);
        $product_key = [];
        foreach ($products as $key => $product) {
            $product_key[$product['id']] = $product;
        }
        $products_list = [];
        foreach ($goods_ids as $key => $goods_id) {
            if(!empty($product_key[$goods_id])){
                $products_list[] = $product_key[$goods_id];
            }
        }
        return $products_list;
    }
}