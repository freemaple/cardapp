<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roleName = '')
    {
        $user = Auth::guard('admin')->user();
        if ($user == null ) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('admin/auth');
            }
        }
        $user_role = $user->rolesName();
        $roles = explode(';', $roleName);
        $is_role = false;
        if(array_intersect($roles, $user_role)){
            $is_role = true;
        } 
        if($is_role){
            return $next($request);
        } else {
            return redirect()->guest('admin');
        }
    }
}