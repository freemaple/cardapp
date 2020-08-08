<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libs\Service\OrderService;

use App\Traits\ProgramLogTrait;
use DB;
use App\Models\Order\Order as OrderModel;
use EasyWeChat\Foundation\Application;

class OrderCancel implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ProgramLogTrait;

    protected $order_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * 订单自动取消
     *
     * @return void
     */
    public function handle(Application $app)
    {
        $order_id = $this->order_id;
        //启用DB事务机制
        $res = DB::transaction(function() use ($order_id, $app){
            $order = OrderModel::where('id', $order_id)->first();
            if($order == null){
                return false;
            }
            if($order['order_status_code'] != 'pending'){
                return false;
            }
            if($order['payment_method'] == 'weixin'){
                $is_pay = OrderService::checkOrderPay($order, $app);
                if($is_pay){
                    return false;
                }
            }
            $result = OrderService::cancelOrder($order, $app);
            return $result;
        });
    }
}
