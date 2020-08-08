<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User\User as UserModel;
use App\Models\Order\Recharge as OrderRechargeModel;
use App\Models\User\StatisticsDate as UserStatisticsDateModel;
use App\Models\User\Statistics as UserStatisticsModel;

class GeneralUserStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GeneralUserStatistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GeneralUserStatistics';

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
            $dates = $this->periodDate('2019-05-30', '2019-06-23');
            foreach ($dates as $key => $date) {
                \Artisan::call('GeneralStatistics', [
                    '--date' => $date
                ]);
            }
          
        } catch(\Exception $e){
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }

    private function periodDate($start_time, $end_time){
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        $i = 0;
        while ($start_time <= $end_time){
            $arr[$i]=date('Y-m-d',$start_time);
            $start_time = strtotime('+1 day',$start_time);
            $i ++;
        }
        return $arr;
    }
}
