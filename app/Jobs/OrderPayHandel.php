<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libs\Service\OrderService;

use App\Traits\ProgramLogTrait;
use DB;

class OrderPayHandel implements ShouldQueue
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
     * 订单支付完成
     *
     * @return void
     */
    public function handle(Application $app)
    {
        $order_id = $this->order_id;
        //启用DB事务机制
        $res = DB::transaction(function() use ($order_id){
            $order = OrderModel::where('id', $order_id)->first();
            if($order == null){
                return false;
            }
            if($order->is_pay_account == '1'){
                return false;
            }
            if($order->order_status_code != 'shipping'){
                $order->order_status_code = 'shipping';
            }
            if($order->payed_at == ''){
                $order->payed_at = date('Y-m-d H:m:s');
            }
            $order->is_pay = '1';

            $res = $order->save();

            static::orderPayAccount($order);
        });
    }
}
