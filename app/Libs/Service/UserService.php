<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Repository\User as UserRepository;
use App\Repository\Base as BaseRepository;
use App\Models\User\User as UserModel;
use App\Models\User\Wallet as UserWalletModel;
use App\Models\User\WalletRecord as WalletRecordModel;
use App\Models\User\Integral as UserIntegralModel;
use App\Models\User\IntegralRecord as IntegralRecordModel;
use App\Models\User\Reward as UserRewardModel;
use App\Models\User\RewardRecord as RewardRecordModel;
use App\Models\User\RegisteredRecord as RegisteredRecordModel;
use App\Models\User\PasswordResetCode as PasswordResetCodeModel;
use App\Models\User\Commission as UserCommissionModel;
use App\Models\User\CommissionReward as UserCommissionRewardModel;
use App\Models\User\Gold as UserGoldModel;
use App\Models\Gold\Config as GoldConfigModel;
use App\Models\User\SubIntegralRecord as UserSubIntegralRecordModel;
use App\Models\User\ShareDate as ShareDateModel;
use App\Models\Gold\GoldDayConfig;
use App\Models\Gold\GoldDaySta;
use App\Models\User\GoldDay as UserGoldDayModel;
class UserService
{
	/**
     * @var Singleton reference to singleton instance
     */
	private static $_instance;  

	
	/**
     * 构造函数私有，不允许在外部实例化
     *
    */
	private function __construct(){}

	/**
     * 防止对象实例被克隆
     *
     * @return void
    */
	private function __clone() {}
	
	/**
	 * Create a new Repository instance.单例模式
	 *
	 * @return void
	 */
    public static function getInstance()    
    {    
        if(! (self::$_instance instanceof self) ) {    
            self::$_instance = new self();   
        }
        return self::$_instance;    
    }  

    /**
     * 保存用户基本信息
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public function changeInfo($user = null, $data = []){
        $result = ['code' => 'Error', 'message' => ''];
        //用户验证
        if($user == null){
            $result['code'] = "UNAUTH";
            $result['message'] = '对不起,您未登录';
            return $result;
        }
      	//数据校验
        $validator = Validator::make($data, [
            'fullname' => 'required|max:100'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }

        //更新数据
      	UserRepository::getInstance()->updateUser($data, [['id', '=', $user->id]]);

      	$result['code'] = "Success";
        $result['message'] = '保存成功';
        return $result;
    }

   /**
    * 修改密码
    * @param  object $user UserModel
    * @param  [type] $current_password  当前密码
    * @param  [type] $new_password   新密码
    * @param  [type] $confirm_new_password 新密码确认
    * @return array
    */
    public function changePwd($user = null, $current_password, $new_password, $confirm_new_password){
        $result = ['code' => 'Error', 'message' => ''];
        //用户验证
        if($user == null){
            $result['code'] = "UNAUTH";
            $result['message'] = '对不起,您未登录';
            return $result;
        }

        $data = ['current_password' => $current_password, 
        'new_password' => $new_password, 'confirm_new_password' => $confirm_new_password];
        //数据校验
        $validator = Validator::make($data, [
            'current_password' => 'required',
            'new_password' => 'required|min:6|max:50',
            'confirm_new_password' => 'required|same:new_password'
        ]);
        //校验失败
        if($validator->fails()){
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }

        //检查当前密码是否正确
        if(Hash::check($current_password, $user->password)){
            $data = [
                'password' => bcrypt($new_password)
            ];
            //更新密码
            UserRepository::getInstance()->updateUser($data, [['id', '=', $user->id]]);
            $result['code'] = "Success";
            $result['message'] = '密码更新成功';

        }else{
            $result['code'] = "Error";
            $result['message'] = '对不起,当前密码错误';
        }
        return $result;
    }

     /**
    * 修改交易密码
    * @param  object $user UserModel
    * @param  [type] $current_password  当前密码
    * @param  [type] $new_password   新密码
    * @param  [type] $confirm_new_password 新密码确认
    * @return array
    */
    public function changeTransactionPwd($user = null, $new_password, $confirm_new_password){
        $result = ['code' => 'Error', 'message' => ''];
        //用户验证
        if($user == null){
            $result['code'] = "UNAUTH";
            $result['message'] = '对不起,您未登录';
            return $result;
        }

        $data = ['new_password' => $new_password, 'confirm_new_password' => $confirm_new_password];
        //数据校验
        $validator = Validator::make($data, [
            'new_password' => 'required|min:6|max:50',
            'confirm_new_password' => 'required|same:new_password'
        ]);
        //校验失败
        if($validator->fails()){
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }

        $data = [
            'transaction_password' => bcrypt($new_password)
        ];
        //更新密码
        UserRepository::getInstance()->updateUser($data, [['id', '=', $user->id]]);
        $result['code'] = "Success";
        $result['message'] = '交易密码更新成功';
        return $result;
    }

    /**
     * 查找用户
     * @param  array   $where    查询条件
     * @param  array   $field    查询字段
     * @return array
     */
    public function findUser($where = [] , $field = []){
        $user_list = UserRepository::getInstance()->findUser($where, $field);
        return $user_list;
    }

    /**
     * 更新用户信息
     * @param  array  $data 保存数据
     * @param  array  $where  查询条件
     * @return array
     */
    public function updateUser($data = [], $where = []){
        if(empty($data)){
            return false;
        }
        $result = UserRepository::getInstance()->updateUser($data, $where);
        return $result;
    }

    //开通vip
    public function openVip($user){
        $result = \DB::transaction(function() use ($user) {
            if($user->is_vip == '1'){
                return false;
            }
            if($user->is_vip != '1'){
                $user->is_vip = '1';
            }
            if($user->level_status <=0){
                $user->level_status = '1';
            }
            if(empty($user->vip_end_date)){
                $next_time = strtotime(date("Y-m-d", strtotime("+1 day")));
                $expire_time = strtotime('+1year', $next_time);
                $user->vip_end_date = date('Y-m-d H:i:s', $expire_time);
            }
            $user->save();
            CardService::createDefaultCard($user);
            $referrer_user_id = $user->referrer_user_id;
            if($referrer_user_id > 0){
                $receive_user = UserModel::find($referrer_user_id);
                $this->userVipNotice($user, $receive_user);
            }
            $user_gold = $user->gold()->first();
            if(empty($user_gold)){
                $user_gold = new UserGoldModel();
                $user_gold->user_id = $user->id;
                $user_gold->save();
            }
            return true;
        });
        return $result;
    }

     //开通vip
    public function autoOpenVip($user, $vip_end_date = null){
        $result = \DB::transaction(function() use ($user, $vip_end_date) {
            if($user->is_vip == '1'){
                return false;
            }
            if($user->is_vip != '1'){
                $user->is_vip = '1';
            }
            if($user->level_status <=0){
                $user->level_status = '1';
            }
            if(empty($user->vip_end_date)){
                if($vip_end_date == null){
                    $next_time = strtotime(date("Y-m-d", strtotime("+1 day")));
                    $expire_time = strtotime('+1year', $next_time);
                    $vip_end_date = date('Y-m-d H:i:s', $expire_time);
                }
                $user->vip_end_date = $vip_end_date;
            }
            $user->is_auto_open_vip = '1';
            $user->save();
            CardService::createDefaultCard($user);
            StoreService::autoOpenStore($user);
            $user_gold = $user->gold()->first();
            if(empty($user_gold)){
                $user_gold = new UserGoldModel();
                $user_gold->user_id = $user->id;
                $user_gold->save();
            }
            return true;
        });
        return $result;
    }

    //续费vip
    public function renewalVIP($user){
        $result = \DB::transaction(function() use ($user) {
            if($user->is_vip != '1'){
                $user->is_vip = '1';
            }
            if($user->level_status <=0){
                $user->level_status = '1';
            }
            if(empty($user->vip_end_date)){
                $user->vip_end_date = date('Y-m-d H:i:s',strtotime('+1year'));
            } else {
                $vip_end_time = strtotime($user->vip_end_date);
                $now = time();
                if($vip_end_time < $now){
                    $vip_end_time = $now;
                }
                $next_time = strtotime(date("Y-m-d", strtotime("+1 day")));
                $new_vip_end_time = strtotime('+1year', $next_time);
                $user->vip_end_date = date('Y-m-d H:i:s', $new_vip_end_time);
            }
            $user->save();
            return true;
        });
        return $result;
    }

    /**
     * 更新vip状态
     * @return [type] [description]
     */
    public function updateVipLevel($user){
        //直接推荐人
        $referrer_user_id = $user->referrer_user_id;

        if(!$referrer_user_id || $referrer_user_id <=0){
            return false;
        }

        //推荐人
        $referrer_user = UserModel::where('id', $referrer_user_id)->first();
        if($referrer_user == null){
            return false;
        }

        //直推满10人，升级为vip金
        $refer_user_count = UserModel::where('referrer_user_id', $referrer_user->id)
        ->where('level_status', '>=', '1')
        ->where('is_vip', '=', '1')
        ->count();
        if($refer_user_count >=5){
            if($referrer_user->level_status < 2){
                $referrer_user->level_status = '2';
            }
        }

        $referrer_user->honor_value = $refer_user_count;

        //vip金个数
        $user_level2_count = UserModel::where('referrer_user_id', $referrer_user->id)
        ->where('level_status', '>=', '2')
        ->where('is_vip', '=', '1')
        ->count();

        $referrer_user->honor_vip_value = $user_level2_count;

        //直推满100人,并且vip金满40
        if($refer_user_count >=100){
            if($user_level2_count >= 40){
                if($referrer_user->level_status < 2){
                    $referrer_user->level_status = '3';
                }
            }
        }

        $referrer_user->save();
    }

    //我的钱包收入
    public function userWalletIncome($user, $amount, $content = ''){
        if($amount <=0){
            return false;
        }
        $wallet = $user->wallet()->first();
        if($wallet == null){
            $wallet = new UserWalletModel();
            $wallet->user_id = $user->id;
        }
        $wallet->balance_amount = $wallet->balance_amount + $amount;
        $r = $wallet->save();
        if($r){
            $walletRecordModel = new WalletRecordModel();
            $walletRecordModel->user_id = $user->id;
            $walletRecordModel->amount = $amount;
            $walletRecordModel->content = $content;
            $walletRecordModel->save();
        }
    }


    //我的赏金收入
    public function userRewardIncome($user, $amount, $content = '', $remarks = '', $order_recharge_id = 0, $order_id = 0){
        if($amount <=0){
            return false;
        }
        $userReward = $user->reward()->first();
        if($userReward == null){
            $userReward = new UserRewardModel();
            $userReward->user_id = $user->id;
        }
        $userReward->amount = $userReward->amount + $amount;
        $r = $userReward->save();
        if($r){
            $RewardRecordModel = new RewardRecordModel();
            $RewardRecordModel->user_id = $user->id;
            $RewardRecordModel->amount = $amount;
            $RewardRecordModel->content = $content;
            $RewardRecordModel->remarks = $remarks;
            $RewardRecordModel->order_recharge_id = $order_recharge_id;
            $RewardRecordModel->order_id = $order_id;
            $RewardRecordModel->type = '1';
            $RewardRecordModel->save();
        }
    }

    //我的赏金支出
    public function userRewardOut($user, $amount, $content = ''){
        if($amount <=0){
            return false;
        }
        $userReward = $user->reward()->first();
        if($userReward == null){
            return;
        }
        $userReward->amount = $userReward->amount - $amount;
        if($userReward->amount <0){
            $userReward->amount = 0;
        }
        $r = $userReward->save();
        if($r){
            $RewardRecordModel = new RewardRecordModel();
            $RewardRecordModel->user_id = $user->id;
            $RewardRecordModel->amount = $amount;
            $RewardRecordModel->content = $content;
            $RewardRecordModel->type = '2';
            $RewardRecordModel->save();
        }
    }

    //有赏积分收入
    public function userIntegralIncome($user, $point, $content = '', $store_sales_points = 0, $remarks = '', $order_recharge_id = 0, $order_id = 0){
        if($point <=0){
            return false;
        }
        $integral = $user->integral()->first();
        if($integral == null){
            $integral = new UserIntegralModel();
            $integral->user_id = $user->id;
        }
        $integral->point = $integral->point + $point;
        if($store_sales_points){
            $integral->store_sales_points += $store_sales_points;
        }
        if($integral->store_sales_points > $integral->point){
            $integral->store_sales_points = $integral->point;
        }
        $r = $integral->save();
        if($r){
            $integralRecordModel = new IntegralRecordModel();
            $integralRecordModel->user_id = $user->id;
            $integralRecordModel->type = '1';
            $integralRecordModel->point = $point;
            $integralRecordModel->content = $content;
            $integralRecordModel->remarks = $remarks;
            $integralRecordModel->order_recharge_id = $order_recharge_id;
            $integralRecordModel->order_id = $order_id;
            $integralRecordModel->save();
        }
    }

    //有赏积分消费
    public function userIntegralOut($user, $point, $content = ''){
        if($point <=0){
            return false;
        }
        $integral = $user->integral()->first();
        if($integral == null){
            $integral = new UserIntegralModel();
            $integral->user_id = $user->id;
        }
        $new_point = $integral->point - $point;
        if($new_point < 0){
            $new_point = 0;
        }
        $integral->point = $new_point;
        if($integral->store_sales_points > $integral->point){
            $integral->store_sales_points = $integral->point;
        }
        $r = $integral->save();
        if($r){
            $integralRecordModel = new IntegralRecordModel();
            $integralRecordModel->user_id = $user->id;
            $integralRecordModel->type = '2';
            $integralRecordModel->point = $point;
            $integralRecordModel->content = $content;
            $integralRecordModel->save();
        }
    }

    //有赏积分收入
    public function userSubIntegralRecord($user, $data){
        if($data['amount'] <=0){
            return false;
        }
        $UserSubIntegralRecordModel = new UserSubIntegralRecordModel();
        $UserSubIntegralRecordModel->user_id = $user->id;
        $UserSubIntegralRecordModel->type = $data['type'];
        $UserSubIntegralRecordModel->amount = $data['amount'];
        $UserSubIntegralRecordModel->content = $data['content'];
        $UserSubIntegralRecordModel->remarks = $data['remarks'];
        if(isset($data['order_recharge_id'])){
            $UserSubIntegralRecordModel->order_recharge_id = $data['order_recharge_id'];
        }
        $UserSubIntegralRecordModel->save();
    }

    //我的佣金收入
    public function userCommissionIn($user, $amount, $content = '', $order_recharge_id, $manager_commission = 0){
        if($amount <=0 && $manager_commission<=0){
            return false;
        }
        $userCommission = $user->commission()->first();
        if($userCommission == null){
            $userCommission = new UserCommissionModel();
            $userCommission->user_id = $user->id;
        }
        $userCommission->gift_commission = $userCommission->gift_commission + $amount;
        $userCommission->manager_commission = $userCommission->manager_commission + $manager_commission;
        $r = $userCommission->save();
        if($r){
            $UserCommissionRewardModel = new UserCommissionRewardModel();
            $UserCommissionRewardModel->user_id = $user->id;
            $UserCommissionRewardModel->gift_commission = $amount;
            $UserCommissionRewardModel->order_recharge_id = $order_recharge_id;
            $UserCommissionRewardModel->content = $content;
            $UserCommissionRewardModel->type = '1';
            $UserCommissionRewardModel->manager_commission = $manager_commission;
            $UserCommissionRewardModel->save();
        }
    }

    //我的礼包佣金收入
    public function userCommissionOut($user, $amount, $content = '', $order_recharge_id = 0, $manager_commission = 0){
        if($amount <=0){
            return false;
        }
        $userCommission = $user->commission()->first();
        if($userCommission == null){
            $userCommission = new UserCommissionModel();
            $userCommission->user_id = $user->id;
        }
        $userCommission->gift_commission = $userCommission->gift_commission - $amount;
        if($userCommission->gift_commission <=0){
            $userCommission->gift_commission = 0;
        }
        $userCommission->manager_commission = $userCommission->manager_commission - $manager_commission;
        if($userCommission->manager_commission <=0){
            $userCommission->manager_commission = 0;
        }
        $r = $userCommission->save();
        if($r){
            $UserCommissionRewardModel = new UserCommissionRewardModel();
            $UserCommissionRewardModel->user_id = $user->id;
            $UserCommissionRewardModel->gift_commission = $amount;
            $UserCommissionRewardModel->manager_commission = $manager_commission;
            $UserCommissionRewardModel->order_recharge_id = $order_recharge_id;
            $UserCommissionRewardModel->content = $content;
            $UserCommissionRewardModel->type = '2';
            $UserCommissionRewardModel->save();
        }
    }


    //我的金麦增加
    public function userGoldNumberIn($user, $gold_number){
        if($gold_number <=0){
            return false;
        }
        $UserGoldModel = $user->gold()->first();
        if($UserGoldModel == null){
            $UserGoldModel = new UserGoldModel();
            $UserGoldModel->user_id = $user->id;
        }
        $UserGoldModel->gold_number = $UserGoldModel->gold_number + $gold_number;
        $r = $UserGoldModel->save();
        return $r;
    }


     //我的金麦增加
    public function userGoldNumberOut($user, $gold_number){
        if($gold_number <=0){
            return false;
        }
        $UserGoldModel = $user->gold()->first();
        if($UserGoldModel == null){
            $UserGoldModel = new UserGoldModel();
            $UserGoldModel->user_id = $user->id;
        }
        $UserGoldModel->gold_number = $UserGoldModel->gold_number - $gold_number;
        if($UserGoldModel->gold_number <=0){
            $UserGoldModel->gold_number = 0;
        }
        $r = $UserGoldModel->save();
        return $r;
    }



    //用户注册通知
    public function userRegisterNotice($user, $receive_user){
        try{
            if($receive_user != null){
                $user_name = isset($user['fullname']) ? $user['fullname'] : '';
                if($user_name == ''){
                    $user_name = isset($user['nickname']) ? $user['nickname'] : '';
                }
                $user_name = $user_name . " (" . (isset($user['phone']) ? $user['phone'] : '') . ')';
                $data = [
                    'user_id' => $receive_user->id,
                    'name' => "好友正在注册",
                    'link' => '/account/u_referrer',
                    'content' => "禀奏皇上，好友" . $user_name . "已注册账户，请带他一起玩转人人有赏！名单在“我的帐户-我的潜在客户”中查看！"
                ];
                MessageService::insert($data);
            }
        }catch(\Exception $e){

        }
    }

     //用户vip注册通知
    public function userVipNotice($user, $receive_user){
        try{
            if($receive_user != null){
                $user_name = isset($user['fullname']) ? $user['fullname'] : '';
                if($user_name == ''){
                    $user_name = isset($user['nickname']) ? $user['nickname'] : '';
                }
                $user_name = $user_name . " " . (isset($user['phone']) ? $user['phone'] : '');
                $data = [
                    'user_id' => $receive_user->id,
                    'name' => "好友已成功开通vip用户",
                    'link' => '/account/referrer',
                    'content' => "好友" . $user_name . "已成为战友，互相学习，一起成长！关注好友成长在“我的帐户-我的战友”中查看！"
                ];
                MessageService::insert($data);
            }
        }catch(\Exception $e){

        }
    }

    //我的推荐人
    public function referrer($user, $pageSize){
        $level_status = config('user.level_status');
        $user_id = $user->id;
        $referrers = UserModel::where('referrer_user_id', $user_id)
        ->where('is_vip', '=', '1')
        ->where('level_status', '>=', '1')
        ->orderBy('id', 'desc')
        ->paginate($pageSize);
        $date = date("Y/m/d");
        $today = date("Y-m-d");
        $day = \Helper::getthemonth($today);
        foreach ($referrers as $key => $referrer) {
            $referrers[$key]->level_status_text = $level_status[$referrer['level_status']];
            $rf_count = UserModel::where('referrer_user_id', '=', $referrer->id)
            ->where('is_vip', '=', '1')
            ->where('level_status', '>=', '1')
            ->count();
            $referrers[$key]->rf_count = $rf_count;
            $rf_month_count = UserModel::where('referrer_user_id', '=', $referrer->id)
            ->where('created_at', '>=', $day[0])
            ->where('created_at', '<=', $day[1])
            ->where('level_status', '>=', '1')
            ->where('is_vip', '=', '1')
            ->count();
            $referrers[$key]->rf_month_count = $rf_month_count;
            $referrers[$key]->rupgrade_link = '';
            $referrers[$key]->rupgrade = 1;
            $u_id = $referrer['u_id'];
            $link = \Helper::route('account_vip_rupgrade', ['uid' => $u_id]);
            $referrers[$key]->rupgrade_link = $link;
        }
        $referrers = $referrers->toArray();
        return $referrers;
    }

    public function getUserGold($user){

        $goldConfig = GoldConfigModel::first();

        $gift_unit = $goldConfig['gift_unit'];

        $user_gold = $user->gold()->first();

        $gold_number = $user_gold['gold_number'];

        $gold_total = $gold_number * $gift_unit;

        $total_amount = $user_gold['bonus_amount'] + $gold_total;

        $data = [
            'gift_unit' => $gift_unit,
            'total_amount' => $total_amount,
            'income_total' => $user_gold['income_total'],
            'bonus_amount' => $user_gold['bonus_amount'],
            'gold_number' => $gold_number,
            'gold_total' => $gold_total
        ];

        return $data;
    }

    public function getGoldData(){

        $user = \Auth::user();

        $gift_commission = 0;

        $user_commission = $user->commission()->first();

        if(!empty($user_commission)){
            $gift_commission = $user_commission->gift_commission;
        }

        $goldConfig = GoldConfigModel::first();

        $gift_unit = $goldConfig['gift_unit'];

        $day_date = date("Y-m-d");

        $yes_date = date("Y-m-d",strtotime("-1 day"));

        $GoldDaySta = GoldDaySta::where('date', '=', $yes_date)
        ->first();

        $day_bonus_unit = 0;

        if(!empty($GoldDaySta)){
            $GoldDayConfig = GoldDayConfig::where('date', '=', $day_date)
                ->first();
            if(!empty($GoldDayConfig)){
                $available_gold_number = $GoldDaySta->available_gold_number;
                $bouns_amount = $GoldDayConfig->bouns_amount;
                if($available_gold_number > 0){
                    $day_bonus_unit = $bouns_amount / $available_gold_number;
                }
            }
        }

        $user_gold = $user->gold()->first();

        if(empty($user_gold)){
            $user_gold = new UserGoldModel();
            $user_gold->user_id = $user->id;
            $user_gold->save();
            $user_gold = $user->gold()->first();
        }

        $gold_number = $user_gold['gold_number'];

        $gold_total = $gold_number * $gift_unit;

        $total_amount = $user_gold['bonus_amount'] + $gold_total;


        $yesterday_bonus_amount = 0;

        $date = date("Y-m-d",strtotime("-1 day"));

        $user_gold_yesterday = UserGoldDayModel::where('date', $date)->where('user_id', '=', $user->id)->first();

        if(!empty($user_gold_yesterday)){
            $yesterday_bonus_amount = $user_gold_yesterday->bonus_amount;
        }
        return [
            'gift_commission' => $gift_commission,
            'gift_unit' => $gift_unit,
            'day_bonus_unit' => $day_bonus_unit,
            'total_amount' => $total_amount,
            'income_total' => $user_gold['income_total'],
            'bonus_amount' => $user_gold['bonus_amount'],
            'gold_number' => $gold_number,
            'gold_total' => $gold_total,
            //'today_bonus_amount' => $today_bonus_amount,
            'yesterday_bonus_amount' => $yesterday_bonus_amount
        ];
    }

    public function shareDate($sid){
        $user = UserModel::where('u_id', '=', $sid)->first();
        if(!empty($user)){
            $user_id = $user->id;
            $date = date('Y-m-d'); 
            $ShareDateModel = ShareDateModel::where('user_id', $user_id)->where('date', $date)->first();
            $is_new = false;
            if(empty($ShareDateModel)){
                $ShareDateModel = new ShareDateModel();
                $ShareDateModel->user_id = $user_id;
                $ShareDateModel->date = $date;
                $ShareDateModel->count = 1;
                $is_new = true;
            } else {
                $ShareDateModel->count = $ShareDateModel->count + 1;
            }
            $ShareDateModel->status = '1';
            $res = $ShareDateModel->save();
            if($is_new && $user->is_vip == '1'){
                $sub_integral = config('user.user_ref_sub_integral_amount');
                $user->sub_integral_amount = $user->sub_integral_amount + $sub_integral;
                $user->save();
                UserService::getInstance()->userSubIntegralRecord($user, [
                    "type" => '1',
                    "amount" => $sub_integral,
                    "content" => '分享朋友圈赠送代购积分',
                    "remarks" => '分享朋友圈赠送代购积分',
                    'order_recharge_id' => 0
                ]);
            }
        }
    }
}