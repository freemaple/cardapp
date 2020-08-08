<?php

namespace App\Cache;

use App\Models\User\User as UserModel;
use App\Models\Store\Store as StoreModel;

class User
{

	public static function store($user_id = '')
	{
		$key = 'userstore:' . $user_id;
		$store = Base::cacheGet($key);
		if($store == null){
			$store = StoreModel::where('user_id', '=', $user_id)->first();
			Base::cachePut($key, $store);
		}
		return $store;
	}

	public static function info($user_id = '')
	{
		$key = 'userinfo:' . $user_id;
		$user = Base::cacheGet($key);
		if($user == null){
			$user = UserModel::where('id', $user_id)->first();
			Base::cachePut($key, $user);
		}
		return $user;
	}

	public static function clearCache($user_id = '')
	{
		$key = 'userinfo:' . $user_id;
		$user = Base::cacheGet($key);
		if($user != null){
			Base::cacheDel($key);
		}
	}
}