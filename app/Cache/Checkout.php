<?php

namespace App\Cache;

class Checkout
{
	/**
	 * 支付订单是否支付缓存
	 * @param  string $basket_code 
	 * @return array
	 */
	public static function getBasketCode($basket_code = '')
	{
		$key = 'basket_code:' . $basket_code;
		$cache = Base::cacheGet($key, true);
		return $cache;
	}

	/**
	 * 支付订单是否支付缓存设置
	 * @param  string $basket_code 
	 * @return array
	 */
	public static function setBasketCode($basket_code = '', $data)
	{
		$key = 'basket_code:' . $basket_code;
		Base::cachePut($key, $data, 144440, true);
	}
}