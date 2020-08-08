<?php
namespace App\Block;

class Admin
{
	/**
	 * 后台网站公共头部块
	 */
	public static function header() {
		$view = view('admin.template.header');
		//获取登录信息
		$user = \Auth::guard('admin')->User();
		$view->with("user", $user);
		return $view;
	}
}