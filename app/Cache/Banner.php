<?php

namespace App\Cache;

use App\Models\Banner\Banner as BannerModel;
class Banner
{
	public static function home()
	{
		$key = 'homebanner';
		$banners = Base::cacheGet($key);
		if(empty($banners)){
			$banners = BannerModel::select('id', 'image', 'url', 'alt')->where('location', 'home')->where('enable', '1')->limit(10)->get();
			Base::cachePut($key, $banners);
		}
		foreach ($banners as $key => $banner) {
           $banners[$key]['image'] = \HelperImage::storagePath($banner['image']);
        }
		return $banners;
	}

	public static function clearHome()
	{
		$key = 'homebanner';
		Base::cacheDel($key, true);
	}
}