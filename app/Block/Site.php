<?php
namespace App\Block;
use Auth;
use Helper;

class Site
{
	/**
	 * 前台网站公共头部块
	 */
	public static function header($user = null) {
		//控制器名称
		$Controller = '';
		//获取当前路由
		$route_current = \Route::current();
		//判断当前路由是否为空和getActionName方法
		if(!empty($route_current) && method_exists($route_current, 'getActionName')){
			//获取当前控制器名称
			$action = $route_current->getActionName(); 
    		list($Controller, $method) = explode('@', $action); 
    		$Controller = class_basename($Controller);
		}
		if(Helper::isMobile()){
			$view = view('app.template.header');
		} else {
			$view = view('front.template.header');
		}
		
		$nickname = '';
		if($user == null){
			$user = Auth::user();
		}
		$message_number = 0;
		if($user != null ){
			$nickname = $user->nickname;
			if(strlen($nickname) > 13){
				$nickname = substr($nickname, 0, 13) . "..";
			}
			$message_number = $user->message()->where("is_read", '=', '0')->count();
		}
		$view->message_number = $message_number;
		$view->nickname = $nickname;
		return $view;
	}

	/**
	 * 前台网站公共头部块
	 */
	public static function footer() {
		if(Helper::isMobile()){
			$view = view('app.template.footer');
		} else {
			$view = view('front.template.footer');
		}
		return $view;
	}

	/**
	 * 前台网站公共头部块
	 */
	public static function teacherHeader() {
		$teacher = Auth::guard('organization_teacher')->user();
		$view = view('front.teacher.block.header');
		$view->with('teacher', $teacher);
		return $view;
	}
}