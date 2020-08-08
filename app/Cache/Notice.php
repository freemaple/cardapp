<?php

namespace App\Cache;

use App\Models\Notice\Notice as NoticeModel;

class Notice
{
	public static function notice($location)
	{
		$key = 'notice:' . $location;
		$notice = Base::cacheGet($key);
		if($notice == null){
			$notice = NoticeModel::where('location', $location)->where('enabled', '1')->orderBy('id', 'desc')->first();
			Base::cachePut($key, $notice);
		}
		return $notice;
	}

	public static function clearNoticeCache($location)
	{
		$key = 'notice:' . $location;
		Base::cacheDel($key, true);
	}
}