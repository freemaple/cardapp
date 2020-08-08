<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

class ContactController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $view = View('contact.index');

        $view->with("title", "留言");

        return $view;

    }
}