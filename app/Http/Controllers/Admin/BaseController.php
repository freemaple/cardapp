<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
	//登录用户
    protected  $admin_user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	//后台登录验证
		$this->middleware('auth.admin:admin');
		
		//define('CACHE_OPEN', false);

        $this->middleware(function ($request, $next){
            $admin_user = \Auth::guard('admin')->user();
            $this->_after($admin_user);
            if($admin_user != null){
                $session_user = $admin_user->toArray();
                $session_user['allRoles'] = $admin_user->rolesName();
            } else {
                $session_user = null;
            }
            view()->share('admin_user', $session_user);
            return $next($request);
        });
        //$this->sqlLog();
    }
    /**
     * 回调取登录用户变量
     * @param ORM $user
     */
    private function _after($admin_user)
    {
        $this->admin_user = $admin_user;
    }

     /**
     * 记录sql语句
     */
    protected  function sqlLog()
    {
        \DB::listen(function($sql){
            $bindings = $sql->bindings;
            $str = $sql->sql;
            $log = new \Monolog\Logger('sql');
            $log->pushHandler(
                new \Monolog\Handler\StreamHandler(storage_path('logs/admin_sql.log'), \Monolog\Logger::INFO)
            );
            $log->addInfo($str.' param:['.implode($bindings, ',').']');
        });
    }
}
