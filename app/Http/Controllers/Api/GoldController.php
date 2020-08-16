<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\UserService;
use Auth;
use App\Models\User\User as UserModel;
use App\Models\Gold\Config as GoldConfigModel;
use App\Models\User\GoldDay as UserGoldDayModel;

class GoldController extends BaseController
{

     /**
     * 金麦
     *
     * @return \Illuminate\Http\Response
     */
    public function info()
    {
        $user = Auth::user();

        $data = UserService::getInstance()->getGoldData();

        $result['code'] = 'Success';
        $result['message'] = '';
        $result['data'] = [
            'user_info' => $user,
            'info' => $data,
            'title' => '我的金麦穗'
        ];
        return response()->json($result);
    }

    /**
     * 金麦红利转为余额
     *
     * @return \Illuminate\Http\Response
     */
    public function bonusToReward(Request $request){

        $result = [];

        $user = Auth::user();

        $transaction_password = $request->transaction_password;
        //检查当前密码是否正确
        if(!\Hash::check($transaction_password, $user->transaction_password)){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起,当前交易密码错误';
            return $result;
        }

        $user_gold = $user->gold()->first();

        if(empty($user_gold)){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有收益';
            return response()->json($result);
        }

        if($user_gold['bonus_amount'] <=0){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有收益';
            return response()->json($result);
        }

        $res = \DB::transaction(function() use ($user, $user_gold) {
            $bonus_amount = $user_gold['bonus_amount'] * 0.9;
            $c_amount = $bonus_amount * 0.5;
            $c_point = $bonus_amount * 0.5;
            if($c_amount > 0){
                $content = "金麦穗红利转为余额:￥$c_amount";
                $remarks = '金麦穗红利转为余额';
                UserService::getInstance()->userRewardIncome($user, $c_amount, $content, $remarks);
            }
            if($c_point > 0){
                $content = "收入有赏积分:￥$c_point";
                $remarks = '红利转为有赏积分';
                UserService::getInstance()->userIntegralIncome($user, $c_point, $content, 0, $remarks);
            }
            $user_gold->bonus_amount = 0;
            $user_gold->save();
            return true;
        }); 
        if(!$res){
            $result['code'] = '2x1';
            $result['message'] = '对不起，操作失败，请稍后再试！';
            return response()->json($result);
        }
        $result['code'] = 'Success';
        $result['message'] = '转换成功';
        $result['data'] = [];
        return response()->json($result);
    }

    /**
     * 金麦转换余额
     *
     * @return \Illuminate\Http\Response
     */
    public function goldComtoReward(Request $request){
        $result = [];
        $user = Auth::user();
        $transaction_password = $request->transaction_password;
        //检查当前密码是否正确
        if(!\Hash::check($transaction_password, $user->transaction_password)){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起,当前交易密码错误';
            return $result;
        }
        $gold_config = GoldConfigModel::first();
        $user_gold = $user->gold()->first();
        if(empty($user_gold)){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有任何金麦';
            return response()->json($result);
        }
        $gold_number = $user_gold->gold_number;
        if($gold_number <= 0){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有任何金麦';
            return response()->json($result);
        }
        $gift_unit = $gold_config->gift_unit;
        $amount = $gift_unit * $gold_number;
        $res = \DB::transaction(function() use ($user, $gold_number, $amount) {
            UserService::getInstance()->userGoldNumberOut($user, $gold_number);
            $content = "金麦穗价值转为赏金:￥$amount";
            $remarks = '金麦穗价值转为余额';
            UserService::getInstance()->userRewardIncome($user, $amount, $content, $remarks);
            return true;
        }); 
        if(!$res){
            $result['code'] = '2x1';
            $result['message'] = '对不起，操作失败，请稍后再试！';
            return response()->json($result);
        }
        $result['code'] = 'Success';
        $result['message'] = '转出成功';
        $result['data'] = [];
        return response()->json($result);
    }
}
