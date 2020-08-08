<?php

namespace App\Cache;

use App\Libs\Service\DocService;

use App\Models\Doc\Doc as DocModel;
class Help
{
	public static function get($id)
	{
		$key = 'help:' . $id;
		$doc = Base::cacheGet($key);
		if($doc == null){
			$doc = DocModel::where('enable', '1');
			if(is_numeric($id)){
				$doc = $doc->where('id', $id);
			} else {
				$doc = $doc->where('url', $id);
			}
			$doc = $doc->first();
			if($doc != null){
				$doc = $doc->toArray();
			}
			Base::cachePut($key, $doc);
		}
		return $doc;
	}

	/**
	 * 清除缓存

	 * @return array
	 */
	public static function clearHelpCache($id = '')
	{
		$key = 'help:' . $id;
		$card = Base::cacheDel($key);
	}

	/**
	 * 帮助页目录
	 * @return array
	 */
	public static function getDocCatalog()
	{
		$key = 'docCatalog';
		$doc_catalog = Base::cacheGet($key);
		if($doc_catalog == null){
			$doc_service = DocService::getInstance(); 
        	$doc_catalog = $doc_service->getDocCatalog();
			Base::cachePut($key, $doc_catalog);
		}
		return $doc_catalog;
	}

}