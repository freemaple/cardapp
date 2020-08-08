<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use Auth;
use App\Models\User\User as UserModel;
use App\Libs\Service\UserService;
use App\Libs\Service\MessageService;

class IntegralController extends BaseController
{


     /**
     * 积分记录
     *
     * @return void
    */
    public function index(Request $request)
    {
        $result = ['code' => '2x1', 'data' => []];

        $data = [];

        $user = Auth::user();

        $integral_amount = 0;
        //积分
        $integral = $user->integral()->first();
        if($integral != null){
           $integral = $integral->toArray();
           $integral_amount = $integral['point'];
        }

        $data['integral_amount'] = $integral_amount;

        $reward_in = $user->integralRecord()->where('type', '=', '1')->orderBy('id','desc')->sum('point');

        $reward_out = $user->integralRecord()->where('type', '=', '2')->orderBy('id','desc')->sum('point');

        $data['reward_in'] = $reward_in;

        $data['reward_out'] = $reward_out;

        $result = ['code' => 'Success', 'data' => $data];

        return response()->json($result);

    }


     /**
     * 积分记录
     *
     * @return void
    */
    public function record(Request $request)
    {
        $user = Auth::user();

        $pageSize = config('paginate.integral.record', 50);

        $rewardRecord = $user->integralRecord()->orderBy('id','desc')->paginate($pageSize);

        $integralRecords = $rewardRecord->toArray();

        $data = [];

        if($request->type == 'app'){
            $data['integralRecords'] = $integralRecords;
            $result = ['code' => 'Success',  'data' => $data];
        } else {
            $view = view('account.integral.block.list')->with('integralRecords', $integralRecords['data'])->render();
            $result = ['code' => 'Success', 'view' => $view, 'data' => []];
        }
        return response()->json($result);

    }

    /**
     * 积分转账
     *
     * @return void
    */
    public function transfer(Request $request)
    {
        $user = Auth::user();

        $payer = $request->payer;

        if($payer == $user->id){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起，您不能给自己转账积分!';
            return json_encode($result);
        }

        $payer_user = UserModel::where('id', '=', $payer)->first();
        if($payer_user == null ){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起，收款人不存在！';
            return json_encode($result);
        }


        $amount = $request->amount;

        $transaction_password = $request->transaction_password;

        //检查当前密码是否正确
        if(!\Hash::check($transaction_password, $user->transaction_password)){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起,当前交易密码错误';
            return json_encode($result);
        }

        $integral = $user->integral()->first();
        if($integral == null || $integral['point'] <=0){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起，您没有任何积分可转账!';
            return json_encode($result);
        }

        if($amount > $integral['point']){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起，最多可转账:' . $integral['point'];
            return json_encode($result);
        }

        $result = \DB::transaction(function() use ($user, $payer_user, $amount) {

            //接收人消息
            $content = '收到有赏积分 ￥' . $amount . '，转入人：' . $user['fullname'] . ' (' . $user['phone'] . ')';

            UserService::getInstance()->userIntegralIncome($payer_user, $amount, $content);

            $title = '收到有赏积分 ￥' . $amount;

            $data = [
                'user_id' => $payer_user->id,
                'name' => $title,
                'content' => $content
            ];
            MessageService::insert($data);

            //转账人消息
            $p_content = '转出积分 ￥' . $amount . ' 收款人：' . $payer_user['fullname'] . '(' . $payer_user['phone'] . ')';

            UserService::getInstance()->userIntegralOut($user, $amount, $p_content);
            
            return true;
        }); 

        $result = ['code' => 'Success', 'message' => '转账成功！'];

        return response()->json($result);

    }

    /**
     * 用户基本信息修改
     * @param  Request $request 
     * @return string           
     */
    public function checkPayer(Request $request){
        $user = Auth::user();
        $result = [];
        $payer_id = $request->payer;
        $payer = UserModel::where('id', '=', $payer_id)->first();
        if($payer == null){
            $result['code'] = 'no_exist';
            $result['message'] = '收款人不存在，请核对账户代码！';
            return response()->json($result);
        }

        if($payer['id'] == $user['id']){
            $result['code'] = 'no_exist';
            $result['message'] = '您不能给自己转账!';
            return response()->json($result);
        }

        $phone = \Helper::hideStar($payer['phone']);

        $user_name = $payer['fullname'] . '(' . $phone . ')';

        $result = [];

        $data = ['user_name' => $user_name];

        $result['code'] = 'Success';

        $result['data'] =  $data;

        return response()->json($result);
    }


    /**
     * 赏金转为有赏积分
     * @param  Request $request 
     * @return string           
     */
    public function toReward(Request $request){
        $result = [];
        $user = Auth::user();
        $transaction_password = $request->transaction_password;
        //检查当前密码是否正确
        if(!\Hash::check($transaction_password, $user->transaction_password)){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起,当前交易密码错误';
            return $result;
        }
        $integral = $user->integral()->first();
        if($integral == null){
            $result['message'] = '对不起，您已没有任何积分！';
            $result['code']  = 'no_integral';
            return response()->json($result);
        }
        if($integral->store_sales_points > $integral->point){
            $integral->store_sales_points = $integral->point;
        }
        $store_sales_points = $integral->store_sales_points;
        $max_sales_points = config('user.integral.max_sales_points');
        if($store_sales_points < $max_sales_points){
            $result['message'] = '店铺结算积分满' . $max_sales_points . '，才可以转入余额提现！';
            $result['code']  = 'no_integral';
            return response()->json($result);
        }
        $amount = $request->amount;
        if($amount <=0){
            $result['message'] = '对不起,金额必须大于0';
            $result['code']  = 'no_integral';
            return response()->json($result);
        }
        if($store_sales_points <= 0){
            $result['message'] = '对不起，您已没有任何可转换积分！';
            $result['code']  = 'no_integral';
            return response()->json($result);
        }
        if($amount > $store_sales_points){
            $result['message'] = '对不起，最多可转换的店铺结算积分为' . $store_sales_points;
            $result['code']  = 'no_integral';
            return response()->json($result);
        }
        if($amount >= $store_sales_points){
            $store_sales_points = $amount;
        }
        $content = "店铺结算积分转出余额 减少￥" . $amount;
        //登录事务处理
        $return_result = \DB::transaction(function() use ($user, $amount, $content, $integral) {
            UserService::getInstance()->userIntegralOut($user, $amount, $content);
            $integral->store_sales_points = $integral->store_sales_points - $amount;
            $integral->save();
            $reward_content = '店铺结算积分转入 增加￥' . $amount;
            UserService::getInstance()->userRewardIncome($user, $amount, $reward_content);
        });
        $result['code'] = 'Success';
        $result['message'] = '转换成功！';
        return response()->json($result);
    }
}
