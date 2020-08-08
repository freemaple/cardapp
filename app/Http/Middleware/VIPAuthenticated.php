<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Support\Facades\Auth;

class VIPAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();
        if(!empty($user)){
            if(!$user['is_vip']){
                return redirect(\Helper::route('account_vipUpgrade'));
            }
            $vip_end_date = $user->vip_end_date;
            $date = date('Y-m-d H:m:s');
            if($user['is_vip'] && $date > $vip_end_date){
                return redirect(\Helper::route('account_vipUpgrade', ['vip_type' => 'renewal']));
            }
        }
        return $next($request);
    }
}
