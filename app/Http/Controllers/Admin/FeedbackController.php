<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;
use App\Models\User\Feedback;

use App\Models\User\User;

class FeedbackController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $FeedbackModel = new Feedback();

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


        $status = trim($request->status);

        if($status !== ''){
            $FeedbackModel = $FeedbackModel->where('status', '=', $status);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $FeedbackModel = $FeedbackModel->where('created_at', '>=', $start_date);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $FeedbackModel = $FeedbackModel->where('created_at', '<=', $end_date);
        }

        $feedbacks = $FeedbackModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        $feedbacks->appends($request->all());

        $pager = $feedbacks->links();

        $view = View('admin.feedback.index');

        $view->with("feedbacks", $feedbacks);


        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "用户");

        return $view;

    }

     /**
     * 加入文库
     *
     * @return void
    */
    public function hander(Request $request)
    {
        $result = [];

        $feedback_id = $request->feedback_id;

        $feedback = Feedback::where('id', '=', $feedback_id)->first();

        if(empty($feedback)){
            $result['code'] = '2x1';
            $result['message'] = '反馈不存在';
            return response()->json($result);
        }
        if($feedback['status'] == '1'){
            $result['code'] = '2x1';
            $result['message'] = '已标记处理，无需重复操作！';
            return response()->json($result);
        }
        $feedback['status'] = '1';
        $feedback['hander_time'] = date('Y-m-d H:i:s');
        $feedback->save();
        $result['code'] = '200';
        $result['message'] = '操作成功！';
        return response()->json($result);
    }
}