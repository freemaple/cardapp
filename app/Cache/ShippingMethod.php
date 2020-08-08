<?php

namespace App\Cache;

use App\Models\Shipping\ShippingMethod as ShippingMethodModel;

class ShippingMethod
{
	public static function get()
	{
		$key = 'shipping_method:';
		$shipping_method = Base::cacheGet($key);
		if($shipping_method == null){
			$shipping_method = ShippingMethodModel::where('status', '1')->get();
			Base::cachePut($key, $shipping_method);
		}
		return $shipping_method;
	}

	public static function clearCache($post_number = '')
	{
		$key = 'shipping_method:';
		$shipping_method = Base::cacheGet($key);
		if($shipping_method != null){
			Base::cacheDel($key);
		}
	}
}