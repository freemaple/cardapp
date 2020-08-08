<?php
namespace App\Libs\Service;

use Auth;
use Validator;
use Helper;
use App\Repository\User as UserRepository;
use App\Libs\Service\Email\UserEmailService;
use Illuminate\Support\Str;
use DB;
use App\Models\User\User as UserModel;
use App\Models\User\Wallet as UserWalletModel;
use App\Models\User\PhoneCode as PhoneCodeModel;
use App\Models\User\ReferrerLevel as ReferrerLevelModel;

class AuthService
{

	private static $UserRepository;  

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
        static::$UserRepository = UserRepository::getInstance();
        return self::$_instance;    
    }  

	/**
	 * [用户登录]
	 * @param  [type] $email    [邮箱]
	 * @param  [type] $password [密码]
	 * @return [string] 
	 */
	public function login($data, $request = null)
	{
		$result = ['code' => '2x1'];
		
		//数据校验
        $validator = Validator::make($data, [
			//'phone' => 'required',
			'password' => 'required'
        ]);

        //数据校验失败
        if($validator->fails()){
			$result['code'] = "Invalid_Parameter";
			$result['message'] = implode("<br />", $validator->errors()->all());
			return $result;
        }

        $data['enable'] = 1;

        //登录事务处理
        $return_result = DB::transaction(function() use ($data, $request) {
        	//登录校验
	        if(Auth::attempt($data)){
	        	$user = Auth::user();
	        	$session_id = $request->session()->getId();
	        	$lastlogin_ip = $user->lastlogin_ip;
	        	$u_ip = \Helper::getIPAddress();
	        	//登录成功,更新登录信息
	        	$update_data = [
	        		'lastlogin' => time(),
	        		'login_times' => $user->login_times + 1
	        	];
	        	$user->lastlogin = time();
	        	$user->lastlogin_ip = $u_ip;
	        	$user->session_id = $session_id;
	        	$user->login_times = $user->login_times + 1;
	        	$user->save();
				return true;
	        }else{
	          	return false;
	        }
        });
        if($return_result){
        	$user = Auth::user();
        	if($user != null && $user['is_vip']){
        		\Cookie::queue('rid', $user['id'], 525600);
        	}
        	$result['data'] = $this->respondWithToken(md5($user->id), [
        		'user_info' => [
        			'fullname' => $user['fullname'],
	        		'nickname' => $user['nickname'],
	        		 'avatar' => \HelperImage::getavatar($user['avatar'])
        		]
        	]);
        	$result['message'] = "Success";
        	$result['code'] = "Success";
        }else{
        	$result['code'] = "User_Not_Exist";
          	$result['message'] = "对不起，用户名或者密码错误！";
        }
        return $result;
	}

	/**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $data)
    {
        return [
            'access_token' => $token,
            //'token_type' => 'bearer',
            //'expires_in' => \Auth::guard()->factory()->getTTL() * 60,
            'user_info' => $data['user_info']
        ];
    }

	/**
	 * [用户注册]
	 * @param  [type] $email    [邮箱]
	 * @param  [type] $password [密码]
	 * @return [string] 
	 */
	public function register($data, $request = null)
	{
		$result = [];
		
		//数据校验
        $validator = Validator::make($data, [
        	'user_name' => 'required|max:20',
        	'fullname' => 'required|max:8',
			'phone' => 'required|phone|max:20',
			'password' => 'required|min:6|max:50',
			//'transaction_password' => 'required|min:6|max:50',
			'verification_code' => 'required'
        ]);

        //校验失败
        if($validator->fails()){
			$result['code'] = "Invalid_Parameter";
			$result['message'] = implode("<br />", $validator->errors()->all());
			return $result;
        }

        //检查姓名是否存在
        $fullname_user = static::$UserRepository->findUser([['fullname', '=', $data['fullname']]]);
		if($fullname_user != null){
			$result['code'] = "2x3";
          	$result['message'] = "此姓名已被使用！";
          	return $result;
		}

        //检查用户名是否存在
        $user_name_user = static::$UserRepository->findUser([['user_name', '=', $data['user_name']]]);
		if($user_name_user != null){
			$result['code'] = "2x3";
          	$result['message'] = "此用户名已被注册！";
          	return $result;
		}

        //检查phone是否存在
        $phone_user = static::$UserRepository->findUser([['user_name', '=', $data['user_name']], ['phone', '=', $data['phone']]]);
		if($phone_user != null){
			$result['code'] = "2x3";
          	$result['message'] = "此用户名和手机号码组合已被注册！";
          	return $result;
		}

		//检查phone是否存在
        $phone_user = UserModel::where('phone', '=', $data['phone'])->where('is_vip', '=', 0)->count();
		if($phone_user > 0){
			$result['code'] = "2x3";
          	$result['message'] = "此手机号码已被注册！";
          	return $result;
		}

		 //code
        $verification_code = $data['verification_code'];

        $environment = \App::environment();

        if($environment == 'local' && $verification_code == env('test_phone_code')){

        	$PhoneCodeModel = null;

        } else {
        	$PhoneCodeModel = PhoneCodeModel::where('phone', $data['phone'])
	        ->where('code', $verification_code)
	        ->where('type', 'sign_up')
	        ->first();

	        if($PhoneCodeModel == null){
	            $result['code'] = 'code_error';
	            $result['message'] = '验证码错误！';
	            return $result;
	        }

	        if($PhoneCodeModel['is_use'] == '1'){
	            $result['code'] = 'code_error';
	            $result['message'] = '验证码已使用！';
	            return $result;
	        }
        }

        

		//注册事务操作
		$return_result = DB::transaction(function() use ($data, $request, $PhoneCodeModel) {
			//注册操作
			$user_model = $this->generateRegister($data);
			if($user_model != null){
				//注册后自动登录
				$user = Auth::login($user_model);
				if(!empty($PhoneCodeModel)){
					$PhoneCodeModel->is_use = '1';
					$PhoneCodeModel->save();
				}
			}
			return $user_model;
		});

		if($return_result != null){
			$user = Auth::user();
			$result['data'] = $this->respondWithToken(md5($user->id), [
        		'user_info' => [
        			'fullname' => $user['fullname'],
	        		'nickname' => $user['nickname'],
	        		 'avatar' => \HelperImage::getavatar($user['avatar'])
        		]
        	]);
			$result['code'] = "Success";
        	$result['message'] = "注册成功！";
		}else{
			$result['code'] = "System_Error";
        	$result['message'] = "system error";
		}
        return $result;
	}

	/**
	 * [生成注册数据]
	 * @param  [type] $email    [邮箱]
	 * @param  [type] $password [密码]
	 * @return [用户model]           
	 */
	public function generateRegister($data){
		//用户ip地址
		$user_ip = Helper::getIPAddress();
		if(empty($data['rf'])){ 
			$rf = \Cookie::get('rid', '');
		} else {
			$rf = $data['rf'];
		}
		//解密
        if(!is_numeric($rf)){
        	$rf = Helper::passport_decrypt($rf, 'rid');
        }
        $avatar = '';
        if(Helper::isWeixin()){
        	$wx_user = session('wechat.oauth_user');
        	if($wx_user != null){
        		$avatar = $wx_user->avatar;
        	}
        }
        $u_id = $this->generateNumber();
        $fullname = isset($data['fullname']) ? $data['fullname'] : '';
		//注册数据
		$insert_data = [
			'user_name' => isset($data['user_name']) ? $data['user_name'] : '',
			'phone' => isset($data['phone']) ? $data['phone'] : '',
			'u_id' => $u_id,
			'openid' => isset($data['openid']) ? $data['openid'] : '',
			'fullname' => $fullname,
			'nickname' => isset($data['nickname']) ? $data['nickname'] : $fullname,
			'password' => bcrypt($data['password']),
			//'transaction_password' => bcrypt($data['transaction_password']),
			'lastlogin' => time(), 
			'signup_ip' => $user_ip,
			'avatar' =>  $avatar 
		];
		$r = null;
		$parent_ids = '';
        if($rf != null){
            $r = UserModel::find($rf);
            if($r != null && $r['is_vip'] == '1'){
                $insert_data['referrer_user_id'] = $rf;
                $insert_data['second_referrer_user_id'] = $r->referrer_user_id;
                $source_referrer_user_id = 0;
                if($r->source_referrer_user_id > 0){
                	$source_referrer_user_id = $r->source_referrer_user_id;
                } else {
                	$source_referrer_user_id = $rf;
                }
                $insert_data['source_referrer_user_id'] = $source_referrer_user_id;
                $rf_user_referrer = ReferrerLevelModel::where('user_id', $r->id)->first();
	            if($rf_user_referrer != null){
	            	if($rf_user_referrer->parent_ids != ''){
	            		$parent_ids = ',' . $r->id . $rf_user_referrer->parent_ids;
	            	} else {
	            		$parent_ids = ',' . $r->id . ',';
	            	}
	            } else {
	            	$parent_ids = ',' . $r->id. ',';
	            }
            }
        }
		//插入注册数据
		$user_model = static::$UserRepository->insertUser($insert_data);
		if($r != null && $r['is_vip'] == '1'){
			UserService::getInstance()->userRegisterNotice($user_model, $r);
		}
		if($user_model != null){
			if($r != null && $r['is_vip'] == '1'){
				if($parent_ids != ''){
					$user_referrer = new ReferrerLevelModel();
					$user_referrer->user_id = $user_model->id;
					$user_referrer->parent_id = $r->id;
					$user_referrer->parent_ids = $parent_ids;
					$user_referrer->save();
				}
				if(!$r['user_type'] || $r['user_type'] == 'general'){
					$s_save = false;
					if($r['director_id'] > 0){
	            		$user_model->director_id = $r['director_id'];
	            		$s_save = true;
	            	}
	            	if($r['manager_id'] > 0){
	            		$user_model->manager_id = $r['manager_id'];
	            		$s_save = true;
	            	}
	            	if($s_save){
	            		$user_model->save();
	            	}
				}
				else if($r['user_type'] == 'director'){
	            	$user_model->director_id = $r->id;
	            	$user_model->save();
	            }
	            else if($r['user_type'] == 'manager'){
	            	$user_model->manager_id = $r->id;
	            	if($r['director_id'] > 0){
	            		$user_model->director_id = $r['director_id'];
	            	}
	            	$user_model->save();
	            }
	            $equity_value = 2;
	            if($r['store_level'] > 0){
	            	$equity_value = 10;
	            }
	            $data = [
                    "order_recharge_id" => 0,
                    "fan_equity_value" => $equity_value,
                    "content" => '邀请普通会员注册赠送',
                    "remark" => '邀请普通会员注册赠送',
                    "type"   => 'register'
                ];
                EquityService::addFanEquity($r, $data);
                $sub_integral = config('user.user_ref_sub_integral_amount');
                $r->sub_integral_amount = $r->sub_integral_amount + $sub_integral;
	            $r->save();
	            UserService::getInstance()->userSubIntegralRecord($r, [
                    "type" => '1',
                    "amount" => $sub_integral,
                    "content" => '邀请粉丝赠送代购积分',
                    "remarks" => '邀请粉丝赠送代购积分',
                    'order_recharge_id' => 0
                ]);
			}
			
		}
		return $user_model;
	}

	/**
     * 生成编号
     */
    public function generateNumber(){
    	$time_str = date('ymd');
        $str = uniqid($time_str);
        return $str;
    }

	/**
	 * [忘记密码]
	 * @param  [type] $email    [邮箱]
	 * @param  [type] $password [密码]
	 * @return [string] 
	 */
	public function forget_password($email)
	{
		$result = [];
		$data = ['email' => $email];

		//数据校验
        $validator = Validator::make($data, [
			'email' => 'required|email|max:50',
        ]);

        //校验失败
        if($validator->fails()){
			$result['code'] = "Invalid_Parameter";
			$result['message'] = implode("<br />", $validator->errors()->all());
			return json_encode($result);
        }

        //检查邮箱是否存在
        $user = static::$UserRepository->findByEmail($email);
		if($user == null){
			$result['code'] = "2x2";
          	$result['message'] = "We can't find a user with that e-mail address.";
          	return $result;
		} 

		$reset_model = $this->generateUserReset($user);
		if($reset_model != null){
			//生成忘记密码链接
			$link = Helper::route("auth_reset", ['id' => $reset_model['token']]);
			//发送忘记密码邮件
			UserEmailService::userReset($link, $email, $email); 
			$result['code'] = "200";
        	$result['message'] = "An email containing a link that will allow you to change your password has been sent to you. ";
		}else{
			$result['code'] = "2x3";
        	$result['message'] = "system error";
		}
        return $result;
	}


	/**
	 * [生成重置密码数据]
	 * @param  [type] $user [用户model]
	 * @return [model]       [密码重置model]
	 */
	public function generateUserReset($user){
		if($user == null || !isset($user->id) || !isset($user->email)){
			return false;
		}
		//生成token
		$hashKey = $user->email . time();
		$token = hash_hmac('sha256', Str::random(40), $hashKey);
		//有效期一天
		$expiration = time() + 86400;

		//密码重置数据
		$data = [
			'user_id' => $user->id,
			'email' => $user->email,
			'expiration' => $expiration, 
			'token' => $token
		];
		//插入密码重置数据
		$insert_model = static::$UserRepository->insertUserReset($data);
		return $insert_model;
	}

	/**
	 * [密码重置]
	 * @param  [type] $email    [邮箱]
	 * @param  [type] $password [密码]
	 * @param  [type] $confirm_password [确认密码]
	 * @return [string] 
	 */
	public function reset($token, $password, $confirm_password)
	{
		$result = [];
		$data = ['token' => $token, 'password' => $password, 'confirm_password' => $confirm_password];

        //数据校验
        $validator = Validator::make($data, [
			'token' => 'required',
			'password' => 'required|min:6|max:50',
			'confirm_password' => 'required|same:password'
        ]);

        //校验失败
        if($validator->fails()){
			$result['code'] = "Invalid_Parameter";
			$result['message'] = implode("<br />", $validator->errors()->all());
			return $result;
      	}
	
		//检查token是否存在
        $token_data = $this->getResetToken($token);
        if($token_data == null){
			$result['code'] = "0x00x2";
          	$result['message'] = "We can't find a user with this token.";
          	return $result;
		} 

		//有效期校验
		$expiration = $token_data->expiration;
		if($expiration<time()){
			$result['code'] = "0x00x3";
          	$result['message'] = "Your reset link was expired. Please try again.";
          	return $result;
		}

		//修改密码
    	static::$UserRepository->updateUser(['password' => bcrypt($password)], [['id', '=', $token_data->user_id]]);
    	//重置密码成功，删除token
    	static::$UserRepository->daleteUserReset([['token', '=', $token]]);

    	$result['code'] = "0x0000";
		$result['message'] = "Your password has been modified successfully.";
		return $result;
	}
	
	/**
	 * [根据token获取reset数据]
	 * @param  string $token 
	 * @return [array]       
	 */
	public function getResetToken($token = ''){
		if($token == null){
			return null;
		}
		//检查token是否存在
        $result = static::$UserRepository->findUserReset([['token', '=', $token]]);
        return $result;
	}
}