<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gold\GoldDayConfig;
use App\Models\User\Gold as UserGold;
use App\Models\User\GoldDay as UserGoldDay;
use App\Models\User\GoldDaySta as UserGoldDaySta;
use App\Models\Gold\GoldDaySta;
use App\Models\Gold\GoldDayUser as GoldDayUserModel;
use App\Models\Order\Recharge as OrderRecharge;
use DB;

class GoldDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GoldDay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GoldDay';

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
                $this->log("开始红利脚本:" . date('Y-m-d H:i:s'));
                $date = date('Y-m-d');
                $GoldDayConfig = GoldDayConfig::where('date', '=', $date)
                ->first();
                if(empty($GoldDayConfig)){
                    $GoldDayConfig = new GoldDayConfig();
                    $GoldDayConfig->date = $date;
                    $GoldDayConfig->bouns_amount = 0;
                    $GoldDayConfig->save();
                }
                $bouns_amount = $GoldDayConfig->bouns_amount;
                $this->log("今天红利金额". $bouns_amount . ' ' . date('Y-m-d H:i:s'));
                $yes_date = date("Y-m-d",strtotime("-1 day"));



                $GoldDaySta = GoldDaySta::where('date', '=', $yes_date)
                ->first();

                $user_golds = GoldDayUserModel::where('date', '=', $yes_date)
                ->where('gold_number', '>', 0)
                ->get();

                $gold_numbers = $GoldDaySta->gold_number;

                $available_gold_number = $GoldDaySta->available_gold_number;

                $gold_unit_amount = $bouns_amount / $available_gold_number;

                foreach ($user_golds as $key => $user_gold) {
                    $user_id = $user_gold->user_id;
                    $UserGoldDay = UserGoldDay::where('user_id', '=', $user_id)->where('date', $date)->first();
                    if(!empty($UserGoldDay)){
                        continue;
                    }
                    $bonus_amount_day = $gold_unit_amount * $user_gold->gold_number;
                    $UserGoldDay = new UserGoldDay();
                    $UserGoldDay->user_id = $user_id;
                    $UserGoldDay->date = $date;
                    $UserGoldDay->bonus_amount = $bonus_amount_day;
                    $UserGoldDay->content = '红利收益';
                    $UserGoldDay->save();
                    $user_gold_model = UserGold::where('user_id', '=', $user_id)->first();
                    if(!empty($user_gold_model)){
                         $income_total = $user_gold_model->income_total + $bonus_amount_day;
                        $bonus_amount = $user_gold_model->bonus_amount + $bonus_amount_day;
                        $user_gold_model->income_total = $income_total;
                        $user_gold_model->bonus_amount = $bonus_amount;
                        $user_gold_model->save();
                    }
                }
                $UserGoldDaySta = UserGoldDaySta::first();
                if(empty($UserGoldDaySta)){
                    $UserGoldDaySta = new UserGoldDaySta();
                }
                $UserGoldDaySta->actual_issued_amount += $bouns_amount;
                $UserGoldDaySta->save();
                $GoldDayConfig->should_issued_amount = $UserGoldDaySta->should_issued_amount;
                $GoldDayConfig->status = '1';
                $GoldDayConfig->gold_number = $available_gold_number;
                $GoldDayConfig->handle_time = date('Y-m-d H:i:s');
                $GoldDayConfig->save();
                $this->log("结束红利脚本". date('Y-m-d H:i:s'));
            });
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
        
    }

    private function log($str){
        $date = date('Y-m-d');
        $log = new \Monolog\Logger('console');
        $log->pushHandler(
            new \Monolog\Handler\StreamHandler(storage_path('logs/GoldDay/'.  $date . '.log'), \Monolog\Logger::INFO)
        );
        $log->addInfo($str);
    }
}
