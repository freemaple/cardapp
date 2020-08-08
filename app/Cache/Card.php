<?php

namespace App\Cache;

use App\Libs\Service\CardService;
use App\Models\Microlink\Microlink;
use App\Models\Card\Card as CardModel;

class Card
{
	/**
	 * 获取名片缓存
	 * @param  string $card_number 
	 * @return array
	 */
	public static function getCard($card_number = '')
	{
		$key = 'card:' . $card_number;
		$card = Base::cacheGet($key);
		$card = null;
		if($card == null){
			$card = CardService::getCardInfo($card_number);
			Base::cachePut($key, $card);
		}
		
		return $card;
	}

	/**
	 * 清除名片缓存
	 * @param  string $card_number 
	 * @return array
	 */
	public static function clearCardCache($card_number = '')
	{
		$key = 'card:' . $card_number;
		$card = Base::cacheDel($key);
	}

	/**
	 * 获取名片链接
	 * @param  [type] $card [description]
	 * @return [type]       [description]
	 */
	public static function getCardMicrolink($card)
	{
		$key = 'card:microlink' . $card['card_number'];
		$card_microlinks = Base::cacheGet($key);
		if($card_microlinks == null){
			$syn_card_id = $card['syn_card_id'];
	        $syn_microlinks = [];
	        if($syn_card_id > 0){
	            $syn_card = CardModel::where('syn_card_id', '=', $syn_card_id)->first();
	            if($syn_card != null){
	                $syn_microlinks = CardService::getCardMicrolink($syn_card);
	                if($syn_microlinks != null){
	                    $syn_microlinks = $syn_microlinks->toArray();
	                }
	            }
	        }
	        $microlinks = CardService::getCardMicrolink($card);
	        if($microlinks != null){
	            $microlinks = $microlinks->toArray();
	        }
	        $card_microlinks = array_merge($syn_microlinks, $microlinks);
			Base::cachePut($key, $card_microlinks);
		}
		return $card_microlinks;
	}
}