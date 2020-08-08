<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use App\Libs\Service\OrderService;
use App\Libs\Service\UserService;
use EasyWeChat\Foundation\Application;
use App\Models\Order\Order as OrderModel;  
use App\Models\User\Address as AddressModel;
use  App\Libs\Service\ProductDispalyService;
use App\Libs\Service\MessageService;
use App\Models\Product\Sku as ProductSkuModel; 
use App\Models\Product\Product as ProductModel;
use App\Models\Order\OrderRefund as OrderRefundModel;
use App\Libs\Service\CartService;
use App\Cache\Checkout as CheckoutCache;


class OrderController extends BaseController
{
    /**
     * order
     *
     * @return void
    */
    public function create(Application $app, Request $request)
    {
        $result = ['code' => ''];

        $address_id = trim($request->address_id);

        $user = Auth::user();

        $order_buyer_id = $user->id;

        $address_id = trim($request->address_id);

        $shipping_address = AddressModel::find($address_id);

        if($shipping_address == null){
            $result['code'] = '2x1';
            return response()->json($result);
        }
        if($shipping_address != null){
            $shipping_address = $shipping_address->toArray();
        }

        //产品ID
        $goods_id = $request->goods_id;        

        //产品SKU
        $sku_ids = $request->sku_id;

        //SKU数量
        $qtys = $request->qtys;

        //验证数据
        $validator = \Validator::make(['sku_ids' => $sku_ids, 'qtys' => $qtys], [
            'sku_ids.*' => 'required',
            'qtys.*' => 'required|integer'
        ]);
        
        if($validator->fails()){
            $view->with('message', 'The data format is incorrect.');
            return $view;
        }

        $cart_result = CartService::getCartData($request);

        if($cart_result['status'] != '1'){
            $result['code'] = '2x1';
            $result['message'] = $cart_result['message'];
            return response()->json($result);
        }

        $cart_data = $cart_result['data'];

        $products = $cart_data['products'];

        $is_self = false;

        foreach ($products as $pkey => $product) {
            if($product['is_self'] == '1'){
                $is_self = true;
            }
        }

        $order_currency = 'CNY';
        
        $total_amount = $cart_data['subtotal_amount'] + $cart_data['shipping_amount'];

        $payment_method = $request->payment;

        $comment = $request->comment;

        $use_integral = $request->use_integral;

        $order_integral = 0;

        $integral_amount = 0;

        //积分
        $integral = $user->integral()->first();
        if($integral != null){
           $integral = $integral->toArray();
           $integral_amount = $integral['point'];
        }

        if($integral_amount > 0 && $use_integral == '1'){
            if($integral_amount > $total_amount){
                $order_integral = $total_amount;
            } else {
                $order_integral = $integral_amount;
            }
        }

        $payment_amount = $total_amount - $order_integral;

        if($payment_amount <= 0){
            $payment_method = '';
        }

        $basket_code = $request->basket_code;

        $payment_data = [
            'order_buyer_id' => $order_buyer_id,
            'order_item_qty' => $cart_data['order_item_qty'],
            'order_ipaddress' => \Helper::getIPAddress(),
            'order_device' => \Helper::device(),
            'order_currency' => $order_currency,
            'amount' => [
                'currency' => $order_currency,
                'total' => $total_amount,
                'details' => [
                    'subtotal' => $cart_data['subtotal_amount'],
                    'shipping' => $cart_data['shipping_amount']
                ]
            ],
            'products' => $cart_data['products'],
            'shipping_address' => $shipping_address,
            'comment' => $comment,
            'order_integral' => $order_integral,
            'payment_amount' => $payment_amount,
            'payment_method' => $payment_method,
            'is_self' => $is_self
        ];

        

        $order = OrderService::createOrder($payment_data, $user);
        if($order != null){
            CheckoutCache::setBasketCode($basket_code, [
                'order_no' => $order['order_no']
            ]);
            $result['code'] = 'Success';
            $result['data'] = ['order_id' => $order['id'], 'order_no' => $order['order_no']];
            if($payment_amount > 0){
                if($payment_method == 'weixin'){
                    OrderService::wxPayment($app, $order, $payment_amount);
                }
            } else {
                OrderService::orderPayHandel($order);
                $result['data']['is_pay'] = '1';
            }
           
        }
        return response()->json($result);
    }

     /**
     * order
     *
     * @return void
    */
    public function pay(Application $app, Request $request)
    {
        $result = ['code' => ''];

        $order_id = $request->order_id;

        $user = Auth::user();

        $user_id = $user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', '=', $user_id)->first();

        if($order == null){
            $result['message'] = '对不起，订单不存在!';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'cancel'){
            $result['message'] = '对不起，订单已取消!';
            return response()->json($result);
        }

        if($order['is_pay']){
            $result['message'] = '对不起，订单已付款!';
            $result['code'] = 'Success';
            $result['data'] = ['is_pay' => '1', 'order_id' => $order['id'], 'order_no' => $order['order_no']];
            return response()->json($result);
        }

        if($order['payment_method'] == 'weixin'){
            $is_pay = OrderService::checkOrderPay($order, $app);
            if($is_pay){
                if($order['order_status_code'] == 'pending'){
                    OrderService::orderPayHandel($order);
                }
                try{
                    OrderService::wxOrderRecord($order, $response);
                } catch(Exception $e){}
                $result['code'] = 'Success';
                $result['data'] = ['is_pay' => '1', 'order_id' => $order['id'], 'order_no' => $order['order_no']];
                return response()->json($result);
            }
        }

        $order_product = $order->products()->get();

        foreach ($order_product as $pkey => $p) {
            $product = ProductModel::where('id', $p['product_id'])
            ->where('is_sale', '1')
            ->where('deleted', '!=', '1')->first();
            if($product == null){
                $result['message'] = '呀， 此产品已下架！';
                return response()->json($result);
            }
            $sku = ProductSkuModel::where('id', $p['sku_id'])
            ->where('is_sale', '1')
            ->where('deleted', '!=', '1')->first();
            if($sku == null){
                $result['message'] = '呀， 此产品已下架！';
                return response()->json($result);
            }
            if($sku['stock'] == 0 || $p['quantity'] > $sku['stock']){
                $result['message'] = '呀,下手晚了一步！库存缺货，请咨询商家补充货再重新下单！';
                return response()->json($result);
            }
        }
        
        $total_amount = $order['order_total'];

        $payment_method = $request->payment;

        $order_integral = $order['order_integral'];

        $payment_amount = $total_amount - $order_integral;


        if($payment_amount > 0){

            if($payment_method == 'weixin'){

                $result = OrderService::wxPayment($app, $order, $payment_amount);
            }
        } else {
            OrderService::orderPayHandel($order);
            $result['code'] = 'Success';
            $result['data'] = ['is_pay' => '1', 'order_id' => $order['id'], 'order_no' => $order['order_no']];
        }
        return response()->json($result);
    }

    /**
     * order
     *
     * @return void
    */
    public function cancel(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_id = $request->order_id;

        $user = Auth::user();

        $user_id = $user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', '=', $user_id)->first();

        if($order == null){
            $result['message'] = '对不起，订单不存在!';
            return response()->json($result);
        }
        
        $result = OrderService::cancelOrder($order, $app);

        $result['data'] = ['order_no' => $order['order_no']];

        return response()->json($result);
    }

    /**
     * 检查订单是否支付完成
     *
     * @return void
    */
    public function checkOrderPay(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_id = $request->order_id;

        $user = Auth::user();

        $user_id = $user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', '=', $user_id)->first();

        if($order != null){
            if($order['is_pay'] == '1'){
                $result['code'] = 'Success';
                return response()->json($result);
            }
            $order_no = $order['order_no'];
            $response = $app->payment->query($order_no);
            if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
                if ($response['trade_state'] == 'SUCCESS') {
                    /*OrderService::orderPayHandel($order);
                    try{
                        OrderService::wxOrderRecord($order, $response);
                    } catch(Exception $e){}*/
                    $result['code'] = 'Success';
                    return response()->json($result);
                }
            }
        }
        return response()->json($result);
    }

    /**
     * 确认发货
     *
     * @return void
    */
    public function orderFinished(Request $request, Application $app)
    {
        $result = ['code' => ''];

        $order_id = $request->order_id;

        $user = Auth::user();

        $user_id = $user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', '=', $user_id)->first();

        if($order == null){
            $result['code'] = '2x1';
            $result['code'] = '订单不存在';
            return response()->json($result);
        }

        if($order['order_status_code'] != 'shipped'){
            $result['code'] = '2x1';
            $result['code'] = '订单未发货';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'finished'){
            $result['code'] = '2x1';
            $result['code'] = '订单已收货完成';
            return response()->json($result);
        }

        if($order != null){
           OrderService::orderFinished($order, $user);
           $result['code'] = 'Success';
           $result['message'] = '订单已经完成,欢迎我王下次继续享受购物乐趣！';
        }
        return response()->json($result);
    }

     /**
     * 退款申请
     *
     * @return void
    */
    public function refundApply(Request $request, Application $app)
    {
        $result = ['code' => '2x1'];

        $order_id = $request->order_id;

        $user = Auth::user();

        $user_id = $user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', '=', $user_id)->first();

        if($order == null){
            $result['code'] = '对不起，订单不存在';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'cancel'){
            $result['code'] = '对不起，订单已取消';
            return response()->json($result);
        }


        if($order['order_status_code'] == 'finished'){
            $result['code'] = '对不起，订单已完成';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'pending'){
            $result['code'] = '对不起，订单还未付款';
            return response()->json($result);
        }

        $reason = $request->reason;

        
        $OrderRefundModel = OrderRefundModel::where('order_id', '=', $order['id'])->orderBy('id', 'desc')->first();

        if($OrderRefundModel != null){
            if($OrderRefundModel['status'] == '0'){
                $result['message'] = '对不起，退款申请审核中';
                return response()->json($result);
            }
            if($OrderRefundModel['status'] == '1'){
                $result['message'] = '对不起，退款申请审核中';
                return response()->json($result);
            }
            if($OrderRefundModel['status'] == '2'){
                $result['message'] = '对不起，退款申请已完结，请勿重新提交';
                return response()->json($result);
            }
        }
        $OrderRefundModel = new OrderRefundModel();
        $refundsn = OrderService::generateOrderRefundNumber($order['user_id']);
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
        $result['code'] = 'Success';
        $result['message'] = '退款申请已提交!';

        if($order['seller_id'] > 0){
            $title = '客户申请退换货！';
            //接收人消息
            $content = '客户申请退换货：订单:' . $order['order_no'] . ', 请进行处理!';
            $data = [
                'user_id' => $order['seller_id'],
                'message_type' => 'order_refund',
                'name' => $title,
                'content' => $content,
                'order_no' => $order['order_no'],
                'link' =>  '/account/store/order/refundlist?order_id=' . $order['id']
            ];
            MessageService::insert($data);
        }

        return response()->json($result);
    }

    public function getOrderList(Request $request){

        $orders = [];

        $user = Auth::user();
    
        $user_id = $user->id;

        $pageSize = config('paginate.orders_list', 100);

        $OrderModel = OrderModel::where('user_id', $user_id);

        $status_code = $request->status_code;

        if($status_code == 'review'){
            $OrderModel->where('order_status_code', '=', 'finished');
            $OrderModel->where('is_review', '=', '0');
        } else if($status_code == 'complete') {
            $OrderModel->where('order_status_code', '=', 'finished');
            $OrderModel->where('is_review', '=', '1');
        } else {
            if($status_code != null){
                $OrderModel->where('order_status_code', '=', $status_code);
            }
        }

        //获取用户订单列表
        $orders = $OrderModel->orderBy("id", "desc")
        ->paginate($pageSize);

        $orders->appends($request->all());


        //显示在前端的状态
        $order_status = config('order.status');

        //显示在前端的状态
        $do_show_status = config('order.do_show_status');

        foreach ($orders as $key => $order) {
            if(in_array($order['order_status_code'], ['shipping', 'shipped'])){
                $refund = OrderRefundModel::where('order_id', $order->id)->whereIn('status', ['0', '1', '2'])
                ->orderBy('id', 'desc')
                ->first();
                if($refund != null){
                    $refund = $refund->toArray();
                }
                $order->refund = $refund;
            }
            $order = OrderService::getOrderListDetail($order);
            $order->order_status_text = !empty($order_status[$order->order_status_code]) ? $order_status[$order->order_status_code] : '';
            $order->currency_text = '￥';
        }

        $result['code'] = 'Success';

        $show_status[] = [
            'code' => '',
            'name' => '全部'
        ];

        foreach ($do_show_status as $code => $value) {
            $show_status[] = [
                'code' => $code,
                'name' => $value
            ];
        }

        $result['data'] = [
            'orders' =>  $orders,
            'order_status' => $show_status
        ];

        return response()->json($result);
    }

    public function getOrderDetail(Request $request){

        $user = Auth::user();
    
        $user_id = $user->id;

        $order_id = $request->order_id;

        $order = OrderModel::where('id', $order_id)->first();

        if($order == null){
            return redirect(\Helper::route('account_orders'));
        }

        $order = OrderService::getOrderDetail($order);

        //显示在前端的状态
        $order_status = config('order.status');
        

        //显示在前端的状态
        $order_status = config('order.status');

        $order->order_status_text = !empty($order_status[$order->order_status_code]) ? $order_status[$order->order_status_code] : '';

        $order['currency_text'] = '￥';

        $result['code'] = 'Success';

        $result['data'] = [
            'order' =>  $order
        ];

        return response()->json($result);
    }

     /**
     * 订单详情
     *
     * @return \Illuminate\Http\Response
     */
    public function orderRefundList(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $pageSize = 100;
        $composite_status = $request->composite_status;
        $order_refunds = OrderRefundModel::select('order_refund.*', 'order.order_no', 'order.order_total', 'order.order_status_code', 'order.order_item_qty')
        ->where('order_refund.user_id', $user_id);

        if($composite_status == '122'){
            $order_refunds = $order_refunds->whereIn('order_refund.status', ['0', '1']);
        }
        if($composite_status == '123'){
            $order_refunds = $order_refunds->whereIn('order_refund.status', ['2', '-1']);
        }
        $order_refunds = $order_refunds->join('order', 'order.id', 'order_refund.order_id')
        ->orderBy('id', 'desc')
        ->paginate($pageSize);

         //显示在前端的状态
        $refund_status = config('order.refund_status');

         foreach ($order_refunds as $key => $o_refunds) {
            $order_product = OrderProductModel::select('order_product.*')
            ->where('order_id', '=', $o_refunds['order_id'])
            ->first();
            if($order_product != null){
                $image = !empty($order_product) ? $order_product['image'] : '';
                if(!empty($image)){
                    $image = \HelperImage::storagePath($image);
                }
                $order_product->image = $image;
                $order_product = $order_product->toArray();
            }
            $o_refunds->product = $order_product;
            $o_refunds->status_text = $refund_status[$o_refunds->status];
            $o_refunds->currency_text = '￥';
        }

       

        $data = [
            'order_refunds' => $order_refunds,
            'refund_status' => $refund_status
        ];
        $result = ['code' => 'Success', 'data' => $data];
        return response()->json($result);
    }

    /**
     * 订单详情
     *
     * @return \Illuminate\Http\Response
     */
    public function orderRefundDetail(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $id = $request->refund_id;
        $pageSize = 100;
        $order_refund = OrderRefundModel::select('order_refund.*', 'order.order_no', 'order.order_total', 'order.order_status_code', 'order.order_item_qty')
        ->where('order_refund.user_id', $user_id)
        ->where('order_refund.id', $id)
        ->join('order', 'order.id', 'order_refund.order_id')
        ->first();

         //显示在前端的状态
        $refund_status = config('order.refund_status');

        $order_refund->status_text = $refund_status[$order_refund->status];

        $order_refund->currency_text = '￥';

        $back_rate= [];

        $back_rate[] = [
            'status' => '1',
            'rate' => '审核中'
        ];

        if($order_refund->status == '2'){
            $back_rate[] = [
                'status' => '1',
                'rate' => '审核通过'
            ];
        } else if($order_refund->status == '-1'){
            $back_rate[] = [
                'status' => '1',
                'rate' => '已拒绝'
            ];
        } else {
            $back_rate[] = [
                'status' => '0',
                'rate' => '审核通过'
            ];
        }

        $order = OrderModel::where('id', $order_refund->order_id)->where('user_id', '=', $user_id)->first();

        $order = OrderService::getOrderDetail($order);

        $order_refund->order = $order;

        $order_refund->back_rate = $back_rate;

        $result = ['code' => 'Success', 'data' => $order_refund];

        return response()->json($result);
    }
}