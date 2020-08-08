<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;

use App\Models\User\User as UserModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Order\Recharge as OrderRechargeModel;
use App\Models\Order\Order as OrderModel;
use App\Models\User\Integral as IntegralModel;
use App\Models\User\Reward as RewardModel;
use App\Models\Payout\Apply as PayoutApplyModel;
use App\Models\User\IntegralSend;


class StatisticsController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $form= $request->all();

        $view = View('admin.statistics.info');

        $view->with("form", $form);

        $view->with("title", "统计");

        return $view;

    }

    public function getUserCount(){

        $data = [];

        $statistics = [];

        //系统用户数
        $user_count = UserModel::count();

        $data['user_count'] = $user_count;

        //普通用户数
        $general_user_count = UserModel::where('is_vip', '0')->where('level_status', '=', '0')->count();

        $statistics['general_user_count'] = $general_user_count;

        //vip用户数
        $vip_count = UserModel::where('is_vip', '1')->where('level_status', '>=', '1')->count();

        $statistics['vip_count'] = $vip_count;

        //手动激活vip
        $manual_vip_count = UserModel::where('is_vip', '1')->where('level_status', '>=', '1')->where('is_auto_open_vip')->count();

        $statistics['manual_vip_count'] = $manual_vip_count;

        //vip用户数
        $vip_user_count = UserModel::where('is_vip', '1')->where('level_status', '=', '1')->count();

        $statistics['vip_user_count'] = $vip_user_count;

        //vip金卡
        $vip_2_count = UserModel::where('is_vip', '1')->where('level_status', '2')->count();

        $statistics['vip_2_count'] = $vip_2_count;

        //铂金vip
        $vip_3_count = UserModel::where('is_vip', '1')->where('level_status', '3')->count();

        $statistics['vip_3_count'] = $vip_3_count;

        //vip缴费量
        $vip_all_pay_count = OrderRechargeModel::where('order_type', 'vip')
        ->where('status', '=', '2')
        ->count();

        $statistics['vip_all_pay_count'] = $vip_all_pay_count;

        //vip缴费量
        $vip_pay_count = OrderRechargeModel::where('order_type', 'vip')
        ->where('vip_type', '=', '1')
        ->where('status', '=', '2')
        ->count();

        $statistics['vip_pay_count'] = $vip_pay_count;

         //vip续费量
        $vip_renewal_count = OrderRechargeModel::where('order_type', 'vip')
        ->where('vip_type', '=', '1')
        ->where('status', '=', '2')
        ->count();

        $statistics['vip_renewal_count'] = $vip_renewal_count;

        $result = [];
        $result['code'] = '200';
        $result['data']['user_count'] = $user_count;
        $result['data']['statistics'] = $statistics;
        return json_encode($result);
    }

    public function getStoreCount(){

        $data = [];

        $statistics = [];

        //开网店
        $store_count = StoreModel::count();

        $data['store_count'] = $store_count;

        //手动激活网店
        $store_manual_count = StoreModel::where('is_pay', '1')
        ->join('user', 'store_account.user_id', '=', 'user.id')
        ->where('user.is_auto_open_store', '1')
        ->groupBy('store_account.id')
        ->count();

        $statistics['store_manual_count'] = $store_manual_count;

        //缴费网店
        $store_pay_count = StoreModel::where('is_pay', '1')
        ->join('order_recharge', 'store_account.user_id', '=', 'order_recharge.user_id')
        ->where('order_recharge.status', '=', '2')
        ->where('order_type', 'store')
        ->groupBy('store_account.id')
        ->count();

        $statistics['store_pay_count'] = $store_pay_count;

        //启用店铺
        $store_valid_count = StoreModel::where('status', '=', '2')
        ->count();

        $statistics['store_valid_count'] = $store_valid_count;

        //禁用店铺
        $store_no_valid_count = $store_count - $store_valid_count;

        $statistics['store_no_valid_count'] = $store_no_valid_count;

        //正在运行店铺
        $store_valid_count = StoreModel::where('status', '=', '2')
        ->whereRaw('store_account.expire_date > now()')
        ->count();

        $statistics['store_run_count'] = $store_no_valid_count;

        //临时掌柜
        $store_0_count = UserModel::join('store_account', 'store_account.user_id', '=', 'user.id')
        ->where('user.store_level', '=', '0')
        ->count('user.id');

        $statistics['store_0_count'] = $store_0_count;

        //网店掌柜
        $store_1_count = UserModel::where('store_level', '1')
        ->count();

        $statistics['store_1_count'] = $store_1_count;

        //金牌掌柜
        $store_2_count = UserModel::where('store_level', '2')
        ->count();

        $statistics['store_2_count'] = $store_2_count;

        //正在运行店铺
        $store_expire_count = StoreModel::where('status', '=', '2')
        ->whereRaw('store_account.expire_date < now()')
        ->count();

        $statistics['store_expire_count'] = $store_expire_count;

        //店铺缴费量
        $store_renewal_count = OrderRechargeModel::where('order_type', 'store')
        ->where('status', '=', '2')
        ->count();

        $statistics['store_renewal_count'] = $store_renewal_count;

        $statistics['store_pay_count'] = $store_pay_count;

        $result = [];
        $result['code'] = '200';
        $data['statistics'] = $statistics;
        $result['data'] = $data;
        return json_encode($result);
    }

     public function getOrderAmount(){

        $statistics = [];

        //订单销售总额
        $order_total = OrderModel::where('is_pay', '=', '1')
        ->where('order_status_code', '!=', 'cancel')
        ->sum('order_total');

        $statistics['order_total'] = $order_total;

        //自营订单销售总额
        $self_order_total = OrderModel::where('is_self', '1')
        ->where('is_pay', '=', '1')
        ->where('order_status_code', '!=', 'cancel')
        ->sum('order_total');

        $statistics['self_order_total'] = $self_order_total;

        //自营订单销售总额
        $store_order_total = OrderModel::where('is_self', '0')
        ->where('seller_id', '>', '0')
        ->where('is_pay', '=', '1')
        ->where('order_status_code', '!=', 'cancel')
        ->sum('order_total');

        $statistics['store_order_total'] = $store_order_total;

        //营业额扣点数总额
        $order_actual_total = OrderModel::where('is_pay', '=', '1')
        ->sum('order_actual_total');

        $statistics['order_actual_total'] = $order_actual_total;

        $result = [];
        $result['code'] = '200';
        $result['data'] = $statistics;
        return json_encode($result);
    }

    public function getOrderCount(){

        $statistics = [];

        //订单数
        $order_pay_count = OrderModel::where('is_pay', '=', '1')
        ->count();

        $statistics['order_pay_count'] = $order_pay_count;

        //自营订单数
        $self_order_count = OrderModel::where('is_self', '1')
        ->where('is_pay', '=', '1')
        ->count();

        $statistics['self_order_count'] = $self_order_count;

        //自营订单待发货
        $shipping_order_count = OrderModel::where('is_self', '1')
        ->where('order_status_code', '=', 'shipping')
        ->count();

        $statistics['shipping_order_count'] = $shipping_order_count;

        //个人网店订单数
        $store_order_count = OrderModel::where('is_self', '0')
        ->where('is_pay', '=', '1')
        ->count();

        $statistics['store_order_count'] = $store_order_count;

        //个人网店订单待发货数
        $store_shipping_order_count = OrderModel::where('is_self', '0')
        ->where('order_status_code', '=', 'shipping')
        ->count();

        $statistics['store_shipping_order_count'] = $store_shipping_order_count;

        $result = [];
        $result['code'] = '200';
        $result['data'] = $statistics;
        return json_encode($result);
    }

    public function x(){
           //名片缴费量
        $card_renewal_count = OrderRechargeModel::where('order_type', 'card_renewal')
        ->where('status', '=', '2')
        ->count();

        $statistics['card_renewal_count'] = $card_renewal_count;

        //积分缴费量
        $integral_count = OrderRechargeModel::where('order_type', 'integral')
        ->where('status', '=', '2')
        ->count();

        $statistics['integral_count'] = $integral_count;
    }

     public function getAmount(){

        $statistics = [];

        //总剩余赏金
        $reward_amount = RewardModel::sum('amount');

        $statistics['reward_amount'] = '￥' . $reward_amount;

        //总剩余积分
        $integral_amount = IntegralModel::sum('point');

        $statistics['integral_amount'] = '￥' . $integral_amount;


        //申请提现总金额
        $payout_total_amount = PayoutApplyModel::sum('amount');

        $statistics['payout_total_amount'] = '￥' . $payout_total_amount;


        //已提现处理总额
        $payout_processed_amount = PayoutApplyModel::where('status', '2')->sum('amount');

        $statistics['payout_processed_amount'] = '￥' . $payout_processed_amount;

        //已提现拨款出去总额
        $payout_processed_actual_amount = PayoutApplyModel::where('status', '2')->sum('actual_amount');

        $statistics['payout_processed_actual_amount'] = '￥' . $payout_processed_actual_amount;


        //申请提现笔数
        $payout_count = PayoutApplyModel::count();

        $statistics['payout_count'] = $payout_count;

        $integral_send_amount = IntegralSend::sum('integral');

        $statistics['integral_send_amount'] = '￥' . $integral_send_amount;

        $integral_pay = OrderRechargeModel::where('order_type', 'integral')->where('status', '2')->sum('amount');

        $statistics['integral_pay'] = '￥' . $integral_pay;

        $result = [];
        $result['code'] = '200';
        $result['data']['statistics'] = $statistics;
        return json_encode($result);
    }
}