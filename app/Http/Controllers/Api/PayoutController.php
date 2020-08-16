<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\PayoutService;
use Auth;
use App\Models\Payout\Apply as PayoutApplyModel;

use App\Models\User\Reward;

class PayoutController extends BaseController
{


      /**
     * 提现申请
     *
     * @return void
    */
    public function info(Request $request)
    {
        $user = Auth::user();

        $result = ['code' => 'Success', 'message' => ''];

        $reward = $user->reward()->first();

        if(empty($reward)){
           $reward = new  Reward();
           $reward->user_id = $user->id;
           $reward->amount = 0;
           $reward->freeze_amount = 0;
           $reward->save();
        }

        $freeze_amount = $reward->freeze_amount;

        if($freeze_amount < 0){
            $freeze_amount = 0;
        }

        $enable_amount = $reward->amount - $freeze_amount;

        $info = [
            'phone' => $user->phone,
            'nickname' => $user->nickname,
            'amount' => $reward->amount,
            'freeze_amount' => $freeze_amount,
            'enable_amount' => $user->enable_amount
        ];

        $result['data']['info'] = $info;

        return response()->json($result);
    }
	

     /**
     * 提现申请
     *
     * @return void
    */
    public function apply(Request $request)
    {
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'amount' => 'required|int|min:0',
            'fullname' => 'required'
        ]);

        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }

        //事务处理
        $result = PayoutService::apply($request);
        return response()->json($result);
    }

     /**
     * 提现记录
     *
     * @return void
    */
    public function applyList(Request $request)
    { 
        $user = Auth::user();

        $pageSize = config('paginate.payout', 100);

        $payoutApplys = PayoutApplyModel::where('user_id', '=', $user->id)->orderBy('id', 'desc')->paginate($pageSize);

        $payout_status = config('payout.status');

        foreach ($payoutApplys as $key => $value) {
            $payoutApplys[$key]['status_text'] = $payout_status[$payoutApplys[$key]['status']];
            $payoutApplys[$key]['amount_text'] = \HelperCurrency::fixed($payoutApplys[$key]['amount']);
            $payoutApplys[$key]['actual_amount_text'] = \HelperCurrency::fixed($payoutApplys[$key]['actual_amount']);

        }

        if($request->type == 'app'){
            $result = ['code' => 'Success', 'data' => $payoutApplys];
        } else {
            $view = view('account.payout.block.list', [
                'payoutApplys' => $payoutApplys,
                'payout_status' => $payout_status
            ])
            ->render();
            $result = ['code' => 'Success', 'view' => $view];
        }

        return response()->json($result);
    }
}
