<?php

namespace App\Cache;

use App\Libs\Service\ProductDispalyService;

use App\Models\Product\ActivityCategory;

use App\Models\Product\Category as CategoryModel;

class Home
{
	/**
	 * 获取推荐产品
	 * @param  string $card_number 
	 * @return array
	 */
	public static function homeSpecialProduct()
	{
		$key = 'homeSpecialProduct';
		$products = Base::cacheGet($key);
		if($products == null){
			$activity_category = ActivityCategory::where('name', 'special_price')->first();
			if($activity_category != null){
				$products = ProductDispalyService::getActivityProduct(1, 0, 4);
				Base::cachePut($key, $products, 20);
			}
		}
		return $products;
	}

	/**
	 * 获取分享产品
	 * @param  string $card_number 
	 * @return array
	 */
	public static function shareProduct()
	{
		$key = 'homeShareProduct';
		$products = Base::cacheGet($key);
		if(empty($products) || count($products) == 0){
			$products = ProductDispalyService::getHomeShareProduct(4);
			Base::cachePut($key, $products, 20);
		}
		return $products;
	}

	/**
	 * 获取分享产品
	 * @param  string $card_number 
	 * @return array
	 */
	public static function clearProductCache()
	{
		$key = 'homeShareProduct';
		Base::cacheDel($key);
		$key = 'homeStoreProduct_page1';
		Base::cacheDel($key);
	}

	public static function clearSpecialProductCache()
	{
		$key = 'homeSpecialProduct';
		$products = Base::cacheGet($key);
		if($products != null){
			Base::cacheDel($key);
		}
		return $products;
	}

	public static function getHomeStoreProduct(){
		$key = 'homeStoreProduct_page1';
		$products = Base::cacheGet($key);
		if(empty($products) || count($products) == 0){
			$products = ProductDispalyService::getHomeStoreProduct();
			Base::cachePut($key, $products, 5);
		}
		return $products;
	}


	/**
	 * 产品一级分类缓存
	 * @return [type] [description]
	 */
	public static function homecategory()
	{
		$key = 'homecategory';
		$categorys = Base::cacheGet($key);
		$categorys = null;
		if(empty($categorys)){
			$categorys = CategoryModel::select()
			->where('is_enable', '1')
			->where('is_deleted', '0')
			->where('pid', '0')
			->orderBy('id', 'asc')
			->get();
			foreach ($categorys  as $key => $category) {
				$categorys[$key]['image'] = \HelperImage::storagePath($category['image']);
			}
			Base::cachePut($key, $categorys);
		}
		return $categorys;
	}
}