<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Helper;
use Illuminate\Routing\Controller;
use App\Libs\Service\AuthService;
use App\Libs\Service\SnsLoginService;
use App\Models\User\RegisteredRecord;
use App\Models\User\User as UserModel;
use App\Models\User\PhoneCode as PhoneCodeModel;
use App\Helper\Sms as HelperSms;

class AuthController extends BaseController
{

    /**
     * 登录操作
     *
     * @return string
     */
    public function login(Request $request)
    {
        //请求数据
        $phone = trim($request->phone);
        $password = trim($request->password);
        $password = Helper::str_decrypt($password);
        if(intval($phone) && strlen($phone) == 11){
            $data = ['phone' => $phone, 'password' => $password];
        } else {
            $data = ['user_name' => $phone, 'password' => $password];
        }
        
        //登录操作
        $result = AuthService::getInstance()->login($data, $request);
        return response()->json($result);
    }

    /**
     * 注册操作
     *
     * @return string
     */
    public function register(Request $request)
    {
        //请求数据
        $user_name = trim($request->user_name);
        if(strlen($user_name) <6){
            $result['code'] = "2x1";
            $result['message'] = "用户名至少6位";
            return response()->json($result);
        }
        if(is_numeric($user_name)){
            $result['code'] = "2x1";
            $result['message'] = "用户名必须包含字母";
            return response()->json($result);
        }
        if(!preg_match("/^[a-zA-Z0-9_]*$/i",$user_name)){
            $result['code'] = "2x1";
            $result['message'] = "用户名只能包含字母和数字";
            return response()->json($result);
        }
        $phone = trim($request->phone);
        $fullname = trim($request->fullname);
        $password = trim($request->password);
        $password = Helper::str_decrypt($password);
        $transaction_password = trim($request->t_password);
        $rf = trim($request->rid);
        $verification_code = trim($request->verification_code);
        $data = ['user_name' => $user_name,'phone' => $phone, 'fullname' => $fullname, 'password' => $password, 'transaction_password' => $transaction_password, 'verification_code' => $verification_code, 'rf' => $rf];
        //用户注册
        $result = AuthService::getInstance()->register($data, $request);
        return response()->json($result);
    }

    /**
     * 忘记密码操作
     *
     * @return string
     */
    public function forget_password(Request $request)
    {
        $result['code'] = 'error';
        $result['message'] = '暂未开通！请等待！';
        //请求数据
        $email = Helper::inputStr(trim($request->email));
        //忘记密码操作
        $result = AuthService::getInstance()->forget_password($email);
        return response()->json($result);
    }

    /**
     * 重置密码操作
     *
     * @return string/view
     */
    public function reset(Request $request)
    {
        $result = [];
        //phone
        $user_name = trim($request->user_name);
        $phone = trim($request->phone);
        $phone_user = UserModel::where('phone', $phone)->first();
        if($phone_user == null){
            $result['code'] = 'phone_no_exits';
            $result['message'] = '手机号码不存在！';
            return response()->json($result);
        }
        $user = UserModel::where('user_name', $user_name)
        ->where('phone', $phone)
        ->first();
        if(empty($user)){
            $result['code'] = 'phone_no_exits';
            $result['message'] = '用户名不存在！';
            return response()->json($result);
        }
        //code
        $code = trim($request->code);
        $PhoneCodeModel = PhoneCodeModel::where('phone', $phone)->where('code', $code)
        ->where('type', 'password')
        ->first();
        if($PhoneCodeModel == null){
            $result['code'] = 'code_error';
            $result['message'] = '验证码错误！';
            return response()->json($result);
        }
        
        if($PhoneCodeModel['is_use'] == '1'){
            $result['code'] = 'code_error';
            $result['message'] = '验证码已使用！';
            return response()->json($result);
        }

        $created_at = $PhoneCodeModel['created_at'];

        $new_date = date('Y-m-d H:i:s', strtotime("-30minute", time()));

        if($new_date > $created_at){
            $result['code'] = 'code_error';
            $result['message'] = '验证码已过期！';
            return $result;
        }

        $PhoneCodeModel->is_use = '1';
        $PhoneCodeModel->save();
        //确认密码
        $new_password = trim($request->new_password);

        $user->password = bcrypt($new_password);

        $user->save();

        $result['code'] = 'Success';

        $result['message'] = '密码重置成功！';
       
        return response()->json($result);
    }

    /**
     * 重置密码操作
     *
     * @return string/view
     */
    public function phoneCode(Request $request)
    {
        $result = [];
        $type = trim($request->type);
        if(!in_array($type, ['sign_up', 'password', 'transaction_password', 'payout'])){
            $result['code'] = 'Success';
            $result['message'] = '对不起,不允许的操作！';
            return response()->json($result);
        }
        //phone
        $phone = trim($request->phone);
        //数据校验
        $validator = Validator::make($request->all(), [
            'phone' => 'required|phone|max:20'
        ]);

        //校验失败
        if($validator->fails()){
            $result['code'] = "Invalid_Parameter";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        
        $user = UserModel::where('phone', $phone)->orderBy('id', 'desc')->first();
        if(in_array($type, ['sign_up'])){
            if($user != null && $user->is_vip != 1){
                $result['code'] = 'phone_no_exits';
                $result['message'] = '对不起,此手机帐号已存在，请直接登录！';
                return response()->json($result);
            }
        } else {
            if($user == null){
                $result['code'] = 'phone_no_exits';
                $result['message'] = '对不起,系统未存在此手机帐号！';
                return response()->json($result);
            }
        }
       
        $ip = \Helper::getIPAddress();
        $time = time();
        if(in_array($type, ['password', 'transaction_password'])){
            $startOfMonth = date('Y-m-01',$time);
            $endOfMonth = date('Y-m-t 23:59:59',$time);
            $code_count = PhoneCodeModel::where('phone', '=', $phone)->where('type', $type)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
            if($code_count >=3){
                $result['code'] = 'Error';
                $result['message'] = '对不起,您已经超过每月使用次数3次！';
                return response()->json($result);
            }
        }

        if(in_array($type, ['sign_up'])){
            $startOfDate = date('Y-m-d 00:00:00');
            $endOfDate = date('Y-m-d 23:59:59');
            $code_count = PhoneCodeModel::where('phone', '=', $phone)->where('type', $type)
            ->whereBetween('created_at', [$startOfDate, $endOfDate])
            ->where('is_use', '0')
            ->count();
            if($code_count >= 100){
                $result['code'] = 'Error';
                $result['message'] = '对不起,您发送验证码太过频繁！';
                return response()->json($result);
            }
            $code_count = PhoneCodeModel::where('ip', '=', $ip)->where('type', $type)
            ->whereBetween('created_at', [$startOfDate, $endOfDate])
            ->where('is_use', '0')
            ->count();
            if($code_count >= 100){
                $result['code'] = 'Error';
                $result['message'] = '对不起,您发送验证码太过频繁！';
                return response()->json($result);
            }
        }

        $phone_code = $this->getCode();
        $PhoneCodeModel = new PhoneCodeModel();
        $PhoneCodeModel->phone = $phone;
        $PhoneCodeModel->type = $type;
        $PhoneCodeModel->user_id = !empty($user) ? $user->id: 0;
        $PhoneCodeModel->code = $phone_code;
        $PhoneCodeModel->is_use = '0';
        $PhoneCodeModel->ip = Helper::getIPAddress();
        $r = $PhoneCodeModel->save();
        if($r){
            $res = HelperSms::sendCode($phone, $phone_code);
            if($res['status'] == '1'){
                try{
                    $PhoneCodeModel->errmsg = $res['data']['errmsg'];
                    $PhoneCodeModel->ext = $res['data']['ext'];
                    $PhoneCodeModel->fee = $res['data']['fee'];
                    $PhoneCodeModel->sid = $res['data']['sid'];
                    $PhoneCodeModel->save();
                } catch(\Exception $e){} 
                $result['code'] = 'Success';
                $result['message'] = '验证码已发送到我王的手机,请查收！';
                return response()->json($result);
            } else {
                \DB::rollback();
                $result['code'] = 'Faild';
                $result['message'] = '验证码发送失败';
                return response()->json($result);
            }
        }
    }

    /**
     * 生成验证码
     * @return [type] [description]
     */
    public function getCode(){
        $str = '';
        for($i=1;$i<=6;$i++){
          $str.='' . rand(0,9);
        }
        return $str;
    }

      /**
     * 修改头像
     * @param  Request $request 
     * @return string
     */
    public function logout(Request $request){
        $result = ['code' => 'Success'];
        \Cookie::queue(\Cookie::forget('rid'));
        session(['wechat.oauth_user' => null]);
        \Auth::logout();
        return response()->json($result);
    }
}