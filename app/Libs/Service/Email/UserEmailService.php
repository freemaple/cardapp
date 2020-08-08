<?php

namespace App\Libs\Service\Email;

use App\Mail\UserRegister;
use App\Mail\UserReset;
use App\Mail\UserActive;

class UserEmailService extends EmailService
{
	/**
	 * 发送注册成功邮件
	 * @param $username
	 * @param string $to
	 */
	public static function userRegister($username, $to, $register_password = '', $active_link = '')
	{
		try{
			$data = self::emailCommonData();
			$data['username'] = $username;
			$data['register_password'] = $register_password;
			$data['active_link'] = $active_link;
			$message = (new UserRegister($data));
			self::send($message, $to);
			return true;
		} catch (\Exception $e){
			return false;
		}
	}
	
	
	/**
	 * [忘记密码邮件]
	 * @param  [type] $token     [token]
	 * @param  [type] $username [用户名]
	 * @param  [type] $to       [发送人邮箱]
	 * @return [bool]
	 */
	public static function userReset($link, $username, $to)
	{
		try{
			$data = self::emailCommonData();
			$data['username'] = $username;
			$data['link'] = $link;
			$message = (new UserReset($data));
			self::send($message, $to);
			return true;
		}catch (\Exception $e){
			return false;
		}
	}

	/**
	 * [用户激活邮件]
	 * @param  [type] $active_link 链接
	 * @param  [type] $username [用户名]
	 * @param  [type] $to       [发送人邮箱]
	 * @return [bool]
	 */
	public static function userActive($active_link, $username, $to)
	{
		try{
			$data = self::emailCommonData();
			$data['username'] = $username;
			$data['active_link'] = $active_link;
			$message = (new UserActive($data))->onQueue('emails');
			self::send($message, $to);
			return true;
		}catch (\Exception $e){
			return false;
		}
	}
}