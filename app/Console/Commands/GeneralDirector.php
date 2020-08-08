<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User\User as UserModel;
use App\Models\Order\Recharge as OrderRechargeModel;
use App\Models\User\StatisticsDate as UserStatisticsDateModel;
use App\Models\User\Statistics as UserStatisticsModel;

class GeneralDirector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GeneralDirector';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GeneralDirector';

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
            $dusers = UserModel::where('user_type', '=', 'director')->get();
           dump(count($dusers));
            foreach($dusers as $key => $user){
                $user_id = $user->id;
                $update_data = ['director_id' => $user_id, 'user.updated_at' => date('Y-m-d H:i:s')];
                $u = \DB::table('user')->join('user_referrer_level', 'user_referrer_level.user_id', '=', 'user.id')
                ->where('parent_ids', 'like', '%,' . $user_id . ',%')
                ->update($update_data);
            }
           /* $musers = UserModel::where('user_type', '=', 'manager')->get();
            foreach($musers as $key => $user){
                $user_id = $user->id;
                \DB::table('user')->join('user_referrer_level', 'user_referrer_level.user_id', '=', 'user.id')
                    ->where('parent_ids', 'like', '%,' . $user_id . ',%')
                    ->update(['manager_id' => $user_id, 'user.updated_at' => date('Y-m-d H:i:s')]);
            }*/
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }
}
