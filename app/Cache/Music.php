<?php

namespace App\Cache;

use App\Models\Music\Music as MusicdModel;

class Music
{
	/**
	 * 音乐缓存
	 * @param  string $card_number 
	 * @return array
	 */
	public static function getMusic()
	{
		$key = 'musics';
		$musics = Base::cacheGet($key);
		if($musics == null){
			$musics = MusicdModel::where('enable', '=', '1')->get();
			Base::cachePut($key, $musics);
		}
		
		return $musics;
	}
}