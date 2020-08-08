<?php

namespace App\Libs\Service\Email;

use Illuminate\Support\Facades\Mail;

class EmailService
{
	/**
	 * 邮件模板共有变量
	 * @return array
	 */
	protected static function emailCommonData()
	{
		$data['site_url'] = url('/');
		$data['site_name'] = config('site.site_name');
		return $data;
	}
	protected static function send($model = null, $to, $cc = ''){
		if($model == null){
			return false;
		}
		$mail = Mail::to($to);
		if($cc){
			$mail = $mail->cc($cc);
		}
		return $mail->send($model);
	}
	protected static function queue($model = null, $to, $cc = ''){
		if($model == null){
			return false;
		}
		$mail = Mail::to($to);
		if($cc){
			$mail = $mail->cc($cc);
		}
		return $mail->queue($model);
	}
}