<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use App\Models\User\User as UserModel;

/**
 * 本地化相关处理
 * 主要是语言和币种处理
 */
class Localization
{

    public function handle($request, Closure $next)
    {

        $url = url()->full();
        if(strpos($url,'xmanage.renrenyoushang.com') !==false && strpos(\Request::url(), '/admin') ==false){
            $url = str_replace('xmanage.renrenyoushang.com', 'www.renrenyoushang.com', $url);
            $url = str_replace('http', 'https', $url);
            return redirect($url);
        }

        if(strpos(\Request::url(),'xmanage.renrenyoushang.com') !==false || strpos(\Request::url(), '/admin') !==false){
            return $next($request);
        }

        //rid       
        $rid = $request->rid;

        if (!empty($rid)) {
            $c_rid = Cookie::get('rid', '');
            if($rid != $c_rid){
                Cookie::queue('rid', $rid, 525600);
            }
        }
        //微信授权登录
        if(\Helper::isWeixin()){
            $user = \Auth::user();
            $wx_user = session('wechat.oauth_user');
            if(!empty($user)){
                if($user['openid'] == null){
                    if(!empty($wx_user)){
                        $openid = $wx_user->id;
                        $u_user = UserModel::where('openid', '=', $openid)->first();
                        if($u_user == null){
                            $user->openid = $openid;
                            $is_save = false;
                            if($user->nickname == null){
                                $user->nickname = $wx_user->nickname;
                                $is_save = true;
                            }
                            if($user->avatar == null){
                                $user->avatar = $wx_user->avatar;
                                $is_save = true;
                            }
                            if($is_save){
                                $user->save();
                            }
                        }
                    }
                }
            }
        }

        $user = \Auth::User();

        if(!empty($user) && $user['is_vip'] == '1'){
            $c_rid = Cookie::get('rid', '');
            if($c_rid != $user->id){
                Cookie::queue('rid', $user->id, 525600);
            }
        }

        if(!empty($user) && $user->session_id != ''){
            $session_id = $request->session()->getId();

            $ip = \Helper::getIPAddress();

            /*if($session_id != $user->session_id){
                $is_ip = false;
                if(strpos($ip,'183.13') !== false || strpos($ip,'192.168.1') !== false){
                    $is_ip = true;
                }
                if($user->lastlogin_ip && $user->lastlogin_ip != $ip && !$is_ip){
                    session(['wechat.oauth_user' => null]);
                    \Auth::logout();
                    $link = \Helper::route('auth_login', ['login']);
                    return redirect($link)->with('message', '帐号异地登录!被迫下线!<br /> 如未您本人操作，请及时修改密码！');
                }
            }*/
        }

        return $next($request);
    }

}
