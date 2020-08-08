<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use EasyWeChat\Foundation\Application;
use Log;
use App\Models\User\User as UserModel;
use App\Helper\Base as Helper;
use EasyWeChat;

use EasyWeChat\Payment\Order;
use App\Models\Order\Recharge as OrderRecharge; 
use App\Models\Order\Order as OrderModel;
use App\Libs\Service\UserService;
use App\Libs\Service\AuthService;
use App\Libs\Service\OrderRechargeService;
use App\Libs\Service\OrderService;


class WechatController extends Controller
{

     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function oauthAuth(Application $wechat, Request $request)
    {
        $user = session('wechat.oauth_user'); // 拿到授权用户资料
        $login_user = \Auth::user();
        if(!empty($user)){
            $openid = $user->id;
            $wx_user = UserModel::where('openid', '=', $openid)->first();
            if($wx_user == null){
                if($login_user == null){
                    //return redirect()->guest('/auth/login');
                } else {
                    if($login_user->openid == null){
                        $login_user->openid = $openid;
                        $login_user->nickname = $user->nickname;
                        if($login_user->nickname == null){
                            $login_user->nickname = $wx_user->nickname;
                        }
                        if($login_user->avatar == null || $login_user->avatar == ''){
                            $login_user->avatar = $user->avatar;
                        }
                        $login_user->save();
                    }
                }
            } 
            if(!empty($login_user)){
                if($login_user->avatar == null || $login_user->avatar == ''){
                    $login_user->avatar = $user->avatar;
                }
                $login_user->save();
            }
        }

        $wx_oauth_back = \Session::get('wx_oauth_back');

        if($wx_oauth_back != null && $wx_oauth_back != ''){
            return redirect($wx_oauth_back);
        }

        return redirect(Helper::route('home'));
    }
     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function vipPaymentBack(Application $app, Request $request)
    {
        \Log::info('paid:back:');
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order_no = $notify['out_trade_no']; 
            $order = OrderRecharge::where('order_no', '=', $order_no)->first();
            if (!$order) { // 如果订单不存在
                return true; // 返回处理完成
            }
            if($order->status == '2'){
                return true;
            }
            if($successful){
                //订单支付成功
                OrderRechargeService::vipOrderPay($order);
                try{
                    OrderRechargeService::wxOrderRecord($order, $notify);
                } catch(Exception $e){}
                return true; // 返回处理完成
            } else {
                $order->faild_at = date('Y-m-d H:m:s');
                $order->save(); // 保存订单
            }
            return true; // 返回处理完成
        });
        return $response;
    }

     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function integralPaymentBack(Application $app, Request $request)
    {
        \Log::info('paid:back:');
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order_no = $notify['out_trade_no']; 
            $order = OrderRecharge::where('order_no', '=', $order_no)->first();
            if (!$order) { // 如果订单不存在
                return true; // 返回处理完成
            }
            if($order->status == '2'){
                return true;
            }
            if($successful){
                //订单支付成功
                OrderRechargeService::integralOrderPay($order);
                try{
                    OrderRechargeService::wxOrderRecord($order, $notify);
                } catch(Exception $e){}
                return true; // 返回处理完成
            } else {
                $order->faild_at = date('Y-m-d H:m:s');
                $order->save(); // 保存订单
            }
            return true; // 返回处理完成
        });
        return $response;
    }

     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function cardRenewalPaymentBack(Application $app, Request $request)
    {
        \Log::info('paid:back:');
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order_no = $notify['out_trade_no']; 
            $order = OrderRecharge::where('order_no', '=', $order_no)->first();
            if (!$order) { // 如果订单不存在
                return true; // 返回处理完成
            }
            if($order->status == '2'){
                return true;
            }
            if($successful){
                //订单支付成功
                OrderRechargeService::cardRenewalOrderPay($order);
                try{
                    OrderRechargeService::wxOrderRecord($order, $notify);
                } catch(Exception $e){}
                return true; // 返回处理完成
            } else {
                $order->faild_at = date('Y-m-d H:m:s');
                $order->save(); // 保存订单
            }
            return true; // 返回处理完成
        });
        return $response;
    }

     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function orderProductPaymentBack(Application $app, Request $request)
    {
        \Log::info('paid:back:');
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order_no = $notify['out_trade_no']; 
            $order = OrderModel::where('order_no', '=', $order_no)->first();
            if (!$order) { // 如果订单不存在
                return true; // 返回处理完成
            }
            if($order->is_pay == '1'){
                return true;
            }
            if($order->order_status_code != 'pending'){
                return true;
            }
            if($successful){
                OrderService::orderPayHandel($order);
                try{
                    OrderService::wxOrderRecord($order, $notify);
                } catch(Exception $e){}
                return true; // 返回处理完成
            } else {
                $order->faild_at = date('Y-m-d H:m:s');
                $order->save(); // 保存订单
            }
            return true; // 返回处理完成
        });
        return $response;
    }

     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function storePaymentBack(Application $app, Request $request)
    {
        \Log::info('paid:back:');
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order_no = $notify['out_trade_no']; 
            $order = OrderRecharge::where('order_no', '=', $order_no)->first();
            if (!$order) { // 如果订单不存在
                return true; // 返回处理完成
            }
            if($order->status == '2'){
                return true;
            }
            if($successful){
                //订单支付成功
                OrderRechargeService::storeOrderPay($order);
                try{
                    OrderRechargeService::wxOrderRecord($order, $notify);
                } catch(Exception $e){}
                return true; // 返回处理完成
            } else {
                $order->faild_at = date('Y-m-d H:m:s');
                $order->save(); // 保存订单
            }
            return true; // 返回处理完成
        });
        return $response;
    }
}