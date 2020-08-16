<?php
namespace App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Auth;
use App;
use EasyWeChat\Foundation\Application;

class BaseController extends Controller
{
    //是否开启证书
	protected  $_CFG_SECURE;
	//站点名称
	protected $_CFG_SITE_NAME;
	//登录用户
	protected  $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
    	//获取站点配置
    	$site_config = config('site');
    	
        //站点环境
        $environment = App::environment();
        //静态资源地址
        $static_path = isset($site_config['static_path'][$environment]) ? $site_config['static_path'][$environment] : '';
    	//版本
    	$version = $site_config['version'];
    	//站点名称
    	$this->_CFG_SITE_NAME = $site_config['site_name'];
    	//是否开启证书
    	$this->_CFG_SECURE = $site_config['secure'];
    	//获取请求链接是否是https
    	$protocol = ($request->secure()) ? true : false;
    	define('SITE_NAME', $this->_CFG_SITE_NAME);
    	//获取session用户
    	$this->middleware(function ($request, $next){
            $server = \Request::server();
            if($request->pagetype){
                $pagetype = $request->pagetype; 
                \Session::set('pagetype', $request->pagetype);
            } else {
                $pagetype = \Session::get('pagetype');
            }
            if($pagetype == 'webview'){
                view()->share('plus_webview', true);
            } else {
                view()->share('plus_webview', false);
            }
    		$user = Auth::user();
    		view()->share('session_user', $user);
    		$this->_after($user);
            $environment = \App::environment();
            if(\Helper::isWeixin() && $environment != 'local'){
                $wx_user = session('wechat.oauth_user');
                if(empty($wx_user)){
                    \Session::set('wx_oauth_back', \URL::full());
                    return redirect(\Helper::route('wx_oauth_auth'));
                }
            }
    		return $next($request);
    	});
    	define('LANGUAGE', App::getLocale());
    	//赋值到视图全局变量
    	view()->share('secure', $protocol);
    	view()->share('version', $version);
        view()->share('static_path', $static_path);
        view()->share('site_config', $site_config);
	}
	/**
	 * 回调取用户变量
	 * @param ORM $user
	 */
	private function _after($user)
	{
		$this->user = $user;
	}

    public function isWeixin(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
            return true; 
        } else {
            return false; 
        }
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
                new \Monolog\Handler\StreamHandler(storage_path('logs/console/sql.log'), \Monolog\Logger::INFO)
            );
            $log->addInfo($str.' param:['.implode($bindings, ',').']');
        });
    }
}
