<?php
namespace App\Libs\Service;

use Hash;
use Auth;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\User\VipPackage as VipPackageModel;
use App\Models\Order\Recharge as OrderRecharge;  
use App\Models\Order\RechargeWxRecord as RechargeWxRecordModel;
use EasyWeChat;
use EasyWeChat\Payment\Order;
use App\Models\Store\StorePackage as StorePackageModel;
use App\Models\Store\Store as StoreModel;
use App\Models\User\IntegralSend;
use App\Models\Site\Config as SiteConfig;
use App\Models\Product\Product as ProductModel;
use App\Models\User\Address as AddressModel;
use App\Models\Gift\Gift as GiftModel;
use App\Models\Order\OrderRechargeRefund;
use App\Cache\Product as ProductCache;
use EasyWeChat\Foundation\Application;

class OrderRechargeService
{
    /**
     * vip付款
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static  function vipPayment($app, $request){

        $result = [];

        $user = Auth::user();

        $user_id = $user->id;

        $vippackage = VipPackageModel::where('enable', '=', '1')->where('year', '=', '1')->first();

        if($vippackage == null){
            $result['code'] = '';
            $result['message'] = '';
            return $result;
        }

        $amount = $vippackage->amount;

        if($user->is_vip && $user->vip_end_date){
            $vip_type = '2';
            $vip_remarks = '续费vip';
        } else {
            $vip_type = '1';
            $vip_remarks = '开通vip';
        }

        $OrderRecharge = OrderRecharge::where('order_type', '=', 'vip')
        ->where('user_id', '=', $user->id)->where('status', '=', '1')
        ->where('vip_type', '=', $vip_type)
        ->where('amount', $amount)
        ->first();

        $new_order = false;

        $product_id = $request->product_id;

        $gift_id = $request->gift_id;

        $product = null;

        if($product_id > 0){
            $product = ProductModel::where('id', '=', $product_id)->first();
            if(!empty($product)){
                 //获取用户默认选择地址
                $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
                if($address == null){
                    //如果用户没有设置默认地址,获取第一条
                    $address = AddressModel::where('user_id', '=', $user_id)->first();
                }
                if(empty($address)){
                    $result['message'] = '请先添加地址';
                    $result['code'] = "2x1";
                    return $result;
                }
            } 
        }

        if($OrderRecharge != null){
            $created_at = $OrderRecharge->created_at;
            $h = date('Y-m-d H:i:s', strtotime('-1hour'));
            if($h > $created_at){
                $new_order = true;
            }
            if($product_id > 0 && !empty($product)){
                $OrderRecharge->product_id = $product_id;
                $OrderRecharge->address_id = !empty($address) ? $address['id'] : '';
                $OrderRecharge->gift_id = $gift_id;
            }
        }

        if($OrderRecharge == null || $new_order){
            $OrderRecharge = new OrderRecharge();
            $OrderRecharge->order_no = static::generateOrderNumber($user->id, 'V');
            $OrderRecharge->user_id = $user->id;
            $OrderRecharge->order_type = 'vip';
            $OrderRecharge->payment_method = 'weixin';
            if($product_id > 0  && !empty($product)){
                $OrderRecharge->product_id = $product_id;
                $OrderRecharge->address_id = !empty($address) ? $address['id'] : '';
                $OrderRecharge->gift_id = $gift_id;
            }
        }

        $OrderRecharge->vip_type = $vip_type;
        $OrderRecharge->remarks = $vip_remarks;

        $OrderRecharge->amount = $amount;

        $r = $OrderRecharge->save();
        
        $order_no = $OrderRecharge->order_no;

        $is_weixin = 0;

        $data = [];

        $pay_amount = $amount * 100;

        if(config('site.test_vip') == '1'){
            static::payVipOrder($OrderRecharge->order_no);
            $data['order_no'] = $order_no;
            $data['mweb_url'] = Helper::route('account_index');
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
                'notify_url'         => Helper::route('wx_vip_payment_back'), 
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
                'notify_url' => Helper::route('wx_vip_payment_back'), 
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
            $OrderRecharge->prepay_id = $data['prepayId'];
            $OrderRecharge->save();
        }
        $data['order_no'] = $order_no;
        $data['is_weixin'] = $is_weixin;
        $result['data'] = $data;
      	$result['code'] = "Success";
        return $result;
    }


    /**
     * vip升级付款
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static  function vipUpgradePayment($app, $request){

        $result = [];

        $upgrade_type = $request->upgrade_type ? $request->upgrade_type : 1;

        $rupgrade_user_id = 0;

        //代购开通
        if($upgrade_type == '2'){
            $uid = $request->uid;
            $user = UserModel::where('u_id', '=', $uid)->first();
            if(empty($user)){
                return;
            }
            $user_id = $user->id;
            $rupgrade_user = Auth::user();
            $rupgrade_user_id = $rupgrade_user->id;
        } else {
            $user = Auth::user();
            $user_id = $user->id;
        }

          //获取用户默认选择地址
        $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
        if($address == null){
            //如果用户没有设置默认地址,获取第一条
            $address = AddressModel::where('user_id', '=', $user_id)->first();
        } 

        $address_data = '';

        if(empty($address)){
            $address_data = $request->address_data;
        }

        if($upgrade_type == '2' && empty($address)){
            //验证数据
            $validator = Validator::make($address_data, [
                'fullname' => 'required|string',
                'phone' => 'required|string',
                'province_id' => 'required',
                'city_id' => 'required',
                'district_id' => 'required',
                'town_id' => 'required',
                'village_id' => 'required',
                'province' => 'required',
                'city' => 'required',
                'district' => 'required',
                'town' => 'required',
                'village' => 'required',
                'address' => 'required|string',
                'zip' => 'required|string'
            ]);
            
            if($validator->fails()){
                $result['code'] = 'INVALID_DATA';
                $result['message'] = "请检查您的地址信息";
                return response()->json($result);
            }
            $address_data = json_encode($address_data);
        }

      

        $gift_id = $request->gift_id;

        $gift = GiftModel::where('gift_type', 'vip')->where('id', $gift_id)
        ->where('enable', '1')
        ->where('deleted', '!=', '1')
        ->first();

        if($gift == null){
            $result['code'] = '2x1';
            $result['message'] = '此礼包产品已下架,请选择其他礼包产品下单！';
            return $result;
        }

        $amount = $gift->price;

        $use_integral = $request->use_integral;

        $sub_integral_amount_use = 0;

        if($upgrade_type == '2' && $use_integral == '1'){
            $rupgrade_user = Auth::user();
            $sub_integral_amount = $rupgrade_user->sub_integral_amount;
            $sub_integral_amount_use = $sub_integral_amount;
            if($sub_integral_amount_use > 200){
                $sub_integral_amount_use = 200;
            }
            if($sub_integral_amount_use > $amount){
                $sub_integral_amount_use = 200;
            }
        }

        $reward_amount_use = 0;

        $use_reward = $request->use_reward;

        if($upgrade_type == '2' && $use_reward == '1'){
            $rupgrade_user = Auth::user();
            $reward_amount = 0;
            //赏金
            $reward = $rupgrade_user->reward()->first();
            if($reward != null){
               $reward = $reward->toArray();
               $reward_amount = $reward['amount'];
            }
            $reward_amount = $reward['amount'];
            $freeze_amount = $reward['freeze_amount'];
            if($freeze_amount <=0){
                $freeze_amount = 0;
            }
            $reward_amount = $reward_amount - $freeze_amount;
            if($reward_amount <=0){
                $reward_amount = 0;
            }
            $amount_total = $amount - $sub_integral_amount_use;
            if($reward_amount > $amount_total){
                $reward_amount_use = $amount_total;
            } else {
                $reward_amount_use = $reward_amount;
            }
        }

        if($user->vip_end_date){
            $vip_type = '2';
            $vip_remarks = '续费vip';
        } else {
            $vip_type = '1';
            $vip_remarks = '开通vip';
        }

        $product_id = $gift->product_id;

        $product_sku_id = $request->product_sku_id;

        $product = ProductModel::where('id', '=', $product_id)->first();

        if(empty($product)){
            $result['message'] = '此礼包产品已下架';
            $result['code'] = "2x1";
            return $result;
        }

        $product_sku = $product->skus()->where('id', $product_sku_id)
        ->where('is_sale', '1')
        ->where('deleted', '!', '1')
        ->first();

        if(empty($product_sku)){
            $result['message'] = '此礼包产品已下架';
            $result['code'] = "2x1";
            return $result;
        }

        if(empty($product_sku['stock'])){
            $result['message'] = '此礼包产品规格已售罄，请选择其他规格或者其他礼包产品';
            $result['code'] = "2x1";
            return $result;
        }

        //获取用户默认选择地址
        $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
        if($address == null){
            //如果用户没有设置默认地址,获取第一条
            $address = AddressModel::where('user_id', '=', $user_id)->first();
        }
        if($upgrade_type == '2'){
            if(empty($address) && empty($address_data)){
                $result['message'] = '请先添加地址';
                $result['code'] = "2x1";
                return $result;
            }
        } else {
            if(empty($address)){
                $result['message'] = '请先添加地址';
                $result['code'] = "2x1";
                return $result;
            }
        }

        $address_id = !empty($address) ? $address['id'] : 0;
        

        $OrderRecharge = OrderRecharge::where('order_type', '=', 'vip')
        ->where('user_id', '=', $user_id)->where('status', '=', '1')
        ->where('vip_type', '=', $vip_type)
        ->where('amount', $amount)
        ->where('upgrade_type', $upgrade_type)
        ->where('rupgrade_user_id', $rupgrade_user_id)
        ->where('sub_integral_amount', $sub_integral_amount_use)
        ->where('reward_amount', $reward_amount_use)
        ->where('product_sku_id', $product_sku_id)
        ->where('address_data', $address_data)
        ->where('gift_id', $gift_id)
        ->first();

        $new_order = true;

        if($OrderRecharge != null){
            $created_at = $OrderRecharge->created_at;
            $h = date('Y-m-d H:i:s', strtotime('-1hour'));
            if($h > $created_at){
                $new_order = true;
            }
        }

        if($OrderRecharge == null || $new_order){
            $OrderRecharge = new OrderRecharge();
            $OrderRecharge->order_no = static::generateOrderNumber($user->id, 'V');
            $OrderRecharge->user_id = $user->id;
            $OrderRecharge->order_type = 'vip';
            $OrderRecharge->payment_method = 'weixin';
        }

        $OrderRecharge->sub_integral_amount = $sub_integral_amount_use;

        $OrderRecharge->reward_amount = $reward_amount_use;

        $OrderRecharge->product_id = $product_id;
        $OrderRecharge->product_sku_id = $product_sku_id;
        $OrderRecharge->address_id = $address['id'];
        $OrderRecharge->address_data = $address_data;
        $OrderRecharge->gift_id = $gift_id;

        $OrderRecharge->upgrade_type = $upgrade_type;

        $OrderRecharge->rupgrade_user_id = $rupgrade_user_id;

        $OrderRecharge->vip_type = $vip_type;
        $OrderRecharge->remarks = $vip_remarks;

        $OrderRecharge->amount = $amount;

        $OrderRecharge->gold_amount = $gift->gold_amount;

        $pay_amount = $amount - $sub_integral_amount_use - $reward_amount_use;

        if(config('site.test_vip') == '1'){
            $pay_amount = 0.00;
        }

        if($pay_amount <0){
            $pay_amount = 0.00;
        }

        $OrderRecharge->payment_amount = $pay_amount;

        if(!empty($gift)){
            $OrderRecharge->gift_data = json_encode($gift->toArray());
        }

        $r = $OrderRecharge->save();
        
        $order_no = $OrderRecharge->order_no;

        $is_weixin = 0;

        $data = [];

        $pay_amount = $pay_amount * 100;

        if(config('site.test_vip') == '1' || $pay_amount <=0){
            static::payVipOrder($OrderRecharge->order_no);
            $data['order_no'] = $order_no;
            $data['mweb_url'] = Helper::route('account_index');
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
                'notify_url'         => Helper::route('wx_vip_payment_back'), 
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
                'notify_url' => Helper::route('wx_vip_payment_back'), 
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
            $OrderRecharge->prepay_id = $data['prepayId'];
            $OrderRecharge->save();
        }
        $data['order_no'] = $order_no;
        $data['is_weixin'] = $is_weixin;
        $result['data'] = $data;
        $result['code'] = "Success";
        return $result;
    }


    /**
     * 积分付款
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static function integralPayment($app, $request){

        $result = [];

        $user = Auth::user();

        $amount = $request->amount;

        if($amount <=0){
            $result['message'] = '对不起,金额必须大于0';
            $result['code']  = '2x1';
            return response()->json($result);
        }

        $OrderRecharge = OrderRecharge::where('order_type', '=', 'integral')
        ->where('user_id', '=', $user->id)->where('status', '=', '1')
        ->where('amount', '=', $amount)
        ->first();

        $new_order = false;

        if($OrderRecharge != null){
            $created_at = $OrderRecharge->created_at;
            $h = date('Y-m-d H:i:s', strtotime('-1hour'));
            if($h > $created_at){
                $new_order = true;
            }
        }


        if($OrderRecharge == null || $new_order){
            $OrderRecharge = new OrderRecharge();
            $OrderRecharge->order_no = static::generateOrderNumber($user->id, 'I');
            $OrderRecharge->payment_method = 'weixin';
        }

        $OrderRecharge->order_type = 'integral';

        $OrderRecharge->remarks = '积分充值';

        $OrderRecharge->user_id = $user->id;

        $OrderRecharge->amount = $amount;

        $r = $OrderRecharge->save();
        
        $order_no = $OrderRecharge->order_no;

        $is_weixin = 0;

        $data = [];

        $pay_amount = $amount * 100;

        if(Helper::isWeixin()){
            $is_weixin = '1';
            $oauth_user = session('wechat.oauth_user');
            $openid = $oauth_user->id;
            $product = [
                'body' => '充值',
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'notify_url'         => Helper::route('wx_integral_payment_back'), 
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
                'notify_url' => Helper::route('wx_integral_payment_back'), 
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
            $OrderRecharge->prepay_id = $data['prepayId'];
            $OrderRecharge->save();
        }
        $data['order_no'] = $order_no;
        $data['is_weixin'] = $is_weixin;
        $result['data'] = $data;
        $result['code'] = "Success";
        return $result;
    }


     /**
     * 名片付款
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static function cardRenewalPayment($app, $request){

        $result = [];

        $user = Auth::user();

        $amount = 0.5;

        $OrderRecharge = new OrderRecharge();

        $OrderRecharge->order_no = static::generateOrderNumber($user->id, 'C');

        $OrderRecharge->order_type = 'card_renewal';

        $OrderRecharge->remarks = '名片续费';

        $OrderRecharge->user_id = $user->id;

        $OrderRecharge->amount = $amount;

        $r = $OrderRecharge->save();
        
        $order_no = $OrderRecharge->order_no;

        $OrderRecharge->payment_method = 'weixin';

        $is_weixin = 0;

        $data = [];

        $pay_amount = $amount * 100;

        if(Helper::isWeixin()){
            $is_weixin = '1';
            $oauth_user = session('wechat.oauth_user');
            $openid = $oauth_user->id;
            $product = [
                'body' => '充值',
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'notify_url'         => Helper::route('wx_card_renewal_payment_back'), 
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
                'notify_url' => Helper::route('wx_card_renewal_payment_back'), 
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
            $OrderRecharge->prepay_id = $data['prepayId'];
            $OrderRecharge->save();
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
        $RechargeWxRecordModel = RechargeWxRecordModel::where('order_id', $order->id)
        ->first();
        if($RechargeWxRecordModel == null){
            $RechargeWxRecordModel = new RechargeWxRecordModel();
            $RechargeWxRecordModel->order_id = $order->id;
        }
        $RechargeWxRecordModel->openid = isset($pay_data['openid']) ? $pay_data['openid'] : '';
        $RechargeWxRecordModel->is_subscribe = isset($pay_data['is_subscribe']) ? $pay_data['is_subscribe'] : '';
        $RechargeWxRecordModel->trade_type = isset($pay_data['trade_type']) ? $pay_data['trade_type'] : '';
        $RechargeWxRecordModel->bank_type = isset($pay_data['bank_type']) ? $pay_data['bank_type'] : '';
        $RechargeWxRecordModel->total_fee = isset($pay_data['total_fee']) ? $pay_data['total_fee'] : '';
        $RechargeWxRecordModel->fee_type = isset($pay_data['fee_type']) ? $pay_data['fee_type'] : '';
        $RechargeWxRecordModel->transaction_id = isset($pay_data['transaction_id']) ? $pay_data['transaction_id'] : '';
        $RechargeWxRecordModel->out_trade_no = isset($pay_data['out_trade_no']) ? $pay_data['out_trade_no'] : '';
        $RechargeWxRecordModel->attach = isset($pay_data['attach']) ? $pay_data['attach'] : '';
        $RechargeWxRecordModel->time_end = isset($pay_data['time_end']) ? $pay_data['time_end'] : '';
        $RechargeWxRecordModel->trade_state = isset($pay_data['trade_state']) ? $pay_data['trade_state'] : '';
        $RechargeWxRecordModel->cash_fee = isset($pay_data['cash_fee']) ? $pay_data['cash_fee'] : '';
        $RechargeWxRecordModel->trade_state_desc = isset($pay_data['trade_state_desc']) ? $pay_data['trade_state_desc'] : '';
        $RechargeWxRecordModel->save();
    }


    /**
     * 生成订单编号
     */
    public static function generateOrderNumber($user_id, $prefix = ''){
        $time_str = date('ymdHis');
        $md5_str = md5(uniqid().$user_id);
        $number = $prefix . $time_str .  substr($md5_str, 0, 10);
        return $number;
    }

    public static function payVipOrder($order_no){

        $order = OrderRecharge::where('order_no', '=', $order_no)->first();
        if (!$order) { // 如果订单不存在
            return true; // 返回处理完成
        }
        $successful = 1;
        if($successful){
            //订单支付成功
            OrderRechargeService::vipOrderPay($order);
            try{
                //OrderRechargeService::wxOrderRecord($order, $notify);
            } catch(Exception $e){}
            return true; // 返回处理完成
        } else {
            $order->faild_at = date('Y-m-d H:m:s');
            $order->save(); // 保存订单
        }
        return true; // 返回处理完成
    }

    //订单支付完成操作
    public static function vipOrderPay($order){
        //已经核算
        if($order->is_account == '1'){
            return false;
        }
        if(empty($order->paid_at)){
            // 更新支付时间为当前时间
            $order->paid_at = date('Y-m-d H:m:s'); 
        }
        if($order->status != '2'){
            //不是已经支付状态则修改为已经支付状态
            $order->status = '2';
        }
        $order_res = $order->save(); // 保存订单 
        if(!$order_res){
            \Log::info('vipOrderPay Save Error!');
            return false;
        }
        static::vipOrderAccount($order);
        
    }


     /**
     * 核算vip订单
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public static function vipOrderAccount($order){
        //未支付
        if($order->status == '1'){
            return false;
        }
        //已经核算
        if($order->is_account == '1'){
            return false;
        }
        //更新vip状态
        $user = UserModel::where('id', '=', $order->user_id)->first();

        if($order->sub_integral_amount > 0){
            $rupgrade_user_id = $order->rupgrade_user_id;
            $rupgrade_user = UserModel::where('id', $rupgrade_user_id)->first();
            $sub_integral_amount = $rupgrade_user->sub_integral_amount;
            if($sub_integral_amount < $order->sub_integral_amount){
                $result['message'] = '代购积分不够';
                $result['code'] = "2x1";
                static::refundHandel($order);
                return $result;
            }
        }

        if($order->reward_amount > 0){
            $rupgrade_user_id = $order->rupgrade_user_id;
            $rupgrade_user = UserModel::where('id', $rupgrade_user_id)->first();
             $reward_amount = 0;
            //赏金
            $reward = $rupgrade_user->reward()->first();
            if($reward != null){
               $reward = $reward->toArray();
               $reward_amount = $reward['amount'];
            }
            $reward_amount = $reward['amount'];
            $freeze_amount = $reward['freeze_amount'];
            if($freeze_amount <=0){
                $freeze_amount = 0;
            }
            $reward_amount = $reward_amount - $freeze_amount;
            if($reward_amount <=0){
                $reward_amount = 0;
            }
            if($reward_amount < $order->reward_amount){
                $result['message'] = '余额不够';
                $result['code'] = "2x1";
                static::refundHandel($order);
                return $result;
            }
        }
        if(empty($user->vip_end_date)){
            $order['vip_type'] = '1';
        } else {
            $order['vip_type'] = '2';
        }
        if($order['vip_type'] == '1'){
            $vip_res = UserService::getInstance()->openVip($user);
        } else {
            $vip_res = UserService::getInstance()->renewalVIP($user);
        }
        //开通成功,核算订单
        if($vip_res){
            $res = \DB::transaction(function() use ($user, $order) {
                $vip_type = $order->vip_type;
                if($vip_type == '1'){
                    UserService::getInstance()->updateVipLevel($user);
                }
                StoreService::paymentStore($user);
                if($order->product_id > 0){
                    static::productOrder($user, $order);
                }
                if($order->sub_integral_amount > 0){
                    $rupgrade_user_id = $order->rupgrade_user_id;
                    $rupgrade_user = UserModel::where('id', $rupgrade_user_id)->first();
                    $sub_integral_amount = $rupgrade_user->sub_integral_amount;
                    $sub_integral_amount = $sub_integral_amount - $order->sub_integral_amount;
                    $rupgrade_user->sub_integral_amount = $sub_integral_amount;
                    $rupgrade_user->save();
                    UserService::getInstance()->userSubIntegralRecord($rupgrade_user, [
                        "type" => '2',
                        "amount" => $order->sub_integral_amount,
                        "content" => '代购开通VIP消费的代购积分',
                        "remarks" => '代购开通VIP消费的代购积分',
                        'order_recharge_id' => $order->id
                    ]);
                }
                if($order->reward_amount > 0){
                    $rupgrade_user_id = $order->rupgrade_user_id;
                    $rupgrade_user = UserModel::where('id', $rupgrade_user_id)->first();
                    UserService::getInstance()->userRewardOut($rupgrade_user, $order->reward_amount, '代购开通VIP抵扣');
                }
                $gift_id = $order->gift_id;
                $gift = GiftModel::where('id', $gift_id)->first();
                $sub_integral = $gift->sub_integral;
                $_user_integral_amount = $user->sub_integral_amount;
                $_user_integral_amount += $sub_integral;
                $user->sub_integral_amount = $_user_integral_amount;
                $user->save();
                $gift_commission = $gift->gift_commission;
                UserService::getInstance()->userSubIntegralRecord($user, [
                    "type" => '1',
                    "amount" => $sub_integral,
                    "content" => '开通VIP赠送代购积分',
                    "remarks" => '开通VIP赠送代购积分',
                    'order_recharge_id' => $order->id
                ]);
                UserService::getInstance()->userCommissionIn($user, $gift_commission, '开通VIP赠送礼包麦粒', $order->id);
                //订单核算
                static::vipOrderCommission($user, $order, $gift);
                $order->is_account = '1';
                $order->save();
                return true;
            });
        }
    }

    /**
     * 核算vip订单
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public static function vipOrderAccountOld($order){
        //未支付
        if($order->status == '1'){
            return false;
        }
        //已经核算
        if($order->is_account == '1'){
            return false;
        }
        //更新vip状态
        $user = UserModel::where('id', '=', $order->user_id)->first();
        if($order['vip_type'] == '1'){
            $vip_res = UserService::getInstance()->openVip($user);
        } else {
            $vip_res = UserService::getInstance()->renewalVIP($user);
        }
        //开通成功,核算订单
        if($vip_res){
            $res = \DB::transaction(function() use ($user, $order) {
                $vip_type = $order->vip_type;
                if($vip_type == '1'){
                    UserService::getInstance()->updateVipLevel($user);
                }
                if($order->product_id > 0){
                    static::productOrder($user, $order);
                }
                //订单核算
                static::vipOrderCommission($user, $order);
                $order->is_account = '1';
                $order->save();
                return true;
            });
            try{
                if($res == true && $order['is_account'] == '1') {
                    if($order['vip_type'] == '1') {
                        \DB::transaction(function() use ($user, $order) {
                            static::vipSendIntegral($user, $order);
                        });
                        \DB::transaction(function() use ($user, $order) {
                            EquityService::vipEquity($order, $user);
                        });
                    }
                }
            } catch(\Exception $e){
                \Log::info($e->getMessage());
            }
        }
    }

    public static function productOrder($user, $order_recharge){
        $total_amount = 0;
        $product_id = $order_recharge->product_id;
        $product_sku_id = $order_recharge->product_sku_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product != null){
            $product_sku = $product->skus()->where('id', $product_sku_id)->first();
            $price = $product_sku['price'];
            $gift_id = $order_recharge->gift_id;
            $gift = GiftModel::where('id', $gift_id)->first();
            if(!empty($gift)){
                $price = $gift['price'];
            }
            $order_currency = 'CNY';
            $subtotal = $price;
            $total_amount = $price;
            $products = [];
            $spec = ProductDispalyService::findProductSkuSpc($product_sku);
            $products[] = [
                'is_self' => $product['is_self'],
                'spu' => $product['spu'],
                'product_name' => $product['name'],
                'product_id' => $product['id'],
                'sku_id' => $product_sku['id'],
                'sku' => $product_sku['sku'],
                'spec' => $spec,
                'image' => $product_sku['image'],
                'quantity' => 1,
                'price' => $price,
                'share_integral_amount' => 0,
                'seller_id' => 0,
                'store_id' => 0,
                'share_user_id' => 0,
                'gift_id' => $order_recharge->gift_id
            ];

            $address_id = $order_recharge->address_id;
            if($order_recharge->address_data){
                $shipping_address = json_decode($order_recharge->address_data, true);
            } else {
                $shipping_address = AddressModel::find($address_id);
                if($shipping_address == null){
                    return false;
                }
                if($shipping_address != null){
                    $shipping_address = $shipping_address->toArray();
                }
            }
            
            $payment_data = [
                'order_buyer_id' => $user->id,
                'order_item_qty' => 1,
                'order_ipaddress' => \Helper::getIPAddress(),
                'order_device' => \Helper::device(),
                'order_currency' => $order_currency,
                'amount' => [
                    'currency' => $order_currency,
                    'total' => $total_amount,
                    'details' => [
                        'subtotal' => $subtotal,
                        'shipping' => 0
                    ]
                ],
                'products' => $products,
                'shipping_address' => $shipping_address,
                'comment' => '',
                'order_integral' => 0,
                'payment_amount' => 0,
                'payment_method' => '',
                'is_self' => 1,
                'order_type' => '1'
            ];
            $order = OrderService::createOrder($payment_data, $user);
            $order->order_status_code = 'shipping';
            $order->payed_at = date('Y-m-d H:i:s');
            $order->order_recharge_id = $order_recharge->id;
            $order->is_pay = 1;
            $order->save();
            OrderService::orderPayAccount($order);
        }
        
    }

    //开通vip自动送积分
    public static function vipSendIntegral($user, $order){
        $config = SiteConfig::first();
        if($config != null){
            $vip_integral_send_open = $config->vip_integral_send_open;
            if($vip_integral_send_open == 1){
                $vip_integral_send_amount = $config->vip_integral_send_amount;
                if($vip_integral_send_amount > 0){
                    $user_id = $user->id;
                    $IntegralSendModel = IntegralSend::where('user_id', $user_id)->where('type', 'vip')->first();
                    if($IntegralSendModel == null){
                        $remarks = 'vip活动赠送';
                        UserService::getInstance()->userIntegralIncome($user, $vip_integral_send_amount, '商城活动赠送', 0, $remarks, $order->id);
                        $IntegralSendModel = new IntegralSend();
                        $IntegralSendModel->user_id = $user_id;
                        $IntegralSendModel->type = 'vip';
                        $IntegralSendModel->integral = $vip_integral_send_amount;
                        $IntegralSendModel->content = '商城活动赠送';
                        $r = $IntegralSendModel->save();
                        if($r){
                            $data = [
                                'user_id' => $user_id,
                                'name' => "商城活动赠送积分",
                                'content' => "商城活动赠送积分： ￥" .$vip_integral_send_amount
                            ];
                            MessageService::insert($data);
                        }
                    }
                }
            }
        }
    }


     //vip订单核算
    public static function vipOrderCommission($user, $order, $gift){
        //未支付
        if($order->status == '1'){
            return false;
        }
        //已经核算
        if($order->is_account == '1'){
            return false;
        }
        //直接推荐人佣金
        static::firstVipCommission($user, $order, $gift);
        //间接推荐人佣金
        static::secondVipCommission($user, $order, $gift);
        //总监、经理佣金
        static::managerVipCommission($user, $order, $gift);
        //间接推荐人id
        $second_referrer_user_id = $user->second_referrer_user_id;
        if(!$second_referrer_user_id || $second_referrer_user_id <=0){
            return false;
        }
        //间接推荐人
        $second_referrer_user = UserModel::where('id', $second_referrer_user_id)->first();
        if($second_referrer_user == null){
            return false;
        }
        static::integralCommissionTree($second_referrer_user, $order);
    }


    //vip订单核算
    public static function vipOrderCommission1($user, $order, $gift){
        //未支付
        if($order->status == '1'){
            return false;
        }
        //已经核算
        if($order->is_account == '1'){
            return false;
        }
       //直接推荐人佣金
        static::firstVipCommission($user, $order);
        //间接推荐人佣金
        static::secondVipCommission($user, $order);
        //间接推荐人id
        $second_referrer_user_id = $user->second_referrer_user_id;
        if(!$second_referrer_user_id || $second_referrer_user_id <=0){
            return false;
        }
        //间接推荐人
        $second_referrer_user = UserModel::where('id', $second_referrer_user_id)->first();
        if($second_referrer_user == null){
            return false;
        }
        static::integralCommissionTree($second_referrer_user, $order);
    }

    public static function managerVipCommission($user, $order, $gift){

        $director_id = $user->director_id;

        if($director_id > 0 && $gift['director_commission'] > 0){
            $director_user = UserModel::where('id', '=', $director_id)->first();
            if(!empty($director_user) && $director_user['is_vip']){
                $director_commission = $gift['director_commission'];
                $content = "收入稻田管理积分:￥$director_commission";
                $remarks = '收入稻田管理积分';
                UserService::getInstance()->userCommissionIn($director_user, 0, $remarks, $order->id, $gift['director_commission']);
                $order->commission_amount = $order->commission_amount + $gift['director_commission'];
                $order->save();
                $data = [
                    'user_id' => $director_user->id,
                    'name' => "收入稻田管理积分",
                    'content' => "收入稻田管理积分" . $director_commission
                ];
                MessageService::insert($data);
            }
            
        }

        $manager_id = $user->manager_id;

        if($manager_id > 0 && $gift['manager_commission'] > 0){
            $manager_user = UserModel::where('id', '=', $manager_id)->first();
            if(!empty($manager_user) && $manager_user['is_vip']){
                $manager_commission = $gift['manager_commission'];
                $content = "收入稻田管理积分:￥$manager_commission";
                $remarks = '收入稻田管理积分';
                UserService::getInstance()->userCommissionIn($manager_user, 0, $remarks, $order->id, $gift['manager_commission']);
                $order->commission_amount = $order->commission_amount + $gift['manager_commission'];
                $order->save();
                $data = [
                    'user_id' => $manager_user->id,
                    'name' => "收入稻田管理积分",
                    'content' => "收入稻田管理积分" .$manager_commission
                ];
                MessageService::insert($data);
            }
            
        }
    }

    //vip订单直接推荐人佣金
    public static function firstVipCommission($user, $order, $gift){

        //已经核算
        if($order->is_account == '1'){
            return false;
        }

        //直接推荐人
        $referrer_user_id = $user->referrer_user_id;

        if(!$referrer_user_id || $referrer_user_id <=0){
            return false;
        }

        //推荐人
        $referrer_user = UserModel::where('id', $referrer_user_id)->first();
        if($referrer_user == null){
            return false;
        }

        if($referrer_user->is_vip != '1' || $referrer_user->level_status < 1){
            return false;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($referrer_user->vip_end_date < $date){
            return false;
        }

        //订单金额
        $amount = $order->amount;

        //已扣佣金
        $commission_amount = $order->commission_amount;
        if($commission_amount <0){
            $commission_amount = 0;
        }

        //可用的佣金
        $able_amount = $amount - $commission_amount;

        if($able_amount <=0){
            $able_amount = 0;
            return false;
        }
        $vip_type = $order->vip_type;
        if($referrer_user->level_status == 1){
            $comm_amount = $gift['first_gift_commission_1'];
        }
        if($referrer_user->level_status >= 2){
            $comm_amount = $gift['first_gift_commission_2'];
        }
        if($comm_amount > $amount){
            //$comm_amount = $amount;
        }
        if($order->vip_type != '1'){
            $comm_amount = 0;
        }
        $c_amount =  round($comm_amount * 0.95, 2);
        $c_point = round($comm_amount * 0.05, 2);
        $UserService = UserService::getInstance();
        $vip_tip = '活跃用户综合评价';
        if($vip_type == '2'){
            //$vip_tip = '续费vip';
        } else {
            //$vip_tip = '开通vip';
        }
        if($c_amount > 0){
            $content = "收入" . $vip_tip . "有赏赏金:￥$c_amount";
            $remarks = 'vip直接推荐礼包麦粒';
            UserService::getInstance()->userCommissionIn($referrer_user, $c_amount, $remarks, $order->id);
            $order->commission_amount = $order->commission_amount + $comm_amount;
            if($order->commission_amount > $order->amount){
                //$order->commission_amount = $order->amount;
            }
            $order->save();
            $data = [
                'user_id' => $referrer_user->id,
                'name' => "收入礼包麦粒",
                'content' => "收入" . $vip_tip ."礼包麦粒:￥$c_amount"
            ];
            MessageService::insert($data);
        }

        if($referrer_user->level_status == 1){
            $comm_reward = $gift['first_gift_reward_1'];
        }
        if($referrer_user->level_status >= 2){
            $comm_reward = $gift['first_gift_reward_2'];
        }

        if($comm_reward > 0){
            $c_point += round($comm_reward * 0.05, 2);
        }

        if($c_point > 0){
            $content = "收入". $vip_tip ."有赏积分:￥$c_point";
            $remarks = 'vip直接推荐有赏积分';
            $UserService->userIntegralIncome($referrer_user, $c_point, $content, 0, $remarks, $order->id);
            $data = [
                'user_id' => $referrer_user->id,
                'name' => "收入有赏积分",
                'content' => "收入". $vip_tip ."有赏积分:￥$c_point"
            ];
            MessageService::insert($data);
        }

        

        if($comm_reward > 0){
            $comm_reward_amount = $comm_reward * 0.95;
            $content = "收入" . $vip_tip. "有赏赏金:￥$comm_reward_amount";
            $remarks = 'vip直接推荐推荐赏金';
            $UserService->userRewardIncome($referrer_user, $comm_reward_amount, $content, $remarks, $order->id);
            $order->commission_amount = $order->commission_amount + $comm_reward;
            if($order->commission_amount > $order->amount){
                //$order->commission_amount = $order->amount;
            }
            $order->save();
            $data = [
                'user_id' => $referrer_user->id,
                'name' => "收入有赏赏金",
                'content' => "收入" .$vip_tip ."有赏赏金:￥$comm_reward_amount"
            ];
            MessageService::insert($data);
        }
    }

     //vip订单间接推荐人佣金
    public static function secondVipCommission($user, $order, $gift){

        //已经核算
        if($order->is_account == '1'){
            return false;
        }

        //间接推荐人id
        $second_referrer_user_id = $user->second_referrer_user_id;
        if(!$second_referrer_user_id || $second_referrer_user_id <=0){
            return false;
        }

        //间接推荐人
        $second_referrer_user = UserModel::where('id', $second_referrer_user_id)->first();
        if($second_referrer_user == null){
            return false;
        }

        if($second_referrer_user->is_vip != '1' || $second_referrer_user->level_status < 1){
            return false;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($second_referrer_user->vip_end_date < $date){
            return false;
        }

        //订单金额
        $amount = $order->amount;

        //已扣佣金
        $commission_amount = $order->commission_amount;
        if($commission_amount <0){
            $commission_amount = 0;
        }

        //可用的佣金
        $able_amount = $amount - $commission_amount;

        if($able_amount <0){
            $able_amount = 0;
            //return false;
        }
        
        $c_amount = 0;
        $c_point = 0;
        $comm_amount = 0;
        $vip_type = $order->vip_type;
        $vip_tip = '活跃用户综合评价';
        if($vip_type == '2'){
            //$vip_tip = '续费vip';
        } else {
           // $vip_tip = '开通vip';
        }
        if($second_referrer_user->is_vip == '1'){
            if($second_referrer_user->level_status == 1){
                $comm_amount = $gift['secend_gift_commission_1'];
            }
            if($second_referrer_user->level_status >= 2){
                $comm_amount = $gift['secend_gift_commission_2'];
            }
            if($comm_amount > $able_amount){
                //$comm_amount = $able_amount;
            }
            $c_amount =  round($comm_amount * 0.95 , 2);
            $c_point = round($comm_amount * 0.05, 2);
            $UserService = UserService::getInstance();
            if($c_amount > 0){
                /*$content = "收入" . $vip_tip. "有赏赏金:￥$c_amount";
                $remarks = 'vip间接推荐赠送礼包麦粒';
                UserService::getInstance()->userCommissionIn($second_referrer_user, $c_amount, $remarks, $order->id);
                $order->commission_amount = $order->commission_amount + $comm_amount;
                if($order->commission_amount > $order->amount){
                    $order->commission_amount = $order->amount;
                }
                $order->save();
                $data = [
                    'user_id' => $second_referrer_user->id,
                    'name' => "收入礼包麦粒",
                    'content' => "收入" .$vip_tip ."礼包麦粒:￥$c_amount"
                ];
                MessageService::insert($data);*/
                $content = "收入" . $vip_tip. "有赏赏金:￥$c_amount";
                $remarks = 'vip间接推荐赏金';
                $UserService->userRewardIncome($second_referrer_user, $c_amount, $content, $remarks, $order->id);
                $order->commission_amount = $order->commission_amount + $comm_amount;
                if($order->commission_amount > $order->amount){
                    //$order->commission_amount = $order->amount;
                }
                $order->save();
                $data = [
                    'user_id' => $second_referrer_user->id,
                    'name' => "收入有赏赏金",
                    'content' => "收入" .$vip_tip ."有赏赏金:￥$c_amount"
                ];
                MessageService::insert($data);
            }
            if($c_point > 0){
                $content = "收入" .$vip_tip ."有赏积分:$c_point";
                $remarks = 'vip间接推荐有赏积分';
                $UserService->userIntegralIncome($second_referrer_user, $c_point, $content, 0, $remarks, $order->id);
                $data = [
                    'user_id' => $second_referrer_user->id,
                    'name' => "收入有赏积分",
                    'content' => "收入" .$vip_tip ."有赏积分:￥$c_point"
                ];
                MessageService::insert($data);
            }
        }
    }

    //vip订单直接推荐人佣金
    public static function firstVipCommission1($user, $order){

        //已经核算
        if($order->is_account == '1'){
            return false;
        }

        //直接推荐人
        $referrer_user_id = $user->referrer_user_id;

        if(!$referrer_user_id || $referrer_user_id <=0){
            return false;
        }

        //推荐人
        $referrer_user = UserModel::where('id', $referrer_user_id)->first();
        if($referrer_user == null){
            return false;
        }

        if($referrer_user->is_vip != '1' || $referrer_user->level_status < 1){
            return false;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($referrer_user->vip_end_date < $date){
            return false;
        }

        //订单金额
        $amount = $order->amount;

        //已扣佣金
        $commission_amount = $order->commission_amount;
        if($commission_amount <0){
            $commission_amount = 0;
        }

        //可用的佣金
        $able_amount = $amount - $commission_amount;

        if($able_amount <=0){
            $able_amount = 0;
            return false;
        }
        $vip_type = $order->vip_type;
        if($vip_type == '2'){
            if($referrer_user->level_status == 1){
                $comm_amount = config('user.renewalVIP.Commission1');
            }
            if($referrer_user->level_status == 2){
                $comm_amount = config('user.renewalVIP.Commission2');
            }
            if($referrer_user->level_status > 2){
                $comm_amount = config('user.renewalVIP.Commission3');
            }
        } else {
            $comm_amount = config('user.vip.firstCommission1');
        }
        
        if($comm_amount > $able_amount){
            $comm_amount = $able_amount;
        }
        if($comm_amount > $amount){
            $comm_amount = $amount;
        }
        $c_amount =  round($comm_amount * 0.95, 2);
        $c_point = round($comm_amount * 0.05, 2);
        $UserService = UserService::getInstance();
        $vip_tip = '活跃用户综合评价';
        if($vip_type == '2'){
            //$vip_tip = '续费vip';
        } else {
            //$vip_tip = '开通vip';
        }
        if($c_amount > 0){
            $content = "收入" . $vip_tip . "有赏赏金:￥$c_amount";
            $remarks = 'vip直接推荐赏金';
            $UserService->userRewardIncome($referrer_user, $c_amount, $content, $remarks, $order->id);
            $order->commission_amount = $order->commission_amount + $comm_amount;
            if($order->commission_amount > $order->amount){
                $order->commission_amount = $order->amount;
            }
            $order->save();
            $data = [
                'user_id' => $referrer_user->id,
                'name' => "收入赏金",
                'content' => "收入" . $vip_tip ."有赏赏金:￥$c_amount"
            ];
            MessageService::insert($data);
        }
        if($c_point > 0){
            $content = "收入". $vip_tip ."有赏积分:￥$c_point";
            $remarks = 'vip直接推荐有赏积分';
            $UserService->userIntegralIncome($referrer_user, $c_point, $content, 0, $remarks, $order->id);
            $data = [
                'user_id' => $referrer_user->id,
                'name' => "收入有赏积分",
                'content' => "收入". $vip_tip ."有赏积分:￥$c_point"
            ];
            MessageService::insert($data);
        }
    }

    //vip订单间接推荐人佣金
    public static function secondVipCommission1($user, $order){

        //已经核算
        if($order->is_account == '1'){
            return false;
        }

        //间接推荐人id
        $second_referrer_user_id = $user->second_referrer_user_id;
        if(!$second_referrer_user_id || $second_referrer_user_id <=0){
            return false;
        }

        //间接推荐人
        $second_referrer_user = UserModel::where('id', $second_referrer_user_id)->first();
        if($second_referrer_user == null){
            return false;
        }

        if($second_referrer_user->is_vip != '1' || $second_referrer_user->level_status < 1){
            return false;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($second_referrer_user->vip_end_date < $date){
            return false;
        }

        //订单金额
        $amount = $order->amount;

        //已扣佣金
        $commission_amount = $order->commission_amount;
        if($commission_amount <0){
            $commission_amount = 0;
        }

        //可用的佣金
        $able_amount = $amount - $commission_amount;

        if($able_amount <0){
            $able_amount = 0;
            return false;
        }
        
        $c_amount = 0;
        $c_point = 0;
        $comm_amount = 0;
        $vip_type = $order->vip_type;
        $vip_tip = '活跃用户综合评价';
        if($vip_type == '2'){
            //$vip_tip = '续费vip';
        } else {
           // $vip_tip = '开通vip';
        }
        if($second_referrer_user->is_vip == '1'){
            if($vip_type == '2'){
                if($second_referrer_user->level_status == 1){
                    $comm_amount = config('user.renewalVIP.Commission1');
                }
                if($second_referrer_user->level_status == 2){
                    $comm_amount = config('user.renewalVIP.Commission2');
                }
                if($second_referrer_user->level_status >2){
                    $comm_amount = config('user.renewalVIP.Commission3');
                }
            }
            else if($second_referrer_user->level_status == 1){
                $comm_amount = config('user.vip.secondCommission1');
            }
            else if($second_referrer_user->level_status > 1){
                $comm_amount = config('user.vip.secondCommission2');
            }
            if($comm_amount > $able_amount){
                $comm_amount = $able_amount;
            }
            $c_amount =  round($comm_amount * 0.95 , 2);
            $c_point = round($comm_amount * 0.05, 2);
            $UserService = UserService::getInstance();
            if($c_amount > 0){
                $content = "收入" . $vip_tip. "有赏赏金:￥$c_amount";
                $remarks = 'vip间接推荐赏金';
                $UserService->userRewardIncome($second_referrer_user, $c_amount, $content, $remarks, $order->id);
                $order->commission_amount = $order->commission_amount + $comm_amount;
                if($order->commission_amount > $order->amount){
                    $order->commission_amount = $order->amount;
                }
                $order->save();
                $data = [
                    'user_id' => $second_referrer_user->id,
                    'name' => "收入有赏赏金",
                    'content' => "收入" .$vip_tip ."有赏赏金:￥$c_amount"
                ];
                MessageService::insert($data);
            }
            if($c_point > 0){
                $content = "收入" .$vip_tip ."有赏积分:$c_point";
                $remarks = 'vip间接推荐有赏积分';
                $UserService->userIntegralIncome($second_referrer_user, $c_point, $content, 0, $remarks, $order->id);
                $data = [
                    'user_id' => $second_referrer_user->id,
                    'name' => "收入有赏积分",
                    'content' => "收入" .$vip_tip ."有赏积分:￥$c_point"
                ];
                MessageService::insert($data);
            }
        }
    }

    public static function integralCommissionTree($user, $order){
        $i = 1;
        while($user && $i <=5){
            $user = static::integralCommission($i, $user, $order);
            if($user == false){
                break;
            }
            $i++;
        }
    }

    public static function integralCommission($i, $user, $order){
        $referrer_user_id = $user->referrer_user_id;
        //推荐人
        $referrer_user = UserModel::where('id', $referrer_user_id)->first();
        if($referrer_user == null){
            return false;
        }
        if($referrer_user->is_vip != '1' || $referrer_user->level_status < 1){
            return $referrer_user;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($referrer_user->vip_end_date < $date){
            return $referrer_user;
        }
        if($i >= 4){
            if($referrer_user->level_status < 2){
                return $referrer_user;
            }
        }
        if($i == 6){
            if($referrer_user->level_status >= 2){
                return $referrer_user;
            }
        }
        $UserService = UserService::getInstance();
        $vip_tip = '活跃用户综合评价';
        $comm_amount = 20;
        $content = "收入" .$vip_tip ."有赏积分:$comm_amount";
        $remarks = 'vip活跃有赏积分';
        $UserService->userIntegralIncome($referrer_user, $comm_amount, $content, 0, $remarks, $order->id);
        $data = [
            'user_id' => $referrer_user->id,
            'name' => "收入有赏积分",
            'content' => "收入" .$vip_tip ."有赏积分:￥$comm_amount"
        ];
        MessageService::insert($data);
        $order->commission_amount = $order->commission_amount + $comm_amount;
        if($order->commission_amount > $order->amount){
            $order->commission_amount = $order->amount;
        }
        $order->save();
        return $referrer_user;
    }


    //积分订单支付完成
    public static function integralOrderPay($order){
        if($order->is_account == '1'){
           return false;
        }
        if(empty($order->paid_at)){
            // 更新支付时间为当前时间
            $order->paid_at = date('Y-m-d H:m:s'); 
        }
        if($order->status != '2'){
            //不是已经支付状态则修改为已经支付状态
            $order->status = '2';
        }
        $order_res = $order->save(); // 保存订单 
        if(!$order_res){
            \Log::info('integralOrderPay Save Error!');
            return false;
        }
        \DB::transaction(function() use ($order) {
            //订单用户
            $user = UserModel::where('id', '=', $order->user_id)->first(); 
            if($user == null){
                return false;
            }
            $point = $order->amount;
            $content = '充值积分 增加￥' . $point;
            UserService::getInstance()->userIntegralIncome($user, $point, $content, 0, '充值积分', $order->id);
            $order->is_account = '1';
            $order->save();
        });
    }

    //名片续费订单支付完成
    public static function cardRenewalOrderPay($order){
        //已经核算
        if($order->is_account == '1'){
           return false;
        }
        if(empty($order->paid_at)){
            // 更新支付时间为当前时间
            $order->paid_at = date('Y-m-d H:m:s'); 
        }
        if($order->status != '2'){
            //不是已经支付状态则修改为已经支付状态
            $order->status = '2';
        }
        $order_res = $order->save(); // 保存订单 
        if(!$order_res){
            \Log::info('cardRenewal Save Error!');
            return false;
        }
        \DB::transaction(function() use ($order) {
            //订单用户
            $user = UserModel::where('id', '=', $order->user_id)->first(); 
            if($user == null){
                return false;
            }
            $user->card_can_add = $user->card_can_add + 6;
            $user->save();
            $order->is_account = '1';
            $order->save();
        });
    }

    /**
     * 店铺付款
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static  function storePayment($app, $request){

        $result = [];

        $user = Auth::user();

        $user_id = $user->id;

        $store_package = StorePackageModel::where('enable', '=', '1')->first();

        if($store_package == null){
            $result['code'] = '';
            $result['message'] = '';
            return $result;
        }

        $product_id = $request->product_id;

        $gift_id = $request->gift_id;

        $product = null;

        $amount = $store_package->amount;

        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if(empty($store) || $store['expire_date'] == null){
            $type = 'open';
            $remarks = '开通店铺';
        } else {
            $type = 'renewal';
            $remarks = '店铺续费';
        }

        $order_no = $request->order_no;

        if(!empty($order_no)){
            $OrderRecharge = OrderRecharge::where('order_type', '=', 'store')
            ->where('user_id', '=', $user->id)
            ->where('order_no', $order_no)
            ->first();
            if($OrderRecharge != null && $OrderRecharge['status'] == '2'){
                $result['code'] = 'Success';
                $result['data'] = ['is_pay' => '1', 'order_no' => $order_no];
                $result['message'] = '';
                return $result;
            }
        }

        $OrderRecharge = OrderRecharge::where('order_type', '=', 'store')
        ->where('user_id', '=', $user->id)
        ->where('status', '=', '1')
        ->where('amount', $amount)
        ->orderBy('id', 'desc')
        ->first();

        $new_order = false;

        if($OrderRecharge != null){
            $created_at = $OrderRecharge->created_at;
            $h = date('Y-m-d H:i:s', strtotime('-1hour'));
            if($h > $created_at){
                $new_order = true;
            }
        }

        if(empty($OrderRecharge) || $new_order){
            $OrderRecharge = new OrderRecharge();
            $OrderRecharge->order_no = static::generateOrderNumber($user->id, 'S');
            $OrderRecharge->user_id = $user->id;
            $OrderRecharge->order_type = 'store';
            $OrderRecharge->payment_method = 'weixin';
        }

        $OrderRecharge->remarks = $remarks;

        $OrderRecharge->amount = $amount;

        if($product_id > 0){
            $product = ProductModel::where('id', '=', $product_id)->first();
            if(!empty($product)){
                 //获取用户默认选择地址
                $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
                if($address == null){
                    //如果用户没有设置默认地址,获取第一条
                    $address = AddressModel::where('user_id', '=', $user_id)->first();
                }
                if(empty($address)){
                    $result['message'] = '请先添加地址';
                    $result['code'] = "2x1";
                    return $result;
                }
            } 
        }

        if($product_id > 0 && !empty($product)){
            $OrderRecharge->product_id = $product_id;
            $OrderRecharge->address_id = !empty($address) ? $address['id'] : '';
            $OrderRecharge->gift_id = $gift_id;
        }

        $r = $OrderRecharge->save();
        
        $order_no = $OrderRecharge->order_no;

        $is_weixin = 0;

        $data = [];

        $pay_amount = $amount * 100;

        if(config('site.test_store') == '1'){
           static::storeOrderPay($OrderRecharge);
            $data['order_no'] = $order_no;
            $data['mweb_url'] = Helper::route('checkout_store_success', [$order_no]);
            $result['data'] = $data;
            $result['code'] = "Success";
            return $result;
        }

        if(\Helper::isWeixin()){
            $is_weixin = '1';
            $oauth_user = session('wechat.oauth_user');
            $openid = $oauth_user->id;
            $product = [
                'body' => '充值',
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'notify_url'         => Helper::route('wx_store_payment_back'), 
                'openid' => $openid
            ];
            $data = static::jsapi($app, $product);
        } else {
            $result['d'] = '2';
            $product = [
                'body' => '充值',
                'nonce_str' => uniqid(),
                'out_trade_no' => $order_no,
                'total_fee' => $pay_amount,
                'trade_type' => 'MWEB', // 请对应换成你的支付方式对应的值类型
                'notify_url' => Helper::route('wx_store_payment_back'), 
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
        if(isset($data['prepayId'])){
            if($data['prepayId'] == ''){
                $result['message'] = '订单已失效！请重新支付！';
                $result['code'] = "2x1";
            }
            $OrderRecharge->prepay_id = $data['prepayId'];
            $OrderRecharge->save();
        }
        $data['order_no'] = $order_no;
        $data['is_weixin'] = $is_weixin;
        $result['data'] = $data;
        $result['code'] = "Success";

        return $result;
    }


    /*
    店铺订单支付成功
     */
    public static function storeOrderPay($order){
        if($order->is_account == '1'){
            return false;
        }
        if(empty($order->paid_at)){
            // 更新支付时间为当前时间
            $order->paid_at = date('Y-m-d H:m:s'); 
        }
        if($order->status != '2'){
            //不是已经支付状态则修改为已经支付状态
            $order->status = '2';
        }
        $order_res = $order->save(); // 保存订单 
        if(!$order_res){
            \Log::info('storeOrderPay Save Error!');
            return false;
        }
        $is_open = false;
        //订单用户
        $user = UserModel::where('id', '=', $order->user_id)->first(); 
        if($user == null){
            return false;
        }
        $res = \DB::transaction(function() use ($order, $user, &$is_open) {
            
            //直接推荐人佣金
            static::firstStoreCommission($user, $order);
            //间接推荐人佣金
            static::secondStoreCommission($user, $order);
            if($user->store_level < 1){
                $user->store_level = '1';
                $user->save();
            }
            if($user->is_vip != '1'){
                $next_time = strtotime(date("Y-m-d", strtotime("+1 day")));
                $expire_time = strtotime('+3year +31 day', $next_time);
                $vip_end_date = date('Y-m-d H:i:s', $expire_time);
                UserService::getInstance()->autoOpenVip($user, $vip_end_date);
                UserService::getInstance()->updateVipLevel($user);
            }
            $store_data = StoreService::paymentStore($user);
            if(isset($store_data['is_open']) && $store_data['is_open']){
                $is_open = true;
            }
            //间接推荐人id
            $second_referrer_user_id = $user->second_referrer_user_id;
            if($second_referrer_user_id > 0){
                //间接推荐人
                $second_referrer_user = UserModel::where('id', $second_referrer_user_id)->first();
                if($second_referrer_user != null){
                    static::storeCommissionTree($second_referrer_user, $order);
                }
            }
            if($order->product_id > 0){
                static::productOrder($user, $order);
            }
            $order->is_account = '1';
            $order->save();
            return true;
        });

        try{
            if($res && $order['is_account'] == '1' && $is_open) {
                \DB::transaction(function() use ($user, $order) {
                    static::storeSendIntegral($user, $order);
                });
                \DB::transaction(function() use ($user, $order) {
                    EquityService::storeEquity($order, $user);
                });
            }
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }

    //直接推荐人店铺佣金
    public static function firstStoreCommission($user, $order){

        if($order->is_account == '1'){
           return false;
        }

        //直接推荐人
        $referrer_user_id = $user->referrer_user_id;

        if(!$referrer_user_id || $referrer_user_id <=0){
            return false;
        }

        //推荐人
        $referrer_user = UserModel::where('id', $referrer_user_id)->first();
        if($referrer_user == null){
            return false;
        }

        if($referrer_user->is_vip != '1' || $referrer_user->level_status < 1){
            return false;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($referrer_user->vip_end_date < $date){
            return false;
        }

        //订单金额
        $amount = $order->amount;

        //已扣佣金
        $commission_amount = $order->commission_amount;
        if($commission_amount <0){
            $commission_amount = 0;
        }

        //可用的佣金
        $able_amount = $amount - $commission_amount;

        if($able_amount <=0){
            $able_amount = 0;
            return false;
        }

        $commission_config = config('store.commission');

        $comm_amount = 0;


        $store = StoreModel::where('user_id', $referrer_user_id)->where('is_pay', '1')->first();


        if(empty($store)){
            $comm_amount = $commission_config[0]['first'];
        } else {
            if($referrer_user->store_level == '0'){
                $comm_amount = $commission_config['0']['first'];
            }
            if($referrer_user->store_level == '1'){
                $comm_amount = $commission_config['1']['first'];
            }
            if($referrer_user->store_level == '2'){
                $comm_amount = $commission_config['2']['first'];
            }
        }

        if($comm_amount > $able_amount){
            $comm_amount = $able_amount;
        }
        if($comm_amount > $amount){
            $comm_amount = $amount;
        }

        $c_amount =  round($comm_amount * 0.95, 2);

        $c_point = round($comm_amount * 0.05, 2);

        $UserService = UserService::getInstance();

        //$store_tip = '店铺缴费';
        
        $store_tip = '店铺活跃综合评价';

        if($c_amount > 0){
            
            $content = "收入" . $store_tip . "赏金:￥$c_amount";
            $remarks = '店铺直接推荐赏金';
            $UserService->userRewardIncome($referrer_user, $c_amount, $content, $remarks, $order->id);
            $order->commission_amount = $order->commission_amount + $comm_amount;
            if($order->commission_amount > $order->amount){
                $order->commission_amount = $order->amount;
            }
            $order->save();
            $data = [
                'user_id' => $referrer_user->id,
                'name' => "收入赏金",
                'content' => "收入" . $store_tip ."赏金:￥$c_amount"
            ];
            MessageService::insert($data);
        }
        if($c_point > 0){
            $content = "收入". $store_tip ."有赏积分:￥$c_point";
            $remarks = '店铺直接推荐有赏积分';
            $UserService->userIntegralIncome($referrer_user, $c_point, $content, 0, $remarks, $order->id);
            $data = [
                'user_id' => $referrer_user->id,
                'name' => "收入有赏积分",
                'content' => "收入". $store_tip ."有赏积分:￥$c_point"
            ];
            MessageService::insert($data);
        }

        $r_store_count = StoreModel::join('user', 'user.id', '=', 'store_account.user_id')
        ->where('is_pay', '1')
        ->where('referrer_user_id', '=', $referrer_user_id)
        ->count();
        if($r_store_count >= 5 && $referrer_user->store_level < 2){
            //网店用户推荐5人升级网店vip
            $referrer_user->store_level = '2';
            $referrer_user->save();
        }
    }

    //间接推荐人店铺佣金
    public static function secondStoreCommission($user, $order){

        if($order->is_account == '1'){
           return false;
        }

        //间接推荐人id
        $second_referrer_user_id = $user->second_referrer_user_id;
        if(!$second_referrer_user_id || $second_referrer_user_id <=0){
            return false;
        }

        //间接推荐人
        $second_referrer_user = UserModel::where('id', $second_referrer_user_id)->first();
        if($second_referrer_user == null){
            return false;
        }

        if($second_referrer_user->is_vip != '1' || $second_referrer_user->level_status < 1){
            return false;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($second_referrer_user->vip_end_date < $date){
            return false;
        }


        //订单金额
        $amount = $order->amount;

        //已扣佣金
        $commission_amount = $order->commission_amount;
        if($commission_amount <0){
            $commission_amount = 0;
        }

        //可用的佣金
        $able_amount = $amount - $commission_amount;

        if($able_amount <0){
            $able_amount = 0;
            return false;
        }

        $commission_config = config('store.commission');

        $comm_amount = 0;


        $store = StoreModel::where('user_id', $second_referrer_user_id)->where('is_pay', '1')->first();


        if(empty($store)){
            $comm_amount = $commission_config[0]['second'];
        } else {
            if($second_referrer_user->store_level == '0'){
                $comm_amount = $commission_config['0']['second'];
            }
            if($second_referrer_user->store_level == '1'){
                $comm_amount = $commission_config['1']['second'];
            }
            if($second_referrer_user->store_level == '2'){
                $comm_amount = $commission_config['2']['second'];
            }
        }

        if($comm_amount > $able_amount){
            $comm_amount = $able_amount;
        }
        if($comm_amount > $amount){
            $comm_amount = $amount;
        }

        $c_amount =  round($comm_amount * 0.95, 2);

        $c_point = round($comm_amount * 0.05, 2);

        $UserService = UserService::getInstance();

        $store_tip = '店铺活跃综合评价';

        if($c_amount > 0){
            
            $content = "收入" . $store_tip . "赏金:￥$c_amount";
            $remarks = '店铺间接推荐赏金';
            $UserService->userRewardIncome($second_referrer_user, $c_amount, $content, $remarks, $order->id);
            $order->commission_amount = $order->commission_amount + $comm_amount;
            if($order->commission_amount > $order->amount){
                $order->commission_amount = $order->amount;
            }
            $order->save();
            $data = [
                'user_id' => $second_referrer_user->id,
                'name' => "收入赏金",
                'content' => "收入" . $store_tip ."赏金:￥$c_amount"
            ];
            MessageService::insert($data);
        }
        if($c_point > 0){
            $content = "收入". $store_tip ."有赏积分:￥$c_point";
            $remarks = '店铺间接推荐有赏积分';
            $UserService->userIntegralIncome($second_referrer_user, $c_point, $content, 0, $remarks, $order->id);
            $data = [
                'user_id' => $second_referrer_user->id,
                'name' => "收入有赏积分",
                'content' => "收入". $store_tip ."有赏积分:￥$c_point"
            ];
            MessageService::insert($data);
        }
        
    }

    public static function storeCommissionTree($user, $order){
        $i = 1;
        while($user && $i <=5){
            $user = static::integralStoreCommission($i, $user, $order);
            if($user == false){
                break;
            }
            $i++;
        }
    }

    public static function integralStoreCommission($i, $user, $order){
        $referrer_user_id = $user->referrer_user_id;
        //推荐人
        $referrer_user = UserModel::where('id', $referrer_user_id)->first();
        if($referrer_user == null){
            return false;
        }
        if($referrer_user->is_vip != '1' || $referrer_user->level_status < 1){
            return $referrer_user;
        }

        $date = date('Y-m-d H:i:s');

        //vip到期
        if($referrer_user->vip_end_date < $date){
            return $referrer_user;
        }

        $store = StoreModel::where('user_id', $referrer_user_id)->where('is_pay', '1')->first();
        if(empty($store)){
            return $referrer_user;
        } else {
            if($referrer_user->store_level == '0'){
                return $referrer_user;
            }
        }
        if($i == 4){
            if($referrer_user->store_level < 2){
                return $referrer_user;
            }
        }
        if($i == 5){
            if($referrer_user->store_level < 2){
                return $referrer_user;
            }
        }
        $UserService = UserService::getInstance();
        $vip_tip = '活跃用户综合评价';
        $comm_amount = 10;
        $content = "收入" .$vip_tip ."有赏积分:$comm_amount";
        $remarks = '店铺活跃有赏积分';
        $UserService->userIntegralIncome($referrer_user, $comm_amount, $content, 0, $remarks, $order->id);
        $data = [
            'user_id' => $referrer_user->id,
            'name' => "收入有赏积分",
            'content' => "收入" .$vip_tip ."有赏积分:￥$comm_amount"
        ];
        MessageService::insert($data);
        $order->commission_amount = $order->commission_amount + $comm_amount;
        if($order->commission_amount > $order->amount){
            $order->commission_amount = $order->amount;
        }
        $order->save();
        return $referrer_user;
    }

    public static function orderRechargePayHandel($order_recharge_id, $app){
        $OrderRecharge = OrderRecharge::where('id', '=', $order_recharge_id)->first();
        if($OrderRecharge == null){
            echo '对不起，订单不存在';
            return false;
        }
        $isPay = static::checkOrderPay($OrderRecharge, $app);
        if($isPay == false){
            echo '对不起，订单未支付';
            return false;
        }
        if($OrderRecharge->is_account == '1'){
            echo '对不起，订单已核算';
            return false;
        }

        $order_type = $OrderRecharge->order_type;

        switch ($order_type) {
            case 'vip':
                OrderRechargeService::vipOrderPay($OrderRecharge);
                break;
            case 'integral':
                OrderRechargeService::integralOrderPay($OrderRecharge);
                break;
            case 'store':
                OrderRechargeService::storeOrderPay($OrderRecharge);
                break;
            case 'card_renewal':
                OrderRechargeService::cardRenewalOrderPay($OrderRecharge);
                break;
            default:
                # code...
                break;
        }
    }

     /**
     * 检查积分充值订单是否支付完成
     *
     * @return void
    */
    public static function checkOrderPay($OrderRecharge, $app)
    {
        if($OrderRecharge != null){
            $order_no = $OrderRecharge['order_no'];
            $response = $app->payment->query($order_no);
            if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
                if ($response['trade_state'] == 'SUCCESS') {
                    try{
                        OrderRechargeService::wxOrderRecord($OrderRecharge, $response);
                    } catch(Exception $e){}
                    return true;
                }
            }
        }
        return false;
    }

     //开通店铺自动送积分
    public static function storeSendIntegral($user, $order){
        $config = SiteConfig::first();
        if($config != null){
            $store_integral_send_open = $config->store_integral_send_open;
            if($store_integral_send_open == 1){
                $store_integral_send_amount = $config->store_integral_send_amount;
                if($store_integral_send_amount > 0){
                    $user_id = $user->id;
                    $IntegralSendModel = IntegralSend::where('user_id', $user_id)->where('type', 'store')->first();
                    if($IntegralSendModel == null){
                        $remarks = '商城活动赠送';
                        UserService::getInstance()->userIntegralIncome($user, $store_integral_send_amount, '商城活动赠送', 0, $remarks, $order->id);
                        $IntegralSendModel = new IntegralSend();
                        $IntegralSendModel->user_id = $user_id;
                        $IntegralSendModel->type = 'store';
                        $IntegralSendModel->integral = $store_integral_send_amount;
                        $IntegralSendModel->content = '商城活动赠送';
                        $r = $IntegralSendModel->save();
                        if($r){
                            $data = [
                                'user_id' => $user_id,
                                'name' => "商城活动赠送积分",
                                'content' => "商城活动赠送积分： ￥" .$store_integral_send_amount
                            ];
                            MessageService::insert($data);
                        }
                    }
                }
            }
        }
    }

    /**
     * 生成退款单号
     */
    public static function generateOrderRefundNumber($user_id){
        $time_str = date('YmdHis');
        $md5_str = md5(rand(1, 10000) . $user_id);
        $number = 'PR' . $time_str.substr($md5_str, 0, 10);
        return $number;
    } 

     /**
     * 生成退款记录
     * @param  [type] $order  [description]
     * @param  [type] $reason [description]
     * @return [type]         [description]
     */
    public static function createOrderRefund($order, $reason){
        $OrderRechargeRefund = OrderRechargeRefund::where('order_id', '=', $order['id'])->first();
        if($OrderRechargeRefund == null){
            $OrderRefundModel = new OrderRechargeRefund();
            $refundsn = static::generateOrderRefundNumber($order['user_id']);
            $OrderRefundModel->order_id = $order['id'];
            $OrderRefundModel->currency = 'CNY';
            $OrderRefundModel->order_id = $order['id'];
            $OrderRefundModel->refundsn = $refundsn;
            $OrderRefundModel->user_id = $order['user_id'];
            $amount = $order['payment_amount'];
            $OrderRefundModel->amount = $amount;
            $OrderRefundModel->reason = $reason;
            $OrderRefundModel->save();
        }
        return $OrderRefundModel;
    }


     /**
     * 退款处理
     * @param  [type] $order            [description]
     * @param  [type] $OrderRefundModel [description]
     * @param  [type] $app              [description]
     * @param  [type] $refundFee        [description]
     * @return [type]                   [description]
     */
    public static function refundHandel($order){
        $app = new Application(config('wechat'));
        $OrderRefundModel = static::createOrderRefund($order, '代购开通VIP抵扣积分或者余额不足');
        $result = ['status' => '0'];
        $OrderRefundModel->status = '1';
        $OrderRefundModel->save();
        $payment_amount = $order['payment_amount'];
        if($payment_amount > 0){
            $response = $app->payment->refund($order['order_no'], $OrderRefundModel['refundsn'], $payment_amount * 100, $payment_amount * 100);
            if($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS'){
                static::refundAccount($order, $OrderRefundModel);
                $result['status'] = '1';
                $result['message'] = '退换单已处理！';
            } else {
                $result['message'] = '退换单已处理，申请退款中！';
            }
            return $result;
        } else {
            static::refundAccount($order, $OrderRefundModel);
        }
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
            $OrderRefundModel->is_account = '1';
            $OrderRefundModel->save();
            $order->refund_status = '2';
            return true;
        });
        return $res;
    }
}