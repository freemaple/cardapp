<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
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
                \Session::set('login_redirect_link', \URL::full());
                $link = \Helper::route('auth_login', ['login']);
                return redirect($link);
            }
        }
        return $next($request);
    }
}
