<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use DB;
use Helper;
use Auth;
use App\Helper\Base as HelperBase;
use App\Models\User\User as UserModel;
use App\Models\Order\Order as OrderModel;
use App\Models\Order\OrderProduct as OrderProductModel;
use App\Models\Order\OrderUserInfo as OrderUserInfoModel;
use App\Models\Order\OrderShipping as OrderShippingModel;
use App\Models\Order\OrderRefund as OrderRefundModel;
use EasyWeChat;
use EasyWeChat\Payment\Order;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Product\Sku as ProductSkuModel;
use App\Models\Order\OrderStatusRecord;
use App\Models\Order\OrderAccountRecord;
use App\Models\Order\OrderWxRecord as OrderWxRecordModel;
use App\Helper\Sms as HelperSms;
use App\Jobs\OrderCancel as OrderCancelJobs;
use Carbon\Carbon;
use App\Libs\Service\Email\OrderEmailService;



class OrderService
{
    /**
     * 创建订单
     */
    public static function createOrder($payment_data, $user){
        $order = DB::transaction(function() use ($payment_data, $user) {
            $order_number = static::generateOrderNumber($payment_data['order_buyer_id']);
            $order_data = [
                'order_no' => $order_number,
                'user_id' => $payment_data['order_buyer_id'],
                'order_status_code' => 'pending',
                'order_item_qty' => $payment_data['order_item_qty'],
                'order_total' => $payment_data['amount']['total'],
                'order_subtotal' => $payment_data['amount']['details']['subtotal'],
                'order_shipping' => $payment_data['amount']['details']['shipping'],
                'currency' => $payment_data['order_currency'],
                'payment_method' => $payment_data['payment_method'],
                'order_ipaddress' => \Helper::getIPAddress(),
                'order_device' => \Helper::device(),
                'comment' => $payment_data['comment'],
                'order_integral' => $payment_data['order_integral'],
                'payment_amount' => $payment_data['payment_amount'],
                'is_self' => $payment_data['is_self'],
                'charge_points' => 0.02,
                'order_type' => isset($payment_data['order_type']) ? $payment_data['order_type'] :0
            ];
            $seller_ids = [];
            $seller_id = '0';
            $store_id = 0;
            foreach ($payment_data['products'] as $sku => $product) {
                if($product['is_self']){
                    continue;
                }
                $seller_id = $product['seller_id'];
                $store_id = $product['store_id'];
                if($seller_id){
                    if(!in_array($seller_id, $seller_ids)){
                        $seller_ids[] = $seller_id;
                    }
                } else {
                    $product = ProductModel::where('id', $product['product_id'])->first();
                    if($product != null){
                        $seller_id = $product['user_id'];
                    }
                }
            }
            if(count($seller_ids) > 1){
                return false;
            }
            if($seller_id > 0){
                $order_data['order_actual_total'] = $payment_data['amount']['total'] * 0.98;
            } else {
                $order_data['order_actual_total'] = $payment_data['amount']['total'];
            }
            $order_data['seller_id'] = $seller_id;
            $order_data['store_id'] = $store_id;
            $order_model = new OrderModel();
            foreach ($order_data as $key => $value) {
                $order_model->$key = $value;
            }
            $order_model->save();
            if($order_model != null){
                if($payment_data['order_integral'] >0){
                    //积分抵扣
                    $p_content = '积分支付产品, 消费￥' . $payment_data['order_integral'];
                    UserService::getInstance()->userIntegralOut($user, $payment_data['order_integral'], $p_content);
                }
                static::createOrderDetail($order_model, $payment_data['products']);
                static::createOrderUserInfo($order_model, $payment_data['shipping_address']);
            }
            return $order_model;
        });
        if($order != null){
            try{
                $job = (new OrderCancelJobs($order['id']))
                    ->delay(Carbon::now()->addMinutes(100))->onQueue('order');
                dispatch($job);
            } catch(\Exception $e){

            }
            
        }
        return $order;
    }
    /**
     * 生成订单详情
     */
    public static function createOrderDetail($order, $product_item){
        foreach ($product_item as $sku => $product) {
            $order_product_model = new OrderProductModel();
            $order_product_model->order_id = $order->id;
            $order_product_model->product_id = $product['product_id'];
            $order_product_model->sku_id = $product['sku_id'];
            $order_product_model->product_name = $product['product_name'];
            $order_product_model->price = $product['price'];
            $order_product_model->share_integral_amount = $product['share_integral_amount'];
            $order_product_model->quantity = $product['quantity'];
            $order_product_model->spec = $product['spec'];
            $order_product_model->image = $product['image'];
            $order_product_model->share_user_id = $product['share_user_id'];
            $order_product_model->gift_id = !empty($product['gift_id']) ? $product['gift_id'] : '0';
            $order_product_model->save();
        }
    }
     /**
     * 生成订单地址
     */
    public static function createOrderUserInfo($order, $shipping_address){
        $OrderUserInfoModel = new OrderUserInfoModel();
        $OrderUserInfoModel->order_id = $order->id;
        $OrderUserInfoModel->user_id = $order->user_id;
        $OrderUserInfoModel->address_id = isset($shipping_address['id']) ? $shipping_address['id'] : 0;
        $OrderUserInfoModel->fullname = $shipping_address['fullname'];
        $OrderUserInfoModel->phone = $shipping_address['phone'];
        $OrderUserInfoModel->province = $shipping_address['province'];
        $OrderUserInfoModel->city = $shipping_address['city'];
        $OrderUserInfoModel->district = $shipping_address['district'];
        $OrderUserInfoModel->town = $shipping_address['town'];
        $OrderUserInfoModel->village = $shipping_address['village'];
        $OrderUserInfoModel->address = $shipping_address['address'];
        $OrderUserInfoModel->zip = $shipping_address['zip'];
        $OrderUserInfoModel->save();
    }    

    /**
     * 生成编号
     */
    public static function generateOrderNumber($user_id){
        $time_str = date('YmdHis');
        $md5_str = md5(rand(1, 10000) . $user_id);
        $number = $time_str.'P'.substr($md5_str, 0, 5);
        return $number;
    }

     /**
     * 订单支付完成
     *
     * @return void
    */
    public static function orderPayHandel($order)
    {
        if($order->order_status_code != 'pending'){
            return false;
        }
        if($order->order_status_code == 'shipping'){
            return false;
        }
        if($order->payed_at == ''){
            $order->payed_at = date('Y-m-d H:m:s');
        }
        $order->is_pay = '1';

        $order->order_status_code = 'shipping';

        $res = $order->save();

        if($res){
            static::orderPayAccount($order);
        }

        try{
            OrderEmailService::orderSelfPaid($order);
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }

    //订单付款完成逻辑处理
    public static function orderPayAccount($order){
        DB::transaction(function() use ($order) {
            if($order->is_pay_account == '1'){
                return false;
            }
            //销量统计、库存处理
            $order_product = $order->products()->get();
            foreach ($order_product as $pkey => $p) {
                $product = ProductModel::where('id', $p['product_id'])->first();
                if($product != null){
                    $product->sales_numbers = $product->sales_numbers + $p['quantity'];
                    $product->save();
                }
                $sku = ProductSkuModel::where('id', $p['sku_id'])->first();
                if($sku != null){
                    $stock = $sku['stock'] - $p['quantity'];
                    if($stock < 0){
                        $stock = 0;
                    }
                    $sku->stock = $stock;
                    $sku->sales_numbers = $sku->sales_numbers + $p['quantity'];
                    $sku->save();
                }
                if(!$product['is_self']){
                    $store = StoreModel::where('user_id', $product['user_id'])->first();
                    if($store != null){
                        $store->sales_number = $store->sales_number + $p['quantity'];
                        $store->save();
                    }
                }
            }
            //通知
            $title = '订单支付成功';
            //接收人消息
            $content = '订单' . $order['order_no'] . '支付成功,请我王等待，即将进行发货!';
            $data = [
                'user_id' => $order->user_id,
                'message_type' => 'order',
                'name' => $title,
                'content' => $content,
                'order_no' => $order['order_no'],
                'link' => '/account/order/detail/' . $order['order_no']
            ];
            MessageService::insert($data);

            $seller_id = $order->seller_id;

            if($seller_id){
                $title = '店铺收到订单';
                //接收人消息
                $content = '店铺收到订单:' . $order['order_no'] . ', 请进行发货处理!';
                $data = [
                    'user_id' => $seller_id,
                    'message_type' => 'order',
                    'name' => $title,
                    'content' => $content,
                    'order_no' => $order['order_no'],
                    'link' => '/account/store/order/detail/' . $order['order_no']
                ];
                MessageService::insert($data);
                $seller_user = UserModel::where('id', $seller_id)->first();
                if($seller_user != null){
                    $phone = $seller_user['phone'];
                    if($phone != ''){
                        HelperSms::sendOrderShip($phone);
                    }
                }
            }
            $order->is_pay_account = '1';
            $order->save();
        });

        try{
            OrderEmailService::orderSelfPaid($order);
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }

    /**
     * 检查订单是否支付完成
     *
     * @return void
    */
    public static function checkOrderPay($order, $app)
    {
        $is_pay = false;

        $order_no = $order['order_no'];

        $response = $app->payment->query($order_no);
        if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
            if ($response['trade_state'] == 'SUCCESS') {
                $is_pay = true;
            }
        }

        return $is_pay;
    }

    /**
     * 微信付款
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static  function wxPayment($app, $order, $payment_amount){

        $result = [];

        $order_no = $order['order_no'];

        $user = \Auth::user();

        $amount = $payment_amount;

        $is_weixin = 0;

        $data = [];

        $pay_amount = $amount * 100;

        if(config('site.test_store') == '1'){
            static::orderPayHandel($order);
            $data['order_no'] = $order_no;
            $data['is_weixin'] = $is_weixin;
            $result['data'] = $data;
            $result['code'] = "Success";
            return $result;
        }

        if(Helper::isWeixin()){
            $is_weixin = '1';
            $oauth_user = session('wechat.oauth_user');
            $openid = $oauth_user->id;
            $product = [
                'body' => '充值',
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'notify_url' => Helper::route('wx_order_payment_back'), 
                'openid' => $openid
            ];
            $data = static::jsapi($app, $product);
        } else {
            $product = [
                'body' => '充值',
                'nonce_str' => uniqid(),
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'MWEB', // 请对应换成你的支付方式对应的值类型
                'notify_url' => Helper::route('wx_order_payment_back'), 
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
            $data = static::h5($app, $product);
        }
        if($data['prepayId']){
            $order->prepay_id = $data['prepayId'];
            $order->save();
        }
        $data['order_no'] = $order_no;
        $data['is_weixin'] = $is_weixin;
        $result['data'] = $data;
        $result['code'] = "Success";
        return $result;
    }

    //jsapi支付
    public static function jsapi($app, $product){
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
    public static function h5($app, $product){
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

    //微信支付订单记录
    public static function wxOrderRecord($order, $pay_data){
        $OrderWxRecordModel = OrderWxRecordModel::where('order_id', $order->id)
        ->first();
        if($OrderWxRecordModel == null){
            $OrderWxRecordModel = new OrderWxRecordModel();
            $OrderWxRecordModel->order_id = $order->id;
        }
        $OrderWxRecordModel->openid = isset($pay_data['openid']) ? $pay_data['openid'] : '';
        $OrderWxRecordModel->is_subscribe = isset($pay_data['is_subscribe']) ? $pay_data['is_subscribe'] : '';
        $OrderWxRecordModel->trade_type = isset($pay_data['trade_type']) ? $pay_data['trade_type'] : '';
        $OrderWxRecordModel->bank_type = isset($pay_data['bank_type']) ? $pay_data['bank_type'] : '';
        $OrderWxRecordModel->total_fee = isset($pay_data['total_fee']) ? $pay_data['total_fee'] : '';
        $OrderWxRecordModel->fee_type = isset($pay_data['fee_type']) ? $pay_data['fee_type'] : '';
        $OrderWxRecordModel->transaction_id = isset($pay_data['transaction_id']) ? $pay_data['transaction_id'] : '';
        $OrderWxRecordModel->out_trade_no = isset($pay_data['out_trade_no']) ? $pay_data['out_trade_no'] : '';
        $OrderWxRecordModel->attach = isset($pay_data['attach']) ? $pay_data['attach'] : '';
        $OrderWxRecordModel->time_end = isset($pay_data['time_end']) ? $pay_data['time_end'] : '';
        $OrderWxRecordModel->trade_state = isset($pay_data['trade_state']) ? $pay_data['trade_state'] : '';
        $OrderWxRecordModel->cash_fee = isset($pay_data['cash_fee']) ? $pay_data['cash_fee'] : '';
        $OrderWxRecordModel->trade_state_desc = isset($pay_data['trade_state_desc']) ? $pay_data['trade_state_desc'] : '';
        $OrderWxRecordModel->save();
    }

    /**
     * 订单发货
     */
    public static function orderShipped($order, $shipping_data, $op_user = null, $op_admin_user = null){
        $res = DB::transaction(function() use ($order, $shipping_data, $op_user, $op_admin_user) {
            $order->order_status_code = 'shipped';
            $order->shipped_at = date('Y-m-d H:m:s');
            $order->save();
            if($shipping_data != null){
                if($shipping_data['tracknumber']){
                    $OrderShippingModel = new OrderShippingModel();
                    $OrderShippingModel->order_id = $order->id;
                    $OrderShippingModel->shipping_method = $shipping_data['shipping_method'];
                    $OrderShippingModel->tracknumber = $shipping_data['tracknumber'];
                    $OrderShippingModel->save();
                }
            }
            $data = [
                'order_id' => $order['id'],
                'admin_id' => !empty($op_admin_user) ? $op_admin_user->id : 0,
                'user_id' => !empty($op_user) ? $op_user->id: 0,
                'type' => 'order_shipped',
                'content' => '订单已发货!'
            ];
            static::orderStatusRecord($data);
            return true;
        });
        return $res;
    }

    /**
     * 订单完成
     */
    public static function orderFinished($order, $user = null, $admin_user = null){
        $res = DB::transaction(function() use ($order, $user, $admin_user) {
            $order->order_status_code = 'finished';
            $order->done_at = date('Y-m-d H:m:s');
            $order->save();
            $is_self = $order->is_self;
            if($is_self == '1' && $order->order_type != '1'){
                static::orderCommission($order);
            } else {
                static::orderStoreAccount($order);
            }
            $order->is_account = '1';
            $order->save();
            $data = [
                'order_id' => $order['id'],
                'admin_id' => !empty($admin_user) ? $admin_user['id'] : 0,
                'user_id' => !empty($user) ? $user['id'] : 0,
                'type' => 'order_finished',
                'content' => '订单已完成!'
            ];
            static::orderStatusRecord($data);
            return true;
        });
        return $res;
    }


    //卖家订单核算
    public static function orderStoreAccount($order){

        if($order->is_account == '1'){
            return false;
        }
        if($order->is_self == '1'){
            return false;
        }

        $order_product = $order->products()->get();

        $order_share_amount = 0;

        $store_order_amount = [];

        $seller_id = $order['seller_id'];

        if(!$seller_id){
            foreach ($order_product as $okey => $o) {
                $product_id = $o['product_id'];
                $StoreProductModel = StoreProductModel::where('product_id', $product_id)->first();
                if($StoreProductModel != null){
                    $store_id = $StoreProductModel['store_id'];
                    $store = StoreModel::where('id', $store_id)->first();
                    if($store != null){
                        $seller_id = $store['user_id'];
                        break;
                    }
                }
            }

        }

        if(!$seller_id){
            return false;
        }

        $seller_user = UserModel::where('id', $seller_id)->first();

        if($seller_user == null){
            return false;
        }

        $order_share_amount = 0;

        $order_product = $order->products()->get();

        foreach ($order_product as $okey => $o) {
            $price = $o['price'];
            $share_integral_amount = $o['share_integral_amount'];
            if($share_integral_amount > $price){
                $share_integral_amount = $price;
            }
            $quantity = $o['quantity'];
            $order_share_amount += $o['share_integral_amount'] * $quantity;
        }

        $order_integral = $order['order_integral'];

        $order_total = $order->order_total;

        $charge_points = $order->charge_points ? $order->charge_points : 0.02;

        //平台收取0.02
        $self_amount = round($order_total * $charge_points, 2) + $order_share_amount;

        if($order_integral > 0){
            if($order_integral > $order_total){
               $order_integral =  $order_total;
            }
            $order_pay = $order_total - $order_integral;
        } else {
            $order_pay = $order_total;
        }

        if($order_pay > $self_amount){
            $order_pay = $order_pay - $self_amount;
        } else {
            $order_pay = 0;
            $order_integral = $order_integral - ($self_amount - $order_pay);
        }

        
        $c_amount = $order_pay;

        $UserService = UserService::getInstance();

        $c_amount = round($c_amount, 2);

        if($c_amount > 0){
            $content = "收入店铺订单结算 金额:￥$c_amount";
            $UserService->userRewardIncome($seller_user, $c_amount, $content);
        }
        $c_point = round($order_integral, 2);
        if($c_point > 0){
            $content = "收入店铺订单结算 有赏积分:￥$c_point";
            $UserService->userIntegralIncome($seller_user, $c_point, $content, $c_point);
            /*$data = [
                'user_id' => $seller_id,
                'name' => "收获购物订单有赏积分",
                'content' => "禀奏皇上，收获购物订单有赏积分:￥$c_point"
            ];
            MessageService::insert($data);*/
        }

        $link = '/account/store/order/detail/' . $order['order_no'];

        $data = [
            'user_id' => $seller_id,
            'name' => "收入店铺订单结算",
            "order_no" => $order['order_no'],
            "link" => $link,
            'content' => "收入店铺订单结算，订单号：" . $order['order_no']
        ];
        MessageService::insert($data);

        $order_profit_total = $c_amount + $c_point;

        $data = [
            'order_id' => $order['id'],
            'seller_id' => $order->seller_id,
            'order_amount' => $order->order_total,
            'order_profit_total' => $order_profit_total,
            'order_profit_amount' => $c_amount,
            'order_profit_integral' => $c_point
        ];
        static::orderAccountRecord($data);
        static::orderCommission($order);
    }

    //自营订单分润
    public static function orderCommission($order){

        if($order->is_account == '1'){
            return false;
        }

        $user_id = $order->user_id;
        $user = UserModel::where('id', $user_id)->first();
        if($user == null){
            return false;
        }

        $order_share_amount = 0;

        $share_user_id = 0;

        $order_product = $order->products()->get();

        foreach ($order_product as $okey => $o) {
            $price = $o['price'];
            $share_integral_amount = $o['share_integral_amount'];
            if($share_integral_amount > $price){
                $share_integral_amount = $price;
            }
            $quantity = $o['quantity'];
            $order_share_amount += $o['share_integral_amount'] * $quantity;
            if($o['share_user_id'] > 0){
                $share_user_id = $o['share_user_id'];
            }
        }

        if($share_user_id == $order->user_id){
            $share_user_id = 0;
        }

        $share_user = UserModel::where('id', $share_user_id)->first();

        if($share_user == null){
            $share_user_id = 0;
        } else {
            $share_user_phone = $share_user->phone;
            if($share_user_phone == $user->phone){
                $share_user_id = 0;
            }
        }

        if($order_share_amount > $order->order_total){
           $order_share_amount =  $order->order_total * 0.5;
        }

        if($order_share_amount <=0){
            return false;
        }

        //直接推荐人
        $referrer_user_id = $user->referrer_user_id;

        if(!$referrer_user_id || $referrer_user_id <=0){
            $referrer_user = null;
        } else {
            //直接推荐人
            $referrer_user = UserModel::where('id', $referrer_user_id)->first();
        }

        if($referrer_user != null){

            $date = date('Y-m-d H:i:s');

            $UserService = UserService::getInstance();

            if(!empty($referrer_user) && $referrer_user->id == $share_user_id){
                $share_user_id = 0;
            }

            if(empty($share_user_id)){
                $first_amount_ratio = config('order.share_commission.first');
            } else {
                $first_amount_ratio = config('order.share_commission.first1');
            }

            if($referrer_user->is_vip == '1' && $referrer_user->level_status >= 1 && $referrer_user->vip_end_date >= $date){
                $referrer_amount = $order_share_amount * $first_amount_ratio;
                
                $c_amount = sprintf('%.2f', $referrer_amount * 0.95);
                if($c_amount > 0){
                    $content = "收入共享商城 赏金:￥$c_amount";
                    $UserService->userRewardIncome($referrer_user, $c_amount, $content);
                    $order->commission_amount = $order->commission_amount + $referrer_amount;
                    if($order->commission_amount > $order->order_total){
                        $order->commission_amount = $order->order_total;
                    }
                    $order->save();
                    $data = [
                        'user_id' => $referrer_user->id,
                        'name' => "收入赏金",
                        'content' => "收入共享商城订单 赏金:￥$c_amount"
                    ];
                    MessageService::insert($data);
                }
                $c_point = sprintf('%.2f', $referrer_amount * 0.05);
                if($c_point > 0){
                    $content = "收入共享商城 有赏积分:￥$c_point";
                    $UserService->userIntegralIncome($referrer_user, $c_point, $content);
                    $data = [
                        'user_id' => $referrer_user->id,
                        'name' => "收入有赏积分",
                        'content' => "收入共享商城 有赏积分:￥$c_point"
                    ];
                    MessageService::insert($data);
                }
                $comm_data = [
                    'order_id' => $order->id,
                    'user_id' => $referrer_user->id,
                    'user_id' => $referrer_user->id,
                    'amount' => $c_amount,
                    'point' => $c_point,
                    'content' => "商城共享积分订单结算佣金"
                ];
                CommissionService::insert($comm_data);
            }
            $second_amount_ratio = config('order.share_commission.second');
            $second_user_amount = $second_amount_ratio * $order_share_amount;
            if($share_user_id == 0){
                static::secondCommission($order, $referrer_user, $second_user_amount);
            }
        } else {
            if($share_user_id > 0){
                if($share_user->referrer_user_id > 0){
                    $share_ref_user = UserModel::where('id', $share_user->referrer_user_id)->first();
                    if(!empty($share_ref_user) && $share_ref_user['is_vip'] == '1'){
                        $second_amount_ratio = config('order.share_commission.second');
                        $second_user_amount = $second_amount_ratio * $order_share_amount;
                        static::secondCommission($order, $share_user, $second_user_amount);
                    }
                }
            }
        }

        //自购佣金
        $order_user_id = $order->user_id;
        if($order_user_id > 0){
            $self_amount_ratio = config('order.share_commission.self');
            $self_user_amount = $self_amount_ratio * $order_share_amount;
            static::userCommissionPoint($order, $order_user_id, $self_user_amount);
        }

        //分享佣金
        if($share_user_id > 0){
            $share_amount_ratio = config('order.share_commission.share');
            $share_user_amount = $share_amount_ratio * $order_share_amount;
            if(!empty($share_user) && $share_user['is_vip'] == '1'){
                static::userCommission($order, $share_user_id, $share_user_amount);
            } else {
                static::userCommissionPoint($order, $share_user_id, $share_user_amount);
            }
        }
       
    }

    //间接推荐人佣金
    public static function userCommissionPoint($order, $user_id, $amount){

        if($amount <=0){
            return false;
        }

        if($order->is_account == '1'){
            return false;
        }
        
        $UserService = UserService::getInstance();

        $user = UserModel::where('id', $user_id)->first();

        if($user == null){
            return false;
        }
        $sc_point = $amount;
        if($sc_point > 0){
            $content = "收入共享商城 有赏积分:￥$sc_point";
            $UserService->userIntegralIncome($user, $sc_point, $content);
            $data = [
                'user_id' => $user->id,
                'name' => "收入有赏积分",
                'content' => "收入共享商城 有赏积分:￥$sc_point"
            ];
            MessageService::insert($data);
        }
        $order->commission_amount = $order->commission_amount + $amount;
        if($order->commission_amount > $order->order_total){
            $order->commission_amount = $order->order_total;
        }
        $order->save();
        $comm_data = [
            'order_id' => $order->id,
            'user_id' => $user_id,
            'amount' => 0,
            'point' => $sc_point,
            'content' => "自购挣钱积分"
        ];
        CommissionService::insert($comm_data);
    }

    //间接推荐人佣金
    public static function userCommission($order, $user_id, $amount){
        
        if($amount <=0){
            return false;
        }

        if($order->is_account == '1'){
            return false;
        }
        
        $UserService = UserService::getInstance();

        $user = UserModel::where('id', $user_id)->first();

        if($user == null){
            return false;
        }

        $sc_amount = sprintf('%.2f', $amount * 0.95);
        if($sc_amount > 0){
            $content = "收入共享商城 赏金:￥$sc_amount";
            $UserService->userRewardIncome($user, $sc_amount, $content);
            $data = [
                'user_id' => $user->id,
                'name' => "收入赏金",
                'content' => "收入共享商城 赏金:￥$sc_amount"
            ];
            MessageService::insert($data);
        }
        $sc_point = sprintf('%.2f', $amount * 0.05);
        if($sc_point > 0){
            $content = "收入共享商城 有赏积分:￥$sc_point";
            $UserService->userIntegralIncome($user, $sc_point, $content);
            $data = [
                'user_id' => $user->id,
                'name' => "收入有赏积分",
                'content' => "收入共享商城 有赏积分:￥$sc_point"
            ];
            MessageService::insert($data);
        }
        $order->commission_amount = $order->commission_amount + $amount;
        if($order->commission_amount > $order->order_total){
            $order->commission_amount = $order->order_total;
        }
        $order->save();
        $comm_data = [
            'order_id' => $order->id,
            'user_id' => $user_id,
            'amount' => $sc_amount,
            'point' => $sc_point,
            'content' => "自营商城共享积分订单结算佣金"
        ];
        CommissionService::insert($comm_data);
    }

    //间接推荐人佣金
    public static function secondCommission($order, $referrer_user, $order_share_amount){

        if($order_share_amount <=0){
            return false;
        }

        if($order->is_account == '1'){
            return false;
        }
        
        $UserService = UserService::getInstance();

        $secend_referrer_user_id = $referrer_user->referrer_user_id;

        $secend_referrer_user = UserModel::where('id', $secend_referrer_user_id)->first();

        if($secend_referrer_user == null){
            return false;
        }
        if($secend_referrer_user->is_vip != '1' && $secend_referrer_user->level_status < 1){
            return false;
        }

        if($order->seller_id == $secend_referrer_user->id){
            //return false;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($secend_referrer_user->vip_end_date < $date){
            return false;
        }

        $secend_referrer_amount = $order_share_amount;
        $sc_amount = sprintf('%.2f', $secend_referrer_amount * 0.95);
        if($sc_amount > 0){
            $content = "收入共享商城 赏金:￥$sc_amount";
            $UserService->userRewardIncome($secend_referrer_user, $sc_amount, $content);
            $data = [
                'user_id' => $secend_referrer_user->id,
                'name' => "收入赏金",
                'content' => "收入共享商城 赏金:￥$sc_amount"
            ];
            MessageService::insert($data);
        }
        $sc_point = sprintf('%.2f', $secend_referrer_amount * 0.05);
        if($sc_point > 0){
            $content = "收入共享商城 有赏积分:￥$sc_point";
            $UserService->userIntegralIncome($secend_referrer_user, $sc_point, $content);
            $data = [
                'user_id' => $secend_referrer_user->id,
                'name' => "收入有赏积分",
                'content' => "收入共享商城 有赏积分:￥$sc_point"
            ];
            MessageService::insert($data);
        }
        $order->commission_amount = $order->commission_amount + $secend_referrer_amount;
        if($order->commission_amount > $order->order_total){
            $order->commission_amount = $order->order_total;
        }
        $order->save();
        $comm_data = [
            'order_id' => $order->id,
            'user_id' => $secend_referrer_user->id,
            'amount' => $sc_amount,
            'point' => $sc_point,
            'content' => "自营商城共享积分订单结算佣金"
        ];
        CommissionService::insert($comm_data);
    }

    /**
     * 取消订单
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public static function cancelOrder($order, $app){
        $result = ['code' => '2x1'];
        if($order == null){
            $result['message'] = '对不起，订单不存在!';
            return $result;
        }

        if($order['order_status_code'] == 'cancel'){
            $result['message'] = '对不起，订单已取消!';
            return $result;
        }

        if($order['order_status_code'] == 'shipped'){
            $result['message'] = '对不起，订单已发货!';
            return $result;
        }

        if($order['order_status_code'] == 'finished'){
            $result['message'] = '对不起，订单已完成!';
            return $result;
        }

        if($order['order_status_code'] != 'pending'){
            $result['message'] = '对不起，订单不能取消!';
            return $result;
        }

        $is_pay = 0;

        $order_integral = $order['order_integral'];

        if($order_integral < $order['order_total']){
            if($order['payment_method'] == 'weixin'){
                $is_pay = static::checkOrderPay($order, $app);
                if($is_pay){
                    $result['message'] = '对不起，订单不能取消!';
                    return $result;
                }
            }
        }

        $OrderRefundModel = \DB::transaction(function() use ($order, $is_pay) {
            if($order->refund_status == '2'){
                return false;
            }
            $order->refund_status = '1';
            $order->order_status_code = 'cancel';
            $order->cancel_at = date('Y-m-d H:i:s');
            $order->save();
            if(!$is_pay){
                $user_id = $order->user_id;
                $user = UserModel::where('id', $user_id)->first();
                if($order['order_integral'] > 0){
                    $order_integral = $order['order_integral'];
                    //返回积分
                    $content = '取消订单，返回积分￥' . $order_integral;

                    UserService::getInstance()->userIntegralIncome($user, $order_integral, $content);
                }
                $order->refund_status = '2';
                $order->save();
                return null;
            } else {
                $reason = '用户取消订单';
                $OrderRefundModel = static::createOrderRefund($order, $reason);
                return $OrderRefundModel;
            }
        });

        $result['code'] = 'Success';
        $result['message'] = '订单取消成功!';

        $OrderRefundModel = OrderRefundModel::where('order_id', '=', $order['id'])->first();

        if($OrderRefundModel && $OrderRefundModel['is_account'] == '1'){
            return $result;
        }

        if($OrderRefundModel != null){
            if($is_pay){
                $reason = '用户取消订单';
                $refundNo = $OrderRefundModel->refundsn;
                $refundFee = $OrderRefundModel->amount;
                $order_no = $order['order_no'];
                $response = $app->payment->refund($order_no, $refundNo, $order['order_total'] * 100, $refundFee * 100);
                if($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS'){
                    static::refundAccount($order, $OrderRefundModel);
                } else {
                    $OrderRefundModel->status = '1';
                    $result['message'] = '订单已取消，申请退款中！';
                }
            }
        }
        return $result;
    }

    /**
     * 退款处理
     * @param  [type] $order            [description]
     * @param  [type] $OrderRefundModel [description]
     * @param  [type] $app              [description]
     * @param  [type] $refundFee        [description]
     * @return [type]                   [description]
     */
    public static function refundHandel($order, $OrderRefundModel, $app, $refundFee){
        $result = ['status' => '0'];
        $order_integral = $order['order_integral'];
        if($order_integral == $order['order_total']){
            static::refundAccount($order, $OrderRefundModel);
            $result['message'] = '退换单已处理！';
            $result['status'] = '1';
            return $result;
        }
        $OrderRefundModel->status = '1';
        $OrderRefundModel->save();
        $order->refund_status = '1';
        $order->save();
        $response = $app->payment->refund($order['order_no'], $OrderRefundModel['refundsn'], $order['payment_amount'] * 100, $refundFee * 100);
        if($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS'){
            static::refundAccount($order, $OrderRefundModel);
            $result['status'] = '1';
            $result['message'] = '退换单已处理！';
        } else {
            $result['message'] = '退换单已处理，申请退款中！';
        }
        return $result;
    }

    /**
     * 退款成功核算
     * @param  [type] $order            [description]
     * @param  [type] $OrderRefundModel [description]
     * @return [type]                   [description]
     */
    public static function refundAccount($order, $OrderRefundModel){
        if($OrderRefundModel && $OrderRefundModel['is_account'] == '1'){
            return false;
        }
        $res = \DB::transaction(function() use ($order, $OrderRefundModel) {
            $user_id = $order->user_id;
            $user = UserModel::where('id', $user_id)->first();
            $OrderRefundModel['status'] = '2';
            $OrderRefundModel->save();
            if($order['order_integral'] > 0){
                $order_integral = $order['order_integral'];
                //返回积分
                $content = '取消订单，返回积分￥' . $order_integral;

                UserService::getInstance()->userIntegralIncome($user, $order_integral, $content);
            }
            $OrderRefundModel->is_account = '1';
            $OrderRefundModel->save();
            $order->refund_status = '2';
            $order->cancel_at = date('Y-m-d H:i:s');
            $order->order_status_code = 'cancel';
            $order->save();
            return true;
        });
        return $res;
    }

    /**
     * 生成退款记录
     * @param  [type] $order  [description]
     * @param  [type] $reason [description]
     * @return [type]         [description]
     */
    public static function createOrderRefund($order, $reason){
        $OrderRefundModel = OrderRefundModel::where('order_id', '=', $order['id'])->first();
        if($OrderRefundModel == null){
            $OrderRefundModel = new OrderRefundModel();
            $refundsn = static::generateOrderRefundNumber($order['user_id']);
            $OrderRefundModel->order_id = $order['id'];
            $OrderRefundModel->currency = $order['currency'];
            $OrderRefundModel->order_id = $order['id'];
            $OrderRefundModel->refundsn = $refundsn;
            $OrderRefundModel->user_id = $order['user_id'];
            $amount = $order['order_total'] - $order['order_integral'];
            $OrderRefundModel->amount = $amount;
            $OrderRefundModel->integral_amount = $order['order_integral'];
            $OrderRefundModel->reason = $reason;
            $OrderRefundModel->save();
        }
        return $OrderRefundModel;
    }

     /**
     * 生成退款单号
     */
    public static function generateOrderRefundNumber($user_id){
        $time_str = date('YmdHis');
        $md5_str = md5(rand(1, 10000) . $user_id);
        $number = 'P' . $time_str.substr($md5_str, 0, 10);
        return $number;
    } 

    /**
     * 订单详情
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public static function getOrderDetail($order){
        $order_products = $order->products()->get();
        $is_self = 0;
        $order_store = null;
        foreach ($order_products as $okey => $order_product) {
            $product = $order_product->product()->first();
            if($product['is_self']){
                $is_self = 1;
            }
            $image = $order_product['image'];
            if(empty($image)){
                $order_product_sku = $order_product->sku()->first();
                $image = !empty($order_product_sku) ? $order_product_sku['image'] : '';
            }
            if(!empty($image)){
                $image = \HelperImage::storagePath($image);
            }
            $order_product->image = $image;
            if($is_self != '1' && $order_store == null){
                $store_product = StoreProductModel::where('product_id', '=', $order_product['product_id'])->first();
                if($store_product != null){
                    $order_store = StoreModel::where('id', $store_product['store_id'])->first();
                }
            }
        }
        $order['order_store'] = $order_store;
        if($order_products != null){
            $order_products = $order_products->toArray();
        }
        $order['order_products'] = $order_products;
        $order_userinfo = $order->userinfo()->first();
        if($order_userinfo != null){
            $order_userinfo = $order_userinfo->toArray();
        }
        $order['is_self'] = $is_self;
        $order['user_info'] = $order_userinfo;

        $order_shipping = $order->shipping()->first();

        if($order_shipping != null){
            $order_shipping = $order_shipping->toArray();
        }

        $pay_remaining_time = 0;
        if($order['order_status_code'] == 'pending'){
            $pay_remaining_time = static::getPayRemainingTime($order);
        }

        $order->pay_remaining_time = $pay_remaining_time;

        $order['shipping_info'] = $order_shipping;

        $refund = OrderRefundModel::where('order_id', $order->id)->whereIn('status', ['0', '1', '2'])
        ->orderBy('id', 'desc')
        ->first();
        if($refund != null){
            $refund = $refund->toArray();
        }
        $order['refund'] = $refund;

        return $order;
    }


    /**
     * 订单列表
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public static function getOrderListDetail($order){
        $order_product = $order->products()
        ->select('order_product.*')->first();
        if($order['order_status_code'] == 'shipped' || $order['order_status_code'] == 'finished'){
            $order_shipping = $order->shipping()->first();
            if($order_shipping != null){
                $order_shipping = $order_shipping->toArray();
            }
            $order->shipping_info = $order_shipping;
        }
        $pay_remaining_time = 0;
        if($order['order_status_code'] == 'pending'){
            $pay_remaining_time = static::getPayRemainingTime($order);
        }
        $order->pay_remaining_time = $pay_remaining_time;
        if($order_product != null){
            $image = !empty($order_product) ? $order_product['image'] : '';
            if(!empty($image)){
                $image = \HelperImage::storagePath($image);
            }
            $order_product->image = $image;
            $order_product = $order_product->toArray();
        }
        $order->product = $order_product;
        return $order;
    }

    /**
     * //支付剩余时间
     * @param   $order 
     * @return int
     */
    public static function getPayRemainingTime($order){
        $pay_remaining_time = 0;
        if($order['order_status_code'] == 'pending'){
            //待付款时间限制
            $now = time();
            $created_at = strtotime($order['created_at']);
            $order_pay_limit_time = config('order.pay_limit_time', 7200);
            $end_time = $created_at + $order_pay_limit_time;
            $pay_remaining_time = ($end_time - $now);
            $pay_remaining_time = $pay_remaining_time > 0 ? $pay_remaining_time : 0;
        }
        return $pay_remaining_time;
    }

    //订单状态记录
    public static function orderStatusRecord($data){
        $OrderStatusRecord = new OrderStatusRecord();
        $OrderStatusRecord->order_id = $data['order_id'];
        $OrderStatusRecord->admin_id = $data['admin_id'] ? $data['admin_id'] : 0;
        $OrderStatusRecord->user_id = $data['user_id'] ? $data['user_id'] : 0;
        $OrderStatusRecord->type = $data['type'];
        $OrderStatusRecord->content = $data['content'];
        $OrderStatusRecord->save();
    }

    //订单核算记录
    public static function orderAccountRecord($data){
        $OrderAccountRecord = OrderAccountRecord::where('order_id', $data['order_id'])->first();
        if($OrderAccountRecord != null){
            return false;
        }
        $OrderAccountRecord = new OrderAccountRecord();
        $OrderAccountRecord->order_id = $data['order_id'];
        $OrderAccountRecord->seller_id = $data['seller_id'];
        $OrderAccountRecord->order_amount = $data['order_amount'];
        $OrderAccountRecord->order_profit_total = $data['order_profit_total'];
        $OrderAccountRecord->order_profit_amount = $data['order_profit_amount'];
        $OrderAccountRecord->order_profit_integral = $data['order_profit_integral'];
        $OrderAccountRecord->save();
    }
}   