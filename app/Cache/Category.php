<?php

namespace App\Cache;

use App\Models\Post\Category as CategoryModel;

class Category
{
	/**
	 * 产品一级分类缓存
	 * @return [type] [description]
	 */
	public static function allCategory()
	{
		$key = 'allcategory';
		$categorys = Base::cacheGet($key);
		if(empty($categorys)){
			$categorys = CategoryModel::select()->where('enable', '1')
			->where('pid', '0')
			->orderBy('sort', 'asc')
			->get();
			Base::cachePut($key, $categorys);
		}
		return $categorys;
	}
}