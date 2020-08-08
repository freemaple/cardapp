<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User\User as UserModel;
use DB;
use App\Libs\Service\UserService;
use App\Libs\Service\MessageService;

class VipHandel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'VipHandel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'VipHandel';

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
            $date = date('Y-m-d H:i:s');
            $users = UserModel::where('vip_end_date', '<', $date)->where('is_vip', '1')->get();
            foreach ($users as $key => $user_item) {
                dump($user_item->fullname);
                //continue;
                DB::transaction(function() use($user_item){
                    $user_item->is_vip = '0';
                    $res = $user_item->save();
                    if($res){
                        $data = [
                            'user_id' => $user_item->id,
                            'name' => "您的金麦用户已到期",
                            'link' => '/account/vipUpgrade?vip_type=renewal',
                            'content' => "您的金麦用户已到期，请立即购买礼包进行续费，以免影响红利收入！"
                        ];
                        //dump($data);
                        MessageService::insert($data);
                        dump('success');
                    }
                });
               
            }
                
        } catch(\Exception $e){
            echo $e->getMessage();
            \Log::info($e->getMessage());
        }
    }
}
