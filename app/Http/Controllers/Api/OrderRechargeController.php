<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use App\Libs\Service\OrderRechargeService;
use EasyWeChat\Foundation\Application;
use App\Models\Order\Recharge as OrderRecharge;  

class OrderRechargeController extends BaseController
{
    /**
     * viporder
     *
     * @return void
    */
    public function vipOrder(Application $app, Request $request)
    {
        return OrderRechargeService::vipPayment($app, $request);
    }


     /**
     * viporder1
     *
     * @return void
    */
    public function vipUpgradeOrder(Application $app, Request $request)
    {
        return OrderRechargeService::vipUpgradePayment($app, $request);
    }

    /**
     * 积分充值订单
     *
     * @return void
    */
    public function integralOrder(Application $app, Request $request)
    {
        return OrderRechargeService::integralPayment($app, $request);
    }

    /**
     * 积分充值订单
     *
     * @return void
    */
    public function cardRenewalOrder(Application $app, Request $request)
    {
        return OrderRechargeService::cardRenewalPayment($app, $request);
    }

    /**
     * 积分充值订单
     *
     * @return void
    */
    public function storeOrder(Application $app, Request $request)
    {
        return OrderRechargeService::storePayment($app, $request);
    }

    /**
     * 检查vip订单是否支付完成
     *
     * @return void
    */
    public function checVipkOrderPay(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_no = $request->order_no;

        $OrderRecharge = OrderRecharge::where('order_no', '=', $order_no)
        ->where('order_type', '=', 'vip')
        ->first();

        if($OrderRecharge != null){
            $response = $app->payment->query($order_no);
            if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
                if ($response['trade_state'] == 'SUCCESS') {
                    /*OrderRechargeService::vipOrderPay($OrderRecharge);
                    try{
                        OrderRechargeService::wxOrderRecord($OrderRecharge, $response);
                    } catch(Exception $e){}*/
                    $result['code'] = 'Success';
                    return response()->json($result);
                }
            }
        }

        return response()->json($result);
    }

    /**
     * 检查积分充值订单是否支付完成
     *
     * @return void
    */
    public function checkIntegralOrderPay(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_no = $request->order_no;

        $OrderRecharge = OrderRecharge::where('order_no', '=', $order_no)
        ->where('order_type', '=', 'integral')
        ->first();

        if($OrderRecharge != null){
            $response = $app->payment->query($order_no);
            if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
                if ($response['trade_state'] == 'SUCCESS') {
                    /*OrderRechargeService::integralOrderPay($OrderRecharge);
                    try{
                        OrderRechargeService::wxOrderRecord($OrderRecharge, $response);
                    } catch(Exception $e){}*/
                    $result['code'] = 'Success';
                    return response()->json($result);
                }
            }
        }

        return response()->json($result);
    }

    /**
     * 检查积分充值订单是否支付完成
     *
     * @return void
    */
    public function checkCardRenewalOrderPay(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_no = $request->order_no;

        $OrderRecharge = OrderRecharge::where('order_no', '=', $order_no)
        ->where('order_type', '=', 'card_renewal')
        ->first();

        if($OrderRecharge != null){
            $response = $app->payment->query($order_no);
            if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
                if ($response['trade_state'] == 'SUCCESS') {
                    /*OrderRechargeService::integralOrderPay($OrderRecharge);
                    try{
                        OrderRechargeService::wxOrderRecord($OrderRecharge, $response);
                    } catch(Exception $e){}*/
                    $result['code'] = 'Success';
                    return response()->json($result);
                }
            }
        }

        return response()->json($result);
    }

    /**
     * 检查积分充值订单是否支付完成
     *
     * @return void
    */
    public function checkStoreOrderPay(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_no = $request->order_no;

        $OrderRecharge = OrderRecharge::where('order_no', '=', $order_no)
        ->where('order_type', '=', 'store')
        ->first();

        if($OrderRecharge != null){
            $response = $app->payment->query($order_no);
            if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
                if ($response['trade_state'] == 'SUCCESS') {
                    /*OrderRechargeService::storeOrderPay($OrderRecharge);
                    try{
                        OrderRechargeService::wxOrderRecord($OrderRecharge, $response);
                    } catch(Exception $e){}*/
                    $result['code'] = 'Success';
                    return response()->json($result);
                }
            }
        }

        return response()->json($result);
    }
}