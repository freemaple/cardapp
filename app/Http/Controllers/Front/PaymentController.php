<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use Auth;
use Validator;
use Session;
use Helper;
use EasyWeChat\Foundation\Application;
use Illuminate\Routing\Controller;
use App\Models\User\User as UserModel;
use EasyWeChat;
use EasyWeChat\Payment\Order;


class PaymentController extends BaseController
{

    /**
     * viporder
     *
     * @return void
    */
    public function pay(Application $app, Request $request)
    {
        $view = view('pay.index', [
            'title' => '搜索'
        ]);

        return $view;
    }

    /**
     * payment
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public function payOrder(Application $app, Request $request){

        $result = [];
        
        $order_no = $this->generateOrderNumber();

        $is_weixin = 0;

        $data = [];

        $pay_amount = 100;

        if(Helper::isWeixin()){
            $is_weixin = '1';
            $openid = $user->openid;
            $product = [
                'body' => '充值',
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'notify_url'         => Helper::route('wx_pay_back'), 
                'openid' => $openid
            ];
            $data = $this->jsapi($app, $product);
        } else {
            $product = [
                'body' => '充值',
                'nonce_str' => uniqid(),
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'MWEB', // 请对应换成你的支付方式对应的值类型
                'notify_url' => Helper::route('wx_pay_back'), 
                'spbill_create_ip' => Helper::getIPAddress(),
                'sign_type' => 'MD5',
                'detail' => '充值',
                'scene_info' => json_encode([
                    'h5_info' => [
                        'type' => 'Wap',
                        'wap_url' => '',
                        'wap_name' => '人人有赏'
                    ]
                ])
            ];
            $data = $this->h5($app, $product);
        }
        $data['order_no'] = $order_no;
        $data['is_weixin'] = $is_weixin;
        $result['data'] = $data;
        $result['code'] = "Success";
        return $result;
    }

    //jsapi支付
    public function jsapi($app, $product){
        $order = new Order($product);
        $js = $app->js;
        $payment = $app->payment;
        $result = $payment->prepare($order);
        $prepayId = null;
        $config = [];
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            $config = $payment->configForJSSDKPayment($prepayId);
        }
        $config['prepayId'] = $prepayId;
        return $config;
    }

    //h5支付
    public function h5($app, $product){
        $order = new Order($product);
        $payment = $app->payment;
        $result = $payment->prepare($order);
        $data = ['prepayId' => null, 'mweb_url' => ''];
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $data['prepayId'] = $result->prepay_id;
            $data['mweb_url'] = $result['mweb_url'];
        }
        return $data;
    }

    /**
     * 生成编号
     */
    public function generateOrderNumber(){
        $time_str = date('ymdHis');
        $md5_str = md5(uniqid());
        $number = $time_str .'test' . substr($md5_str, 0, 10);
        return $number;
    }

    //jsapi支付
    public function paymentcallback(Application $app, Request $request){
        $response = $app->payment->handleNotify(function($notify, $successful){
            return true;
        });
        return $response;
    }
}