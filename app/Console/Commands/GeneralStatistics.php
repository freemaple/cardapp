<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User\User as UserModel;
use App\Models\Order\Recharge as OrderRechargeModel;
use App\Models\User\StatisticsDate as UserStatisticsDateModel;
use App\Models\User\Statistics as UserStatisticsModel;

class GeneralStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GeneralStatistics  {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GeneralStatistics';

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
            // 获取一个指定的选项值
            $date = $this->option('date');
            if($date == null){
                $date = date("Y-m-d", strtotime("-1 day"));
            } else {
                if(!strtotime($date)){
                    echo '日期格式不对！';
                    return false;
                }
            }
            
            $this->log("开始统计: $date 订单 ");
            $orders = OrderRechargeModel::where('status', '2')
            ->whereRaw("date_format(paid_at,'%Y-%m-%d') = '" . $date . "'")
            ->whereIn('order_type', ['vip', 'store'])
            ->get();
            $this->log("总共有" .count($orders) . ' 订单 ');
            $statistics = [];
            foreach ($orders as $key => $order) {
                $this->log('开始订单' . $order['order_no'] . '开始统计 ');
                $user_id = $order['user_id'];
                $user = UserModel::where('id', $user_id)->first();
                if($user == null){
                    continue;
                }
                if($user['referrer_user_id'] == null){
                    continue;
                }
                $g_u = $this->findG($user);
                if($g_u['u_m'] != null){
                    $this->log('总监：' . $g_u['u_m']['id'] . ' ' . $g_u['u_m']['fullname'] .' ');
                } else {
                    $this->log('总监：无');
                }
                if($g_u['u_d'] != null){
                    $this->log('总经理: ' . $g_u['u_d']['id'] . ' ' . $g_u['u_d']['fullname'] .' ');
                } else {
                    $this->log('总经理：无');
                }
                foreach ($g_u as $key => $u) {
                    if($u == null){
                        continue;
                    }
                    $u_id = $u['id'];
                    if(!isset($statistics[$u_id])){
                        $statistics[$u_id] = [
                            'vip_open_number' => 0,
                            'vip_renewal_number' => 0,
                            'store_number' => 0
                        ];
                    }
                    if($order['order_type'] == 'vip'){
                        if($order['vip_type'] == '1'){
                            $statistics[$u_id]['vip_open_number'] = $statistics[$u_id]['vip_open_number'] + 1;
                        }
                        else if($order['vip_type'] == '2'){
                            $statistics[$u_id]['vip_renewal_number'] = $statistics[$u_id]['vip_renewal_number'] + 1;
                        }
                    }
                    else if($order['order_type'] == 'store'){
                        $statistics[$u_id]['store_number'] = $statistics[$u_id]['store_number'] + 1;
                    }
                }
                $this->log('结束订单' . $order['order_no'] . '统计 ');
            }
            $this->log('统计结果' . json_encode($statistics) . ' ');
            foreach ($statistics as $s_id => $s_data) {
                $this->log('user_id: ' . $s_id . ' 记录, 数据：' . json_encode($s_data));
                $user_statistics = UserStatisticsDateModel::where('date', $date)->where('user_id', $s_id)->first();
                if($user_statistics == null){
                    $user_statistics = new UserStatisticsDateModel();
                    $user_statistics->user_id = $s_id;
                    $user_statistics->date = $date;
                }
                if(empty($user_statistics->year)){
                    $year = date('Y', strtotime($date));
                    $user_statistics->year = $year;
                }
                if(empty($user_statistics->month)){
                    $month = date('m', strtotime($date));
                     $user_statistics->month = $month;
                }
                if(isset($s_data['vip_open_number'])){
                    $user_statistics->vip_open_number = $s_data['vip_open_number'];
                }
                if(isset($s_data['vip_renewal_number'])){
                    $user_statistics->vip_renewal_number = $s_data['vip_renewal_number'];
                }
                if(isset($s_data['store_number'])){
                    $user_statistics->store_number = $s_data['store_number'];
                }
                $user_statistics->save();
                $this->log('user_id: ' . $s_id . ' 成功');
                $this->updateUserStatistics($s_id);
            }
            $this->log("结束统计: $date 订单");
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }

    //总统计
    private function updateUserStatistics($user_id){
        $user_statistics_date = UserStatisticsDateModel::where('user_id', $user_id)->first();
        if($user_statistics_date == null){
            return false;
        }

        $vip_open_number = UserStatisticsDateModel::where('user_id', $user_id)->sum('vip_open_number');

        $vip_renewal_number = UserStatisticsDateModel::where('user_id', $user_id)->sum('vip_renewal_number');

        $store_number = UserStatisticsDateModel::where('user_id', $user_id)->sum('store_number');

        $user_statistics = UserStatisticsModel::where('user_id', $user_id)->first();

        if($user_statistics == null){
            $user_statistics = new UserStatisticsModel();
            $user_statistics->user_id = $user_id;
        }
        $user_statistics->vip_open_number = $vip_open_number;
        $user_statistics->vip_renewal_number = $vip_renewal_number;
        $user_statistics->store_number = $store_number;
        $user_statistics->save();
        $this->log("统计$user_id: 总数据成功");
    }

    //20以上总经理和总监
    public function findGl($user){
        $u_m = null;
        $u_d = null;
        $level = 1;
        $user_referrer = ReferrerLevelModel::where('user_id', $user->id)->first();
        if($user_referrer != null){

        }
        for ($i = 1; $i<=32; $i++) {
            if($user['referrer_user_id'] <= 0){
                break;
            }
            $user = UserModel::where('id', $user['referrer_user_id'])->first();
            if($user == null){
                break;
            }

            //总经理
            if($u_m == null && $user['user_type'] == 'manager'){
                $u_m = $user;
                break;
            }

            //总监
            if($u_d == null && $user['user_type'] == 'director'){
                $u_d = $user;
            }
        }
        return ['u_m' => $u_m, 'u_d' => $u_d];
    }

    //20以上总经理和总监
    public function findG($user){
        $u_m = null;
        $u_d = null;
        //总经理
        $director_id = $user->director_id;
         //总经理
        $manager_id = $user->manager_id;
        if($director_id > 0){
            $u_d = UserModel::where('id', $director_id)->where('user_type', '=', 'director')->first();
            if($u_d == null){
                $this->log("异常$director_id 不是总经理");
            }
        }
        if($manager_id > 0){
            $u_m = UserModel::where('id', $manager_id)->where('user_type', '=',  'manager')->first();
            if($u_m == null){
                $this->log("异常 " . $manager_id . "不是总监");
            }
        }
        return ['u_m' => $u_m, 'u_d' => $u_d];
    }

    private function log($str){
        $date = date('Y-m-d');
        $log = new \Monolog\Logger('console');
        $log->pushHandler(
            new \Monolog\Handler\StreamHandler(storage_path('logs/GeneralStatistics/'.  $date . '.log'), \Monolog\Logger::INFO)
        );
        $log->addInfo($str);
    }
}
