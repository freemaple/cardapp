<?php

namespace App\Cache;

use App\Libs\Service\ProductDispalyService;

use App\Libs\Service\ProductCategoryService;

use App\Models\Store\Store;

class Product
{
	public static function defaultSKU($product)
	{
		$key = 'defaultSKU:' . $product['id'];
		$sku = Base::cacheGet($key);
		$sku = null;
		if($sku == null){
			$max_share_integral = 0;
			$min_share_integral = 0;
			$skus = $product->skus()->orderBy('price', 'asc')->where('deleted', '!=', '1')->get();
			foreach ($skus as $skey => $sk) {
				if($sk['share_integral'] > 0){
					if($sk['share_integral'] > $max_share_integral){
						$max_share_integral = $sk['share_integral'];
					}
					if($sk['share_integral'] < $min_share_integral || $min_share_integral == 0){
						$min_share_integral = $sk['share_integral'];
					}
				}
			}
            if(count($skus) > 0){
                $sku = $skus[0]->toArray();
                $sku['max_share_integral'] = $max_share_integral;
                $sku['min_share_integral'] = $min_share_integral;
            }
			Base::cachePut($key, $sku);
		}
		return $sku;
	}


	public static function store($product)
	{
		$key = 'store:' . $product['id'];
		$store = Base::cacheGet($key);
		if(empty($store)){
			$store = Store::where('user_id', $product['user_id'])->first();
	        if(!empty($store)){
	        	$store = $store->toArray();
	            Base::cachePut($key, $store);
	        }
		}
		return $store;
	}

	public static function clearDefaultSKUCache($product_id = '')
	{
		$key = 'defaultSKU:' . $product_id;
		$sku = Base::cacheGet($key);
		if($sku != null){
			Base::cacheDel($key);
		}
	}


	public static function productView($product_id = '')
	{
		$key = 'productView:' . $product_id;
		$goods_detail = Base::cacheGet($key);
		if($goods_detail == null){
			$goods_detail = ProductDispalyService::findProduct($product_id);
			Base::cachePut($key, $goods_detail, 60);
		}
		return $goods_detail;
	}

	public static function clearProductViewCache($product_id = '')
	{
		$key = 'productView:' . $product_id;
		Base::cacheDel($key);
	}

	public static function clearProductCache($product_id = ''){
		static::clearDefaultSKUCache($product_id);
		static::clearProductViewCache($product_id);
		Home::clearProductCache();
	}
}