<?php
namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
		if(!$request->ajax()){
            return response()->json(['code' => 'invalid', 'message' => 'invalid'], 200);
        }
         //获取session用户
        $this->middleware(function ($request, $next){
            $user = \Auth::user();
            view()->share('session_user', $user);
            return $next($request);
        });
    }
    /**
	 * 监听sql查询语句
	 */
	protected  function sqlDump()
	{
		\DB::listen(function($sql) {
            //参数值
            $bindings = implode($sql->bindings, ',');
            //sql语句
            $str = $sql->sql;
			print_r(sprintf('%s param: [ %s ] <br />', $str, $bindings));
		});
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
                new \Monolog\Handler\StreamHandler(storage_path('logs/sql.log'), \Monolog\Logger::INFO)
            );
            $log->addInfo($str.' param:['.implode($bindings, ',').']');
        });
    }
}
