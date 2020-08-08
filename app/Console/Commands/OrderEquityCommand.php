<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User\User as UserModel;
use App\Models\Order\Recharge as OrderRechargeModel;
use App\Libs\Service\EquityService;

class OrderEquityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OrderEquityCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'OrderEquityCommand';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
             $orders = OrderRechargeModel::where('status', '2')
            ->where('equity_account', '!=', '1')
            ->whereIn('order_type', ['vip', 'store'])
            ->where('vip_type', '!=', '2')
            ->get();
            echo '共订单:' . count($orders) . '个';
            foreach ($orders as $key => $order) {
                if($order['equity_account'] == '1'){
                    continue;
                }
                $user = UserModel::where('id', $order['user_id'])->first();
                if($user == null){
                    continue;
                }
                if($order['order_type'] == 'vip' && $order['vip_type'] == '1'){
                    EquityService::vipEquity($order, $user);
                }
                if($order['order_type'] == 'store'){
                    EquityService::storeEquity($order, $user);
                }
            }
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }
}
