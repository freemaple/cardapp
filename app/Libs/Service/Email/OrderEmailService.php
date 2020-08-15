<?php

namespace App\Libs\Service\Email;

use App\Mail\OrderPaid;
use App\Libs\Service\OrderService;

class OrderEmailService extends EmailService
{
	/**
	 * 订单支付成功邮件模板
	 * 
	 * @param string $order_number
	 * @param string $to
	 */
	public static function orderSelfPaid($order_data, $to = '')
	{
		try{
			$to = config('email.email_1');
			$cc = config('email.email_2');
			$data = self::emailCommonData();
			$data['order'] = $order_data;
			$message = (new OrderPaid($data));
			self::send($message, $to, $cc);
			return true;
		} catch (\Exception $e){
			\Log::info($e->getMessage());
			return false;
		}
	}

}