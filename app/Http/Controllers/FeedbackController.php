<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;

use App\Models\User\Feedback as FeedbackModel;

class FeedbackController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $FeedbackModel = new FeedbackModel();

        $pageSize = 20;

        $form = $request->all();

        $phone = trim($request->phone);

        if($phone != null){
            $FeedbackModel = $FeedbackModel->where('phone', '=', $phone);
        }

        $fullname = trim($request->fullname);

        if($fullname != null){
            $FeedbackModel = $FeedbackModel->where('fullname', '=', $fullname);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $FeedbackModel = $FeedbackModel->where('created_at', '>=', $start_date);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $FeedbackModel = $FeedbackModel->where('created_at', '<=', $end_date);
        }

        $feedbacklist = $FeedbackModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        $feedbacklist->appends($request->all());

        $pager = $feedbacklist->links();

        $view = View('admin.feedback.index');

        $view->with("feedbacklist", $feedbacklist);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "客户反馈");

        return $view;

    }
}