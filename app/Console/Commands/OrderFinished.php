<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order\Order as OrderModel;
use App\Libs\Service\OrderService;

class OrderFinished extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OrderFinished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Orders Finished';

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
            $this->log("开始订单完成脚本:" . date('Y-m-d H:i:s'));
            $start_time = strtotime(date("Y-m-d", strtotime("-21 day")));
            $start_date = date('Y-m-d', $start_time);
            $orders = OrderModel::where('order_status_code', '=', 'shipped')
            ->where('shipped_at', '<', $start_date)
            ->get();
            $this->log("共需要自动完成订单". count($orders). ' 个 ' . date('Y-m-d H:i:s'));
            foreach ($orders as $key => $order) {
                if($order == null){
                    return false;
                }
                if($order->order_status_code == 'cancel'){
                    return false;
                }
                if($order->order_status_code == 'finished'){
                    return false;
                }
                if($order->order_status_code != 'shipped'){
                    return false;
                }
                $result = OrderService::orderFinished($order);
                $this->log("自动完成订单". $order['order_no'] . ' ' . date('Y-m-d H:i:s'));
            }
            $this->log("结束订单完成脚本". date('Y-m-d H:i:s'));
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
        
    }


     private function log($str){
        $date = date('Y-m-d');
        $log = new \Monolog\Logger('console');
        $log->pushHandler(
            new \Monolog\Handler\StreamHandler(storage_path('logs/OrdersFinished/'.  $date . '.log'), \Monolog\Logger::INFO)
        );
        $log->addInfo($str);
    }
}
