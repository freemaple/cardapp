<?php

namespace App\Cache;

use App\Models\Theme\Background;

class Theme
{
	public static function backgrounds()
	{
		$key = 'screen_backgrounds';
		$backgrounds = Base::cacheGet($key);
		if($backgrounds == null){
			$backgrounds = Background::where('type', '1')->where('enable', '1')->get();
	        if($backgrounds != null){
	            $backgrounds = $backgrounds->toArray();
	        }
			Base::cachePut($key, $backgrounds, 14400);
		}
		return $backgrounds;
	}

	public static function shareBackgrounds()
	{
		$key = 'share_backgrounds';
		$backgrounds = Base::cacheGet($key);
		if($backgrounds == null){
			$backgrounds = Background::where('type', '2')->where('enable', '1')->get();
	        if($backgrounds != null){
	            $backgrounds = $backgrounds->toArray();
	        }
			Base::cachePut($key, $backgrounds, 14400);
		}
		return $backgrounds;
	}

	public static function clearBackgrounds()
	{
		$key = 'screen_backgrounds';
		Base::cacheDel($key);
	}
}