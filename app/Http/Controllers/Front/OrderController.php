<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use Auth;
use Session;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Order\Order as OrderModel;  
use App\Models\Store\Store as StoreModel;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Models\Order\OrderRefund as OrderRefundModel;
use App\Models\Order\OrderProduct as OrderProductModel;
use App\Libs\Service\OrderService;
use EasyWeChat\Foundation\Application;


class OrderController extends BaseController
{

     /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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

        $pager = '';

        if(count($orders) > 0 && $orders->lastPage() > 1){
            $pager = $orders->links();
        }

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
        }

        //显示在前端的状态
        $order_status = config('order.status');

        //显示在前端的状态
        $do_show_status = config('order.do_show_status');

        $form = ['status_code' => ''];

        $filter = $request->all();

        $form = array_merge($form, $filter);

        return view('account.order.index')->with([
            'title' => '订单列表',
            'orders' => $orders,
            'order_status' => $order_status,
            'do_show_status' => $do_show_status,
            'form' => $form,
            'pager' => $pager
        ]);
    }

    /**
     * 订单详情
     *
     * @return \Illuminate\Http\Response
     */
    public function orderDetail(Request $request, $number)
    {
        $user = Auth::user();
        $order = OrderModel::where('order_no', $number)->first();
        if($order == null){
            return redirect(\Helper::route('account_orders'));
        }

        $order = OrderService::getOrderDetail($order);

        //显示在前端的状态
        $order_status = config('order.status');
        
        $view = view('account.order.detail',[
            'user' => $user,
            'title' => '订单详情',
            'order_detail' => $order,
            'order_status' => $order_status
        ]);
        return $view;
    }

     /**
     * 订单详情
     *
     * @return \Illuminate\Http\Response
     */
    public function orderPay(Request $request, Application $app, $number)
    {
        $user = Auth::user();
        $order = OrderModel::where('order_no', $number)->first();
        if($order == null){
            return redirect(\Helper::route('account_orders'));
        }
        $order_no = $order['order_no'];
        if($order->is_pay != 'pending'){
            return redirect(\Helper::route('account_order_detail', [$order_no]));
        }
        if($order->is_pay == '1'){
            return redirect(\Helper::route('account_order_detail', [$order_no]));
        }

        $pay_remaining_time = OrderService::getPayRemainingTime($order);

        if($order['order_status_code'] == 'pending' && $pay_remaining_time <=0){
            OrderService::cancelOrder($order, $app);
            return redirect(\Helper::route('account_order_detail', [$order_no]));
        }

        $order = OrderService::getOrderDetail($order);

        //显示在前端的状态
        $order_status = config('order.status');

        $total_amount = $order['order_total'];

        $order_integral = $order['order_integral'];

        $payment_amount = $total_amount - $order_integral;

        $is_auto_pay = $request->is_auto_pay;

        $view = view('account.order.pay',[
            'user' => $user,
            'title' => '订单支付',
            'order_detail' => $order,
            'order_status' => $order_status,
            'payment_amount' => $payment_amount,
            'is_auto_pay' => $is_auto_pay
        ]);
        return $view;
    }

    
     /**
     * 支付成功
     *
     * @return void
    */
    public function successPay(Request $request, $order_no)
    {
        $user = Auth::user();
        $order = OrderModel::where('order_no', '=', $order_no)->where('is_pay', '=', '1')->first();
        $view = view('checkout.success', [
            'title' => '支付成功',
            'user' => $user,
            'order' => $order
        ]);
        return $view;
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
        $order_refunds = OrderRefundModel::select('order_refund.*', 'order.order_no', 'order.order_total', 'order.order_status_code', 'order.order_item_qty')
        ->where('order_refund.user_id', $user_id)
        ->join('order', 'order.id', 'order_refund.order_id')
        ->orderBy('id', 'desc')
        ->paginate($pageSize);

        $order_refunds->appends($request->all());

        $pager = '';

        if(count($order_refunds) > 0){
            $pager = $order_refunds->links();
        }

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
        }

        //显示在前端的状态
        $refund_status = config('order.refund_status');
        
        $view = view('account.order.refund',[
            'user' => $user,
            'title' => '退换货申请',
            'order_refunds' => $order_refunds,
            'refund_status' => $refund_status,
            'pager' => $pager
        ]);
        return $view;
    }

     /**
     * 订单详情
     *
     * @return \Illuminate\Http\Response
     */
    public function orderRefund(Request $request, $id)
    {
        $user = Auth::user();
        $order = OrderModel::where('id', $id)->first();
        if($order == null){
            return redirect(\Helper::route('account_orders'));
        }

        //显示在前端的状态
        $order_status = config('order.status');
        
        $view = view('account.order.refund_apply',[
            'user' => $user,
            'title' => '订单详情',
            'order' => $order,
            'order_status' => $order_status,
        ]);
        return $view;
    }

}
