<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\UserService;
use Auth;

class RewardController extends BaseController
{
	
    /**
     * 赏金转为有赏积分
     * @param  Request $request 
     * @return string           
     */
    public function toIntegral(Request $request){
        $result = [];
    	$user = Auth::user();
        $transaction_password = $request->transaction_password;
        //检查当前密码是否正确
        if(!\Hash::check($transaction_password, $user->transaction_password)){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起,当前交易密码错误';
            return $result;
        }
        $Reward = $user->Reward()->first();
        if($Reward == null){
            $result['message'] = '对不起，您已没有任何赏金！';
            $result['code']  = 'no_reward';
            return response()->json($result);
        }
        $reward_amount = $request->reward_amount;
        if($reward_amount <=0){
            $result['message'] = '对不起,金额必须大于0';
            $result['code']  = 'no_reward';
            return response()->json($result);
        }
        $amount = $Reward->amount;
        $freeze_amount = $Reward->freeze_amount;
        if($freeze_amount <0){
            $freeze_amount = 0;
        }
        $enabe_amount = $amount - $freeze_amount; 
        if($enabe_amount <= 0){
            $result['message'] = '对不起，您已没有任何可转换赏金！';
            $result['code']  = 'no_reward';
            return response()->json($result);
        }  
        if($reward_amount > $enabe_amount){
            $result['message'] = "对不起，可转换赏金:￥" . $enabe_amount;
            $result['code']  = 'no_reward';
            return response()->json($result);
        }
        if($reward_amount >= $enabe_amount){
            $reward_amount = $enabe_amount;
        }
        $content = "赏金转入 ￥" . $reward_amount;
        //登录事务处理
        $return_result = \DB::transaction(function() use ($user, $amount, $reward_amount, $content, $Reward) {
            UserService::getInstance()->userIntegralIncome($user, $reward_amount, $content);
            $reward_content = '转为有赏积分 减少￥' . $reward_amount;
            UserService::getInstance()->userRewardOut($user, $reward_amount, $reward_content);
        });
        $result['code'] = 'Success';
        $result['message'] = '转换成功！';
    	return response()->json($result);
    }

     /**
     * 赏金记录
     *
     * @return void
    */
    public function index(Request $request)
    {
        $result = ['code' => '2x1', 'data' => []];

        $data = [];

        $user = Auth::user();

        $reward_amount = 0;
        $freeze_amount = 0;
        //赏金
        $reward = $user->reward()->first();
        if($reward != null){
           $reward = $reward->toArray();
           $reward_amount = $reward['amount'];
           $freeze_amount = $reward['freeze_amount'];
        }

        $data['reward_amount'] = $reward_amount;

        $data['freeze_amount'] = $freeze_amount;

        $data['available_amount'] = $reward_amount - $freeze_amount;

        $result = ['code' => 'Success', 'data' => $data];

        return response()->json($result);

    }

     /**
     * 赏金记录
     *
     * @return void
    */
    public function record(Request $request)
    {
        $user = Auth::user();

        $pageSize = 50;

        $rewardRecord = $user->rewardRecord()->orderBy('user_reward_record.id','desc')->paginate($pageSize);

        $rewardRecord = $rewardRecord->toArray();

        if($request->type == 'app'){
            $data = [];
            $data['rewardRecord'] = $rewardRecord;
            $result = ['code' => 'Success', 'data' => $data];
        } else {
            $view = view('account.reward.block.list')->with('recordRecords', $rewardRecord['data'])->render();
            $result = ['code' => 'Success', 'view' => $view, 'data' => $rewardRecord['data']];
        }
        return response()->json($result);

    }
}
