<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

class FeedbackController extends BaseController
{

    /**
     * 反馈
     *
     * @return void
    */
    public function index(Request $request)
    {
        $view = View('contact.index');

        $view->with("title", "建议反馈");

        return $view;

    }
}