<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User\User as UserModel;

class Authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::user();
        if (empty($user)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['code' => 'UNAUTH', 'message' => '页面已过期，请重新登录!'], 200);
            } else {
                if(\Helper::isWeixin()){
                    $wx_user = session('wechat.oauth_user'); 
                    if($wx_user != null){
                        $openid = $wx_user->id;
                        $wx_user = UserModel::where('openid', '=', $openid)->first();
                        if($wx_user != null){
                            //Auth::login($wx_user);
                            //return $next($request);
                        }
                    } 
                }
                \Session::set('login_redirect_link', \URL::full());
                $link = \Helper::route('auth_login', ['login']);
                return redirect($link);
            }
        } 
        
        return $next($request);
    }

    private function isUserInfoComplete($user){
        if(empty($user->email)){
            return false;
        }
        return true;
    }

    public function isWeixin(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
            return true; 
        } else {
            return false; 
        }
    }
}
