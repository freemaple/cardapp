<?php

namespace App\Helper;

use Qcloud\Sms\SmsSingleSender;

class Sms
{   
    /**
     *发送短信
     * @param string $name  route 别名
     * @param array $parameters
     * @param bollean $secure
     * @return string
     */
	public static function sendCode($phone, $code)
    {
    	$result = [];
    	$environment = \App::environment();
    	if($environment == 'local'){
    		$result['status'] = '1';
		    $result['data'] = [];
		    return $result;
    	}
    	try {
    		$appconfig = config('qcloudsms');
		    $ssender = new SmsSingleSender($appconfig['appid'], $appconfig['appkey']);
		    $templateId = '265526';
		    $params = [];
		    $params[] = $code;
		    $re = $ssender->sendWithParam("86", $phone, $templateId, $params);
		    $rsp = (array)json_decode($re);
		    if($rsp && $rsp['result'] == '0'){
		    	$result['status'] = '1';
		    	$result['data'] = $rsp;
		    } else {
		    	$result['status'] = '0';
		    	$result['data'] = $rsp;
		    }
		    return $result;
		} catch(\Exception $e) {
			$result = [];
			$result['status'] = '0';
			$result['message'] = $e->getMessage();
		    return $result;
		}

		try{
			if(!empty($result['message'])){
				\Log::warning('sendCode:' . $result['message']);
			}
		} catch(\Exception $e) {
			
		}
    }

    /**
     *卖家订单发货提醒
     * @param string $name  route 别名
     * @param array $parameters
     * @param bollean $secure
     * @return string
     */
	public static function sendOrderShip($phone)
    {
        \Log::info("sms $phone");
    	$environment = \App::environment();
    	if($environment == 'local'){
    		return false;
    	}
    	try {
    		$appconfig = config('qcloudsms');
		    $ssender = new SmsSingleSender($appconfig['appid'], $appconfig['appkey']);
		    $templateId = '289465';
		    $params = [];
		    $params[] = 24;
		    $re = $ssender->sendWithParam("86", $phone, $templateId, $params);
		    $rsp = (array)json_decode($re);
		    if($rsp && $rsp['result'] == '0'){
		    	$result['status'] = '1';
		    	$result['data'] = $rsp;
                        \Log::info("sms $phone success");
		    } else {
		    	$result['status'] = '0';
		    	$result['data'] = $rsp;
                        \Log::info("sms $phone status0");
		    }
		    return $result;
		} catch(\Exception $e) {
                        \Log::info("sms $phone faild");
			$result = [];
			$result['status'] = '0';
			$result['message'] = $e->getMessage();
		    return $result;
		}
		try{
			if(!empty($result['message'])){
				\Log::warning('sendOrderShip:' . $result['message']);
			}
		} catch(\Exception $e) {
			
		}
    }
}
