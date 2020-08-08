<?php
namespace App\Libs\Service;

use Auth;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Payout\Apply as PayoutApplyModel;
use App\Models\User\PhoneCode as PhoneCodeModel;

class PayoutService
{
    /**
     * payment
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static function apply($request){
        //事务处理
        $result = \DB::transaction(function() use ($request) {

            $user = Auth::user();

            $user_id = $user->id;

            $type = isset($request->type) ? $request->type : '1';

            $card_number = trim($request->card_number);

            $card_bank = trim($request->card_bank);

            $alipay = trim($request->alipay);

            $transaction_password = $request->transaction_password;

            $date = date('Y-m-d 00:00:00');

            $date_end = date('Y-m-d 23:23:59');

            $amount = $request->amount;

            $dayPayoutApplyCount = PayoutApplyModel::where('user_id', '=', $user_id)->where('created_at', '>=', $date)
            ->where('created_at', '<=', $date_end)
            ->where('status', '!=', '0')
            ->count();

            if($dayPayoutApplyCount > 3){
                $result['code'] = "2xf";
                $result['message'] = '对不起,一天仅限提现3次,明天再来！';
                return $result;
            }

            //检查当前密码是否正确
            if($amount <3){
                $result['code'] = "2xf";
                $result['message'] = '对不起,单笔最小3元';
                return $result;
            }

            //检查当前密码是否正确
            if($amount > 5000){
                $result['code'] = "2xf";
                $result['message'] = '对不起,单笔限额5000!';
                return $result;
            }

            //检查当前密码是否正确
            if(!\Hash::check($transaction_password, $user->transaction_password)){
                $result['code'] = "0x00x2";
                $result['message'] = '对不起,当前交易密码错误';
                return $result;
            }

            //code
            $code = trim($request->code);
            $PhoneCodeModel = PhoneCodeModel::where('user_id', $user_id)->where('code', $code)
            ->where('type', 'payout')
            ->first();
            if($PhoneCodeModel == null){
                $result['code'] = 'code_error';
                $result['message'] = '验证码错误！';
                return $result;
            }

            if($PhoneCodeModel['is_use'] == '1'){
                $result['code'] = 'code_error';
                $result['message'] = '验证码已使用！';
                return $result;
            }

            $created_at = $PhoneCodeModel['created_at'];

            $new_date = date('Y-m-d H:i:s', strtotime("-10minute", time()));

            if($new_date > $created_at){
                $result['code'] = 'code_error';
                $result['message'] = '验证码已过期！';
                return $result;
            }

            if($type == '1'){
                if($alipay == ''){
                    $result['code'] = "0x00x1";
                    $result['message'] = '对不起,支付宝不能为空！';
                    return $result;
                } 
            }

            if($type == '2'){
                if($card_number == ''){
                    $result['code'] = "0x00x1";
                    $result['message'] = '对不起,银行卡不能为空！';
                    return $result;
                }
                if($card_bank == ''){
                    $result['code'] = "0x00x1";
                    $result['message'] = '对不起,请输入开户银行';
                    return $result;
                }
            }

            $reward = $user->reward()->first();

            if($reward == null){
                $result['code'] = "0x00x1";
                $result['message'] = '对不起,您没有可提现的赏金！';
                return $result;
            }

            $freeze_amount = $reward->freeze_amount;

            if($freeze_amount < 0){
                $freeze_amount = 0;
            }

            $enable_amount = $reward->amount - $freeze_amount;

            if($amount > $enable_amount){
                $result['code'] = "0x00x1";
                $result['message'] = '对不起,当前可提现的赏金最多为！￥' . $enable_amount;
                return $result;
            }

            $fullname = trim($request->fullname);

            $card_number = trim($request->card_number);

            $card_bank = trim($request->card_bank);

            $alipay = trim($request->alipay);

            $payout_apply = new PayoutApplyModel();
            $payout_apply->number = static::generatNumber($user_id);
            $payout_apply->user_id = $user_id;
            $payout_apply->amount = $amount;
            $payout_apply->actual_amount = $amount - 2;
            $payout_apply->fullname = $fullname;
            $payout_apply->type = $type;
            if($type == '1'){
                $payout_apply->alipay = $alipay;
            } else {
                $payout_apply->card_number = $card_number;
                $payout_apply->card_bank = $card_bank;
            }
            $r = $payout_apply->save();
            if($r){
                $freeze_amount = $reward->freeze_amount + $amount;
                if($freeze_amount > $reward->amount){
                   $freeze_amount =  $reward->amount;
                }
                $reward->freeze_amount = $freeze_amount;
                $reward->save();
            }
            $PhoneCodeModel->is_use = '1';
            $PhoneCodeModel->save();
            $result = ['code' => 'Success', 'message' => '成功！'];
            return $result;
        });
        return $result;
    }

    /**
     * 生成编号
     */
    public static function generatNumber($user_id){
        $time_str = date('YmdHis');
        $md5_str = md5(uniqid().$user_id);
        $number = $time_str .  substr($md5_str, 0, 10);
        return $number;
    }

    /**
     * payment
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static function approvalApply($payout_apply, $admin_user){
        $result = ['code' => '', 'message' => ''];
        //事务处理
        $res = \DB::transaction(function() use ($payout_apply, $admin_user) {
            if($payout_apply->status == '2'){
                return;
            }
            $payout_apply->status = '2';
            $payout_apply->approve_time = date('Y-m-d H:i:s');
            $payout_apply->approval_admin_id = $admin_user->id;
            $r = $payout_apply->save();
            if($r){
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
                $content = '赏金提现 减少￥' . $amount;
                UserService::getInstance()->userRewardOut($user, $amount, $content);
                $data = [
                    'user_id' => $user->id,
                    'name' => "您的提现申请已成功处理",
                    'content' => "您的提现申请已成功处理，请检查关注到帐情况！",
                    'link' => '/account/payout/index'
                ];
                MessageService::insert($data);
                return true;
            }
            $result = ['code' => 'Success', 'message' => '成功！'];
            return $result;
        });
        return $result;
    }
}