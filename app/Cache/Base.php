<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class Base
{
	/**
	 * 获取缓存数据
	 * @param string $key
	 * @param boolean $cache_must_open 缓存必须开启
	 */
	public static function cacheGet($key, $cache_must_open = false)
	{
		try{
			if($cache_must_open || self::_cacheOpen()){
				return Cache::get($key, null);
			}else{
				return null;
			}
		} catch(\Exceptio $e){
			return null;
		}
	}
	/**
	 * 
	 * @param string $key
	 * @param array $data
	 * @param int $time
	 * @param boolean $cache_must_open 缓存必须开启
	 */
	public static function cachePut($key, $data, $time = null, $cache_must_open = false)
	{
		try{
			if($cache_must_open || self::_cacheOpen()){
			//缓存过期时间为空时，取配置过期时间
			if($time == null){
				$time = config('cache.expire_time');
			}
			return Cache::put($key, $data, $time);
			}else{
				return null;
			}
		} catch(\Exceptio $e){
			return null;
		}
		
	}
	/**
	 * 删除缓存
	 * @param string $key
	 */
	public static function cacheDel($key){
		try{
			Cache::forget($key);
		} catch(\Exceptio $e){
			return null;
		}
	}
	/**
	 * 
	 * @param string $key 缓存key名称
	 * @param array $param 缓存参数，一维数组
	 * @return string
	 */
	public static function cacheKey($key, $param= [])
	{
		$param_key = '';
		if(!empty($param)){
			while(list($k,$v) = each($param)){
				if(is_array($v)){
					$v = serialize($v);
				}
				$param_key.= '_'.$k.'_'.$v;
			}
			$param_key = md5($param_key);
		}
		$cache_key = strtoupper(config('site.site_name').':'.$key.':'.$param_key);
		return trim($cache_key);
	}
	/**
	 * 获取缓存开关
	 * @return boolean
	 */
	private static function _cacheOpen()
	{
		//判断是否定义有缓存开关，默认是缓存开启
		if(defined('CACHE_OPEN')){
			return CACHE_OPEN;
		}else{
			return true;
		}
	}
}