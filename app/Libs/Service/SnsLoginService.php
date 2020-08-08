<?php
namespace App\Libs\Service;

use Socialite;
use App\Repository\User as UserRepository;
use App\Repository\UserSnsLogin as SnsUserRepository;
use DB;
use Auth;
use App\Libs\Service\Email\UserEmailService;

class SnsLoginService
{

	/**
     * @var Singleton reference to singleton instance
     */
	private static $_instance;  

	
	/**
     * 构造函数私有，不允许在外部实例化
     *
    */
	private function __construct(){}

	/**
     * 防止对象实例被克隆
     *
     * @return void
    */
	private function __clone() {}
	
	/**
	 * Create a new Repository instance.单例模式
	 *
	 * @return void
	 */
    public static function getInstance()    
    {    
        if(! (self::$_instance instanceof self) ) {    
            self::$_instance = new self();   
        }
        return self::$_instance;    
    }  

	/**
	 * [sns登录重定向]
	 * @param  [string] $sns [fackbook/google]
	 * @return [redirect]      
	 */
	public static function redirect($sns)
	{
		return Socialite::with($sns)->redirect();
	}

	/**
	 * [获取sns登录后回调用户数据]
	 * @param  [string] $sns [fackbook/google]
	 * @return [array]      
	 */
	public static function user($sns)
	{
		return Socialite::with($sns)->user();
	}

	/**
	 * [sns登录]
	 * @param  [string] $sns [fackbook/google]
	 * @return [array]      
	 */
	public static function snsLogin($sns, $user_type = '0')
	{
		//获取sns登录数据
		$sns_user = static::user($sns);
		//dd($sns_user);
		if(empty($sns_user)){
			return false;
		}
		return static::login($sns_user, $sns, $user_type);
	}

	/**
	 * [sns登录操作]
	 * @param  [string] $sns [fackbook/google]
	 * @return [array]      
	 */
	public static function login($sns_user, $sns, $user_type = '0')
	{
		$where = [["sns", '=', $sns], ['snsid', '=', $sns_user->id]];
		$sns_info = SnsUserRepository::getInstance()->find($where, []);
		if($sns_info != null){
			$user_id = $sns_info->user_id;
			$user = UserRepository::getInstance()->find($user_id);
			if(!empty($user)){
				Auth::login($user);
				//登录成功,更新登录信息
	        	$update_data = [
	        		'lastlogin' => time(),
	        		'login_times' => $user->login_times + 1
	        	];
	        	UserRepository::getInstance()->updateUser($update_data, [['id', '=', $user_id]]);
				return true;
			}
		} else {
			return self::register($sns_user, $sns, $user_type);
		} 
	}

	/**
	 * [sns注册操作]
	 * @param  [string] $sns [fackbook/google]
	 * @return [array]      
	 */
	public static function register($sns_user, $sns, $user_type = '0')
	{
		$result = DB::transaction(function() use ($sns_user, $sns, $user_type){
			$password = str_random(6);
			$auth_service =  AuthService::getInstance();
			$nickname =  $sns_user->nickname;
			$email =  '';
			$user_model = $auth_service->generateRegister($email, $password, $user_type, $nickname);
			if($user_model != null){
				$data = [
					'user_id' => $user_model->id,
					'sns' => $sns,
					'register_date' => time(), 
					'snsid' => $sns_user->id
				];
				$sns_login_model = SnsUserRepository::getInstance()->insert($data);
				if($sns_login_model != null){
					//自动登录
					Auth::login($user_model);
					//登录成功,更新登录信息
		        	$update_data = [
		        		'lastlogin' => time(),
		        		'login_times' => $sns_login_model->login_times + 1
		        	];
		        	UserRepository::getInstance()->updateUser($update_data, [['id', '=', $user_model->id]]);
					return true;
				} else {
					//业务逻辑出错,手动回滚
                    DB::rollback();
                    return false;
				}
			}
			return false;
		});
		return $result;
	}
}