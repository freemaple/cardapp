<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User\User as UserModel;
use App\Models\User\ReferrerLevel as ReferrerLevelModel;

class UserReferrerLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UserReferrerLevel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UserReferrerLevel';

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
            $users = UserModel::where('referrer_user_id', '>', 0)->get();
            foreach ($users as $key => $user) {
                $parent_ids = '';
                $parent_id = $user->referrer_user_id;
                $rf_id = $user->referrer_user_id;
                if(!$rf_id){
                    continue;
                }
                $parent_ids = ',' . $rf_id;
                while($rf_id > 0){
                    $rf = UserModel::where('id', $rf_id)->first();
                    if($rf != null){
                        $rf_id = $rf->referrer_user_id;
                        if($rf_id > 0){
                            $parent_ids .= ',' . $rf_id;
                        }
                    }
                }
                if($parent_ids != ''){
                    $parent_ids = $parent_ids . ',';
                    $user_referrer = ReferrerLevelModel::where('user_id', $user->id)->first();
                    if($user_referrer == null){
                        $user_referrer = new ReferrerLevelModel();
                        $user_referrer->user_id = $user->id;
                    }
                    $user_referrer->parent_id = $parent_id;
                    $user_referrer->parent_ids = $parent_ids;
                    $user_referrer->save();
                }
            }
        } catch(\Exception $e){
            \Log::info($e->getMessage());
        }
    }
}
