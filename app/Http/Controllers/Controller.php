<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        //前端资源版本
        \View::share('asset_version', config('asset.version'));
        $this->init();
        $this->sqlLog();
    }

    protected function init()
    {
        
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
                new \Monolog\Handler\StreamHandler(storage_path('logs/sql/site.log'), \Monolog\Logger::INFO)
            );
            $log->addInfo($str.' param:['.implode($bindings, ',').']');
        });
    }
}
