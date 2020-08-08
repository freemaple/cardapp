<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;
use App\Libs\Service\OrderRechargeService;

use App\Models\Order\Recharge as OrderRechargeModel;
use EasyWeChat\Foundation\Application;

class OrderRechargeController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $OrderRechargeModel = new OrderRechargeModel();

        $pageSize = 20;

        $form = $request->all();

        $order_no = trim($request->order_no);

        if($order_no != null){
            $OrderRechargeModel = $OrderRechargeModel->where('order_no', '=', $order_no);
        }

        $status = trim($request->status);

        if($status != null){
            $OrderRechargeModel = $OrderRechargeModel->where('status', '=', $status);
        }

        $order_type = trim($request->order_type);

        if($order_type != null){
            $OrderRechargeModel = $OrderRechargeModel->where('order_type', '=', $order_type);
        }

        $is_account = trim($request->is_account);

        if($is_account != null){
            $OrderRechargeModel = $OrderRechargeModel->where('is_account', '=', $is_account);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $OrderRechargeModel = $OrderRechargeModel->where('paid_at', '>=', $start_date);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $OrderRechargeModel = $OrderRechargeModel->where('paid_at', '<=', $end_date);
        }

        $orders = $OrderRechargeModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        foreach ($orders as $key => $order) {
            $user = $order->user()->first();
            $order->userinfo = !empty($user) ? $user->toArray() : [];
        }

        $orders->appends($request->all());

        $pager = $orders->links();

        $recharge_type = config('user.recharge_type');

        $view = View('admin.order.recharge.index');

        $view->with("orders", $orders);

        $view->with("recharge_type", $recharge_type);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "订单");

        return $view;

    }

    /**
     * 核算
     *
     * @return void
    */
    public function pay(Request $request, Application $app, $order_id)
    {
        OrderRechargeService::orderRechargePayHandel($order_id, $app);

    }
}