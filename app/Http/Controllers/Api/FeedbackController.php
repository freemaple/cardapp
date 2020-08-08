<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User\Feedback as FeedbackModel;

class FeedbackController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function submit(Request $request)
    {
        $result = ['code' => '2X1', 'message' => ''];

        $user = \Auth::user();

        $user_id = !empty($user) ? $user->id : 0;

        $fullname = $request->fullname;

        $phone = $request->phone;

        $content = $request->content;

        $date = date('Y-m-d');

        $pkey = "feedback_submit";

        if($user_id > 0){
            $count = FeedbackModel::where('user_id', $user_id)
            ->where('created_at', '>', $date)
            ->count();
        } else {
            $feedback_submit = \Cookie::get($pkey, 0);
            if($feedback_submit > 5){
                $result['message'] = '对不起，您反馈太频繁了！';
                return response()->json($result);
            }
            $count = FeedbackModel::where('fullname', $fullname)
            ->where('phone', '=', $phone)
            ->where('created_at', '>', $date)
            ->count();
        }
        if($count > 5){
            $result['message'] = '对不起，您反馈太频繁了！';
            return response()->json($result);
        }
        $FeedbackModel = FeedbackModel::where('fullname', $fullname)
        ->where('user_id', $user_id)
        ->where('phone', '=', $phone)
        ->where('fullname', '=', $fullname)
        ->where('content', '=', $content)
        ->where('status', '=', '0')
        ->first();
        if($FeedbackModel == null){

            $FeedbackModel = new FeedbackModel();

            $FeedbackModel->user_id = $user_id;

            $FeedbackModel->fullname = $fullname;

            $FeedbackModel->phone = $phone;

            $FeedbackModel->content = $content;

            $FeedbackModel->status = '0';

            $FeedbackModel->save();

            $feedback_submit = \Cookie::get($pkey, 0);

            $feedback_submit = $feedback_submit + 1;

            \Cookie::queue($pkey, $feedback_submit, 1440);
        }
        

        $result = ['code' => 'Success', 'message' => '提交成功！'];

        return response()->json($result);
    }
}