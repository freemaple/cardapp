<?php

namespace App\Cache;

use App\Libs\Service\ProductCategoryService;

class ProductCategory
{
	public static function getTopCategory($post_number = '')
	{
		$key = 'top_category';
		$categorys = Base::cacheGet($key);
		if(empty($categorys)){
			$field = ['id', 'name', 'pid'];
			$categorys = ProductCategoryService::getInstance()->getTopCategoryList($field);
			Base::cachePut($key, $categorys);
		}
		return $categorys;
	}

	public static function clearTopCategoryCache($post_number = '')
	{
		$key = 'top_category';
		$categorys = Base::cacheGet($key);
		if($categorys != null){
			Base::cacheDel($key);
		}
	}
}