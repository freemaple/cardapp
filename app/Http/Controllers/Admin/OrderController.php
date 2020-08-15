<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;

use App\Models\Order\Order as OrderModel;
use App\Models\Order\OrderRefund as OrderRefundModel;
use App\Models\User\User as UserModel;

use App\Models\Order\OrderShipping as OrderShippingModel;
use App\Models\Store\Store as StoreModel;
use App\Cache\ShippingMethod as ShippingMethodCache;
use App\Libs\Service\OrderService;

class OrderController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $OrderModel = new OrderModel();

        $pageSize = 20;

        $form = $request->all();

        $order_type = trim($request->order_type);

        if(isset($request->order_type) && $order_type !==''){
            $OrderModel = $OrderModel->where('order_type', '=', $order_type);
        }

        $order_no = trim($request->order_no);

        if($order_no != null){
            $OrderModel = $OrderModel->where('order_no', '=', $order_no);
        }

        $is_self = $request->is_self;

        if($is_self == '1'){
            $OrderModel = $OrderModel->where('is_self', '=', '1');
        } else if($is_self == '0'){
            $OrderModel = $OrderModel->where('is_self', '=', '0');
        }

        $order_status_code = trim($request->order_status_code);

        if($order_status_code != null){
            $OrderModel = $OrderModel->where('order_status_code', '=', $order_status_code);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $OrderModel = $OrderModel->where('created_at', '>=', $start_date);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $OrderModel = $OrderModel->where('created_at', '<=', $end_date);
        }

        $orders_statistics = [];

        $orders_amount = $OrderModel->sum('order_total');

        $orders_statistics['order_total'] = $orders_amount;

        $orders_integral = $OrderModel->sum('order_integral');

        $orders_statistics['orders_integral'] = $orders_integral;

        $orders_payment_amount = $OrderModel->sum('payment_amount');

        $orders_statistics['payment_amount'] = $orders_payment_amount;

        $orders = $OrderModel->orderBy('id', 'desc');


        $orders = $orders->paginate($pageSize);

        foreach ($orders as $key => $order) {
            $user = $order->user()->first();
            $order->userinfo = !empty($user) ? $user->toArray() : [];
            $seller_id = $order->seller_id;
            if($seller_id != null){
                $store = StoreModel::where('user_id', $seller_id)->first();
                $order->store_info = !empty($store) ? $store->toArray() : [];
            }
            $order_accountRecord = $order->orderAccountRecord()->first();
            if($order_accountRecord != null){
                $order_accountRecord = $order_accountRecord->toArray();
                $order->account_record = $order_accountRecord;
            }
            $order_product = $order->products()->first();
            $order->image = $order_product->image;
            $order->image = \HelperImage::storagePath($order->image);
            $order->shipinfo = $order->userinfo()->first();
            $referrer_user_id = $user->referrer_user_id;
            if(!empty($referrer_user_id)){
                $order->referrer_user = UserModel::where('id', $referrer_user_id)->first();
            }
        }

        $orders->appends($request->all());

        //dd($orders->toArray());

        $pager = $orders->links();

        $shipping_method = ShippingMethodCache::get();

        //显示在前端的状态
        $order_status = config('order.status');

        $view = View('admin.order.index');

        $view->with("orders", $orders);

        $view->with("orders_statistics", $orders_statistics);

        $view->with("shipping_method", $shipping_method);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("order_status", $order_status);

        $view->with("title", "订单");

        return $view;

    }

     /**
     * 订单详情
     *
     * @return void
    */
    public function detail(Request $request, $id)
    {
        $order = OrderModel::where('id', $id)->first();
        if($order == null){
            return redirect(route('admin_orders'));
        }
        $order_info = OrderService::getOrderDetail($order);
        $order_accountRecord = $order->orderAccountRecord()->first();
        if($order_accountRecord != null){
            $order_accountRecord = $order_accountRecord->toArray();
            $order_info['account_record'] = $order_accountRecord;
        }
        //显示在前端的状态
        $order_status = config('order.status');
        $view = view('admin.order.detail',[
            'title' => '订单详情',
            'order_info' => $order_info,
            'order_status' => $order_status
        ]);
        return $view;

    }

     /**
     * 订单发货
     *
     * @return void
    */
    public function shipOrder(Request $request)
    {
        $result = [];
        $order_id = $request->order_id;
        $order = OrderModel::where('id', $order_id)->first();
        if($order == null){
            $result['code'] = '2x1';
            $result['message'] = '订单不存在！';
            return response()->json($result);
        }
        if($order['order_status_code'] == 'pending'){
            $result['code'] = '2x1';
            $result['message'] = '订单未付款！';
            return response()->json($result);
        }
        if($order['order_status_code'] == 'complete'){
            $result['code'] = '2x1';
            $result['message'] = '订单已完成';
            return response()->json($result);
        }
        if($order['order_status_code'] == 'shipped'){
            $result['code'] = '2x1';
            $result['message'] = '订单已发货';
            return response()->json($result);
        }
        if($order['order_status_code'] != 'shipping'){
            $result['code'] = '2x1';
            $result['message'] = '订单无需发货';
            return response()->json($result);
        }
        if($order['order_status_code'] != 'shipping'){
            $result['code'] = '2x1';
            $result['message'] = '订单无需发货';
            return response()->json($result);
        }
        $res = \DB::transaction(function() use ($order, $request) {
            $shipping_method = $request->shipping_method;
            $tracknumber = $request->tracknumber;
            $OrderShippingModel = new OrderShippingModel();
            $OrderShippingModel->order_id = $order->id;
            $OrderShippingModel->shipping_method = $shipping_method;
            $OrderShippingModel->tracknumber = $tracknumber;
            $OrderShippingModel->save();
            $order->order_status_code = 'shipped';
            $order->shipped_at = date('Y-m-d H:i:s');
            $order->save();
        });
        $result['code'] = '200';
        $result['message'] = '订单已发货';
        return response()->json($result);
    }

    /**
     * 订单详情
     *
     * @return void
    */
    public function refund(Request $request)
    {
        $OrderRefundModel = OrderRefundModel::select('order_refund.*', 'order.order_no', 'order.store_id', 'order.seller_id')->join('order', 'order.id', 'order_refund.order_id');

        $pageSize = 20;

        $form = $request->all();

        $order_no = trim($request->order_no);

        if($order_no != null){
            $OrderRefundModel = $OrderRefundModel->where('order.order_no', '=', $order_no);
        }

        $is_self = $request->is_self;

        if($is_self == '1'){
            $OrderRefundModel = $OrderRefundModel->where('order.is_self', '=', '1');
        } else if($is_self == '0'){
            $OrderRefundModel = $OrderRefundModel->where('order.is_self', '=', '0');
        }

        $order_status_code = trim($request->order_status_code);

        if($order_status_code != null){
            $OrderModel = $OrderModel->where('order.order_status_code', '=', $order_status_code);
        }

        $order_refunds = $OrderRefundModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        foreach ($order_refunds as $key => $order_refund) {
            $user = UserModel::where('id', $order_refund['user_id'])->first();
            $order_refund->userinfo = !empty($user) ? $user->toArray() : [];
            $store_id = $order_refund->store_id;
            if($store_id != null){
                $store = StoreModel::where('id', $store_id)->first();
                $order_refund->store_info = !empty($store) ? $store->toArray() : [];
            }
            $order = $order_refund->order()->first();
            $order_refund->order_info = !empty($order) ? $order->toArray() : [];
        }

        $order_refunds->appends($request->all());

        $pager = $order_refunds->links();

        //显示在前端的状态
        $order_status = config('order.status');

        //显示在前端的状态
        $refund_status = config('order.refund_status');


        //dd($order_refunds->toArray());

        $view = View('admin.order.refund');

        $view->with("order_refunds", $order_refunds);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("order_status", $order_status);

        $view->with("refund_status", $refund_status);

        $view->with("title", "订单");

        return $view;

    }
}