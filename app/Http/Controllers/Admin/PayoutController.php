<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;

use App\Models\User\User as UserModel;

use App\Models\Payout\Apply as PayoutApply;

use App\Libs\Service\PayoutService;

use App\Libs\Service\MessageService;

class PayoutController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function apply(Request $request)
    {
        $PayoutApply = new PayoutApply();

        $pageSize = 20;

        $form = $request->all();

        $fullname = trim($request->fullname);

        if($fullname != null){
            $PayoutApply = $PayoutApply->where('fullname', '=', $fullname);
        }

        $status = trim($request->status);

        if($status != null){
            $PayoutApply = $PayoutApply->where('status', '=', $status);
        }

        $payoutApplys = $PayoutApply->orderBy('id', 'desc')
        ->paginate($pageSize);

        $payoutApplys->appends($request->all());

        $pager = $payoutApplys->links();

        foreach ($payoutApplys as $key => $payoutApply) {
            $payoutApply->userinfo = UserModel::where('id', '=', $payoutApply->user_id)->first();
        }

        $statistics = [];

        $payout_count = PayoutApply::count();

        $statistics['payout_count'] = $payout_count;

        $payout_amount = PayoutApply::sum('amount');

        $statistics['payout_amount'] = $payout_amount;

        $payout_actual_amount = PayoutApply::sum('actual_amount');

        $statistics['payout_actual_amount'] = $payout_actual_amount;


        $processing_count = PayoutApply::where('status', '1')->count();

        $statistics['processing_count'] = $processing_count;


        $processing_amount = PayoutApply::where('status', '1')->sum('amount');

        $statistics['processing_amount'] = $processing_amount;

        $processing_actual_amount = PayoutApply::where('status', '1')->sum('actual_amount');

        $statistics['processing_actual_amount'] = $processing_actual_amount;

        $processed_count = PayoutApply::where('status', '2')->count();

        $statistics['processed_count'] = $processed_count;

        $processed_actual_amount = PayoutApply::where('status', '2')->sum('actual_amount');

        $statistics['processed_actual_amount'] = $processed_actual_amount;

        $processed_amount = PayoutApply::where('status', '2')->sum('amount');

        $statistics['processed_amount'] = $processed_amount;

        $level_status = config('user.level_status');

        $payout_status = config('payout.status');

        $view = View('admin.payout.index');

        $view->with("payoutApplys", $payoutApplys);

        $view->with("level_status", $level_status);

        $view->with("payout_status", $payout_status);

        $view->with("payout_status", $payout_status);

        $view->with("payout_status", $payout_status);

        $view->with("statistics", $statistics);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "提现申请");

        return $view;

    }

    /**
     * 用户列表
     *
     * @return void
    */
    public function handerApply(Request $request)
    {
        $payout_apply_id = $request->id;
        $payout_apply = PayoutApply::where('id', $payout_apply_id)->first();
        if($payout_apply == null){
           $result['code'] = '2x1';
            $result['message'] = '提现记录不存在';
            return response()->json($result);
        }
        $approval = $request->approval;
        $remarks = trim($request->remarks);
        $admin_user = $this->admin_user;
        if($approval == 0){
            if($payout_apply->status == 0){
                $result['code'] = '200';
                $result['message'] = '已处理，无需重复操作！';
                return response()->json($result);
            }
            //事务处理
            $res = \DB::transaction(function() use ($payout_apply, $remarks, $admin_user) {
                $payout_apply->status = '0';
                $payout_apply->remarks = $remarks;
                $payout_apply->refused_time = date('Y-m-d H:i:s');
                $payout_apply->approval_admin_id = $admin_user->id;
                $payout_apply->save();
                $user = UserModel::where('id', $payout_apply['user_id'])->first();
                if($user == null){
                    return;
                }
                $reward = $user->reward()->first();
                if($reward == null){
                    return;
                }
                $amount = $payout_apply['amount'];
                $freeze_amount = $reward->freeze_amount - $amount;
                if($freeze_amount > $reward->amount){
                   $freeze_amount =  $reward->amount;
                }
                if($freeze_amount <=0){
                    $freeze_amount = 0;
                }
                $reward->freeze_amount = $freeze_amount;
                $reward->save();
                $data = [
                    'user_id' => $user->id,
                    'name' => "您的提现申请已被拒绝",
                    'content' => "您的提现申请已被拒绝，原因:" . $remarks,
                    'link' => '/account/payout/index'
                ];
                MessageService::insert($data);
            });
        } else if($approval == 1){
            if($payout_apply->status == '2'){
                $result['code'] = '200';
                $result['message'] = '已处理，无需重复操作！';
                return response()->json($result);
            }
            PayoutService::approvalApply($payout_apply, $admin_user);
        }
        $result['code'] = '200';
        $result['message'] = '审批成功！';
        return response()->json($result);
    }
}