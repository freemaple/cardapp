<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User\Gold as UserGold;
use App\Models\Gold\GoldDaySta;
use App\Models\Gold\GoldDayUser as GoldDayUserModel;
use App\Models\User\GoldDaySta as UserGoldDaySta;
use App\Models\Order\Recharge as OrderRecharge;
use DB;

class GoldDayUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GoldDayUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GoldDayUser';

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
            DB::transaction(function(){

                $this->log("开始每日金麦统计脚本:" . date('Y-m-d H:i:s'));
                $date = date("Y-m-d",strtotime("-1 day"));

                //可参加红利的用户
                $user_golds = UserGold::join('user', 'user.id', 'user_gold.user_id')
                ->where('user.is_vip', '=', '1')
                ->join('user_share_date', 'user.id', 'user_share_date.user_id')
                ->where('date', '=', $date)
                ->where('gold_number', '>', 0)
                ->get();

                //所有的金麦橞总量
                $gold_numbers = UserGold::join('user', 'user.id', 'user_gold.user_id')
                ->where('user.is_vip', '=', '1')
                ->sum('gold_number');

                //可参加金麦橞总量
                $available_gold_number = UserGold::join('user', 'user.id', 'user_gold.user_id')
                ->join('user_share_date', 'user.id', 'user_share_date.user_id')
                ->where('user.is_vip', '=', '1')
                ->where('date', '=', $date)
                ->where('status', '=', '1')
                ->sum('gold_number');

                foreach ($user_golds as $key => $user_gold) {
                    $user_id = $user_gold->user_id;
                    $GoldDayUserModel = GoldDayUserModel::where('user_id', '=', $user_id)->where('date', $date)->first();
                    if(empty($GoldDayUserModel)){
                        $GoldDayUserModel = new GoldDayUserModel();
                    }
                    $GoldDayUserModel->user_id = $user_id;
                    $GoldDayUserModel->date = $date;
                    $GoldDayUserModel->gold_number = $user_gold->gold_number;
                    $GoldDayUserModel->save();
                }
                $yes_stare_data = $date . ' 00:00:00';
                $yes_end_data = date("Y-m-d 00:00:00");

                $should_issued_amount = OrderRecharge::where('order_type', 'vip')
                ->where('status', '=', 2)
                ->where('gift_id', '>', 0)
                ->where('paid_at', '>=', $yes_stare_data)
                ->where('paid_at', '<', $yes_end_data)
                ->sum('gold_amount');

                $GoldDaySta = GoldDaySta::where('date', $date)->first();
                if(empty($GoldDaySta)){
                    $GoldDaySta = new GoldDaySta();
                    $GoldDaySta->date = $date;
                }
                $GoldDaySta->gold_number = $gold_numbers;
                $GoldDaySta->available_gold_number = $available_gold_number;
                $GoldDaySta->should_issued_amount = $should_issued_amount;
                $GoldDaySta->save();

                $UserGoldDaySta = UserGoldDaySta::first();
                if(empty($UserGoldDaySta)){
                    $UserGoldDaySta = new UserGoldDaySta();
                }

                $UserGoldDaySta->should_issued_amount += $should_issued_amount;

                $UserGoldDaySta->save();

                $this->log("结束每日金麦统计脚本". date('Y-m-d H:i:s'));
            });
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
        
    }

    private function log($str){
        $date = date('Y-m-d');
        $log = new \Monolog\Logger('console');
        $log->pushHandler(
            new \Monolog\Handler\StreamHandler(storage_path('logs/GoldDayUser/'.  $date . '.log'), \Monolog\Logger::INFO)
        );
        $log->addInfo($str);
    }
}
