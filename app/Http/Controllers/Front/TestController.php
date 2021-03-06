<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Helper;

use App\Libs\Service\OrderRechargeService;
use EasyWeChat\Foundation\Application;

use EasyWeChat\Payment\Order;
use App\Models\Product\Product as ProductModel; 
use App\Models\Order\Recharge as OrderRecharge; 
use App\Models\Order\Order as OrderModel; 
use App\Models\Order\OrderRefund as OrderRefundModel; 
use App\Libs\Service\UserService;
use App\Libs\Service\OrderService;
use App\Libs\Service\AuthService;
use App\Libs\Service\WXService;
use App\Models\User\User as UserModel;
use App\Libraries\Storage\User as UserStorage;
use App\Models\Theme\Background;


class TestController extends BaseController
{


    /**
     * 编辑
     * @param  Request $request 
     * @return string           
     */
    public function upload(Request $request){
        
        //当前密码
        $result = [];
        //检查文件上传
        $file = $request->file('image');
        if(!$file || !$file->isValid()){
            $result['code'] = '0x0x0f';
            $result['message'] = 'This is not a valid image.';
            return json_encode($result);
        }
        $UserStorage = new UserStorage('background');
        $filepath = $UserStorage->saveUpload($file);
        $bg = Background::where('image', $filepath)
        ->where('type', '2')
        ->first();
        if($bg == null){
            echo 1;
            $Background = new Background();
            $Background->image = $filepath;
            $Background->type = '2';
            $Background->save();
        }
       
        $result['code'] = 'Success';
        $result['message'] = '保存成功';
        return response()->json($result);
    }

    /**
     * 测试
     *
     * @return void
    */
    public function index(Application $app, Request $request)
    {

        
        /*$users = UserModel::get();

        foreach ($users as $key => $user) {
            $u_id = AuthService::getInstance()->generateNumber();
            $user->u_id = $u_id;
            $user->save();
        }


        exit();
        $result = [];
        $result['code'] = 'Success';

        $result['data'] = $base64_code;

        echo "<img src='$base64_code' />";

        //return response()->json($result);
        //
        exit();





        dd(bcrypt('linyiyong666'));

        $next_time = strtotime(date("Y-m-d", strtotime("+1 day")));
        $new_expire_time = strtotime('+31 day', $next_time);
        //dd($new_expire_time);
        $expire_date = date('Y-m-d H:i:s', $new_expire_time);

        dd($expire_date);


        /*$order_id = $request->order_id;

        $order = OrderModel::where('id', $order_id)->first();

        if($order == null){
            $result['code'] = '2x1';
            $result['code'] = '订单不存在';
            return response()->json($result);
        } 

        $OrderRefundModel = OrderRefundModel::where('order_id', '=', $order['id'])->first();
        $reason = '用户取消订单';
        $refundNo = $OrderRefundModel->refundsn;
        $refundFee = $OrderRefundModel->amount;
        $order_no = $order['order_no'];
        $response = $app->payment->refund($order_no, $refundNo, $order['order_total'] * 100, $refundFee * 100);
        if($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS'){
            OrderService::refundAccount($order, $OrderRefundModel);
        } else {
            $result['message'] = '订单已取消，申请退款中！';
        }

        dd($response);

        exit();*/

        $view = View('test.upload', [
            'title' => '上传',
        ]);

        return $view;

        //$time_str = date('YmdHis');
       // print($time_str);
        //exit();
        //$md5_str = uniqid();
        //$x = $time_str . $md5_str;
        //echo($time_str . $md5_str);
        //echo " length:" . strlen($x);
        //exit();

        $r = OrderRechargeService::generateOrderNumber(1, 'V');

        echo $r;

        echo " length:" . strlen($r);

        //dd(storage_path() . '/cert/apiclient_cert.pem');
        /*$result = ['code' => ''];

        $order_no = $request->order_no;

        if($order_no != null){
            $refundNo = 'Test' .md5(time());
            $response = $app->payment->refund($order_no, $refundNo, 100, 100);
            dd($response);
        }
        dd(1);
        /*
        $time_str = date('YmdHis');
        $md5_str = md5(uniqid(). '1');
        $number = $time_str .  substr($md5_str, 0, 10);
        return $number;

        $view = View('test.index', [
            'title' => '上传',
        ]);

        return $view;



        $user = \Auth::user();

        WXService::registerMessage($user, $app);

        return 'success';



        $response = $app->payment->query('1901051605487b23b73cb3');
        if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
            if ($response['trade_state'] == 'SUCCESS') {
                //如果需要加入后续支付成功步骤的话自己加
                return $this->ok();
            }else {
                return $this->fail();
            }
        }else {
            return $this->fail();
        }
        dump($r);
        return '1';
        /*$order_no = $request->order_no; 
        $order = OrderRecharge::where('order_no', '=', $order_no)->first();
        if (!$order) { // 如果订单不存在
            return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
        }
        if($order->status == '2'){
            return true;
        }
        $pay_order = OrderRechargeService::vipOrderPay($order);

        dump($pay_order);

        return '1';*/
        

    }

    /**
     * 二维码
     *
     * @return \Illuminate\Http\Response
     */
    public static function qrcode($product, $size){
        $product_link = Helper::route('product_view', $product['id']);
        $product_qrcode = Helper::qrcode1($product_link, $size, [0, 0, 0]);
        return  'data:image/png;base64,' . base64_encode($product_qrcode);
    }

    /**
     * order
     *
     * @return void
    */
    public function refund(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_no = $request->order_no;

        if($order_no != null){
            $refundNo = 'Test' .md5(time());
            $response = $app->payment->refund($order_no, $refundNo, 1, 0.5, null, 'out_trade_no', 'REFUND_SOURCE_RECHARGE_FUNDS', '退款');
            dd($response);
        }
    }
}