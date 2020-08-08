<?php

namespace App\Cache;

use App\Libs\Service\PositionService;

class Position
{
	public static function provices()
	{
		$key = 'provices';
		$provices = Base::cacheGet($key);
		if($provices == null){
			$provices = PositionService::provices();
			Base::cachePut($key, $provices, 1400000);
		}
		return $provices;
	}
}