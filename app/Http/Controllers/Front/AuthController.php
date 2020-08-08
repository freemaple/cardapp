<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use Auth;
use Validator;
use Session;
use Helper;
use Illuminate\Routing\Controller;
use App\Libs\Service\AuthService;
use App\Libs\Service\SnsLoginService;
use App\Models\User\User as UserModel;


class AuthController extends BaseController
{

    /**
     * 前台用户登录页面
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type)
    {
        if($type == 'login'){
            if(Auth::check()){
                return redirect(Helper::route('account_index'));
            }
        }

        //推荐人id
        $rid = $request->rid;

        $s_type = $request->s_type;

        $login_redirect_link = Session::get('login_redirect_link', '');

        if(empty($login_redirect_link)){
            $login_redirect_link = \URL::previous();
        }
        
        if($login_redirect_link != null){
            Session::set('login_redirect_link', $login_redirect_link);
        }
        if($login_redirect_link == null){
            $login_redirect_link = Session::get('login_redirect_link', '');
        }

        $is_checkout = $request->is_checkout;

        if($is_checkout == '1'){
            $login_redirect_link = Session::get('checkout_redirect_link');
        }

        if($is_checkout == '1'){
            $register_redirect_link = $login_redirect_link;
        } else {
            if($request->is_share == '1'){
                $register_redirect_link = route('account_vipUpgrade');
            } else {
                $register_redirect_link = route('account_entry');
            }
        }

        if($rid == null){
            $rid = \Cookie::get('rid', '');
        } else {
            \Cookie::queue('rid', $rid, 525600);
        }

        $r_user = null;

        $r_user_name = '';

        if($rid != null){
            //解密
            if(!is_numeric($rid)){
                $rid = \Helper::passport_decrypt($rid, 'rid');
            }
            $r_user = UserModel::find($rid);
            if($r_user != null && $r_user['is_vip'] == '1'){
                $r_user_name = $r_user['fullname'];
            } 
        }
        
        $view = view('auth.index', [
            'title' => '登录',
            'type' => $type,
            's_type' => $s_type,
            'rid' => $rid,
            'r_user' => $r_user,
            'r_user_name' => $r_user_name,
            'login_redirect_link' => $login_redirect_link,
            'register_redirect_link' => $register_redirect_link
        ]);
        return $view;
    }

    /**
     * 重置页面
     *
     * @return string/view
     */
    public function reset(Request $request, $token)
    {
        //token有效性
        $is_valid = true;
        //检查token是否存在
        $token_data = AuthService::getInstance()->getResetToken($token);
        if($token_data == null){
            $is_valid = false;
        }else{
            //有效期校验
            $expiration = $token_data->expiration;
            //token不存在或者已经过期
            if($expiration<time()){
                $is_valid = false;
            }
        }
        return view('auth.reset', ['token' => $token, 'is_valid' => $is_valid]);
    }

    /**
     * 注销操作
     *
     * @return string
     */
    public function logout(Request $request)
    {
        \Cookie::queue(\Cookie::forget('rid'));
        session(['wechat.oauth_user' => null]);
        \Auth::logout();
        return redirect(route('auth_login', ['login']));
    }
}