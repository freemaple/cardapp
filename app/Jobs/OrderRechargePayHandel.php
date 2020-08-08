<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libs\Service\OrderRechargeService;

use App\Traits\ProgramLogTrait;
use DB;

class OrderRechargePayHandel implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ProgramLogTrait;

    protected $order_recharge_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_recharge_id)
    {
        $this->order_recharge_id = $order_recharge_id;
    }

    /**
     * 订单完成
     *
     * @return void
     */
    public function handle(Application $app)
    {
        $order_recharge_id = $this->order_recharge_id;
        $OrderRecharge = OrderRecharge::where('id', '=', $order_recharge_id)->first();
        if($OrderRecharge == null){
            return false;
        }
        if($OrderRecharge->order_status_code != 'pending'){
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
}
