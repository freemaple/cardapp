<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class HomeController extends BaseController
{

    /**
     * 后台系统首页
     *
     * @return void
     */
    public function index(Request $request)
    {

        $view = View('admin.home.index');
 
        $view->with("title", "首页");

        return $view;
    }
}