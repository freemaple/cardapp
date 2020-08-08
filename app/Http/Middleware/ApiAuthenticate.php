<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $mkey = $request->header('MKEY');
        if(!$request->ajax()){
            if($mkey == null){
                //return response()->json(['code' => 'invalid', 'message' => 'invalid'], 200);
            } else {
                $path = $request->getPathInfo();
                $res = $this->md5key1($path, $mkey);
                if(!$res){
                    //return response()->json(['code' => 'invalid', 'message' => 'invalid'], 200);
                }
            }
        }
        return $next($request);
    }

    public function md5key($path, $mkey){
        $app_id = config('api.app_id');
        $app_key = config('api.api_key');
        $m = md5($app_id . '_' .  $app_key . '_' . $path);
        if($mkey == $m){
            return true;
        }
        return false;
    }


    public function md5key1($path, $mkey){
        $app_id = config('api.app_id');
        $app_key = config('api.api_key');
        //$m = md5($app_id . '_' .  $app_key . '_' . $path);
        $m = $app_key;
        if($mkey == $m){
            return true;
        }
        return false;
    }
}