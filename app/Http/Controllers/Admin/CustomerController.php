<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;
use App\Libs\Service\CustomerService;

use App\Models\User\RegisteredRecord;

use App\Models\User\User;
use App\Models\Store\Store as StoreModel;

use App\Libs\Service\UserService;
use App\Libs\Service\StoreService;
use App\Models\User\IntegralSend;
use App\Models\User\IntegralRecord;
use App\Models\User\RewardRecord;
use App\Models\User\Statistics as UserStatisticsModel;
use App\Models\User\StatisticsDate as UserStatisticsDateModel;
use App\Models\User\ReferrerLevel as ReferrerLevelModel;

use App\Cache\User as UserCache;

class CustomerController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $UserModel = new User();

        $user_type = config('user.user_type');

        $pageSize = 20;

        $form = $request->all();

        $phone = trim($request->phone);

        if($phone != null){
            $UserModel = $UserModel->where('phone', '=', $phone);
        }

        $fullname = trim($request->fullname);

        if($fullname != null){
            $UserModel = $UserModel->where('fullname', '=', $fullname);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $UserModel = $UserModel->where('created_at', '>=', $start_date);
        }

        $level_status = trim($request->level_status);

        if($level_status != null){
            $UserModel = $UserModel->where('level_status', '=', $level_status);
        }

        $store_level = trim($request->store_level);

        if($store_level != null){
            $UserModel = $UserModel->where('store_level', '=', $store_level);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $UserModel = $UserModel->where('created_at', '<=', $end_date);
        }

        $userlist = $UserModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        foreach ($userlist as $key => $user) {
            $no_store = 0;
            $store = StoreModel::where('user_id', '=', $user->id)->first();
            $user->store = $store;
            if($store == null){
                $no_store = '1';
            }
            if($store['expire_date'] == null){
                $no_store = '1';
            }
            $user->no_store = $no_store;
            $referrer_user_id = $user->referrer_user_id;
            if($referrer_user_id > 0){
                $referrer_user = UserCache::info($referrer_user_id);
                if($referrer_user != null){
                    $user->referrer_user = $referrer_user->toArray();
                }
            }
            $referrer_user_count = User::where('referrer_user_id', $user->id)->count();
            $user->referrer_user_count = $referrer_user_count;
            $integral = $user->integral()->first();
            if($integral != null){
                $integral = $integral->toArray();
            }
            $user->integral_amount = $integral != null ? $integral['point'] : 0;
            $u_integral_send_total = IntegralSend::where('user_id', '=', $user->id)->sum('integral');
            $user->integral_send_total = $u_integral_send_total;
            $gift_commission = 0;
            $user_commission = $user->commission()->first();
            if(!empty($user_commission)){
                $gift_commission = $user_commission->gift_commission;
            }
            $user->gift_commission = $gift_commission;
        }

        $userlist->appends($request->all());

        $integral_send_total = IntegralSend::sum('integral');

        $pager = $userlist->links();

        $level_status_list = config('user.level_status');

        $store_level_list = config('store.level_text');

        $view = View('admin.customer.index');

        $view->with("integral_send_total", $integral_send_total);

        $view->with("userlist", $userlist);

        $view->with("level_status", $level_status_list);

        $view->with("store_level_list", $store_level_list);

        $view->with("user_type", $user_type);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "用户");

        return $view;

    }

     /**
     * 用户详情
     *
     * @return void
    */
    public function info(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        $no_store = 0;

        $store = StoreModel::where('user_id', '=', $user->id)->first();

        $user->store = $store;

        if($store == null){
            $no_store = '1';
        }
        if($store['expire_date'] == null){
            $no_store = '1';
        }

        $user->no_store = $no_store;

        $referrer_user_id = $user->referrer_user_id;

        if($referrer_user_id != null){
            $referrer_user = User::where('id', $referrer_user_id)->first();
            if($referrer_user != null){
                $user->referrer_user = $referrer_user->toArray();
            }
        }

        $referrer_user_count = User::where('referrer_user_id', $user->id)->count();

        $user->referrer_user_count = $referrer_user_count;

        //vip个数
        $honor_value = User::where('referrer_user_id', $user->id)
        ->where('is_vip', '=', '1')
        ->count();

        $user->honor_value = $honor_value;

        //vip金个数
        $honor_vip_value = User::where('referrer_user_id', $user->id)
        ->where('level_status', '>=', '2')
        ->where('is_vip', '=', '1')
        ->count();

        $user->honor_vip_value = $honor_vip_value;

        $integral = $user->integral()->first();
        if($integral != null){
            $integral = $integral->toArray();
            if($integral['store_sales_points'] > $integral['point']){
                $integral['store_sales_points'] = $integral['point'];
            }
        }

        $user->integral_info = $integral;

        $reward = $user->reward()->first();
        if($reward != null){
            $reward = $reward->toArray();
        }

        $user->reward_info = $reward;

        $level_status_list = config('user.level_status');

        $store_level_list = config('store.level_text');

        $store_status = config('store.status');

        $user_type = config('user.user_type');

        $view = View('admin.customer.info');

        $view->with("user", $user);

        $view->with("level_status", $level_status_list);

        $view->with("store_level_list", $store_level_list);

        $view->with("store_status", $store_status);

        $view->with("user_type", $user_type);

        $view->with("title", "用户信息");

        return $view;

    }

       /**
     * 用户积分明细
     *
     * @return void
    */
    public function integral(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        $pageSize = 50;

        $integral_records = IntegralRecord::where('user_id', $id)->paginate($pageSize);

        $integral_records->appends($request->all());

        $pager = $integral_records->links();

        $view = View('admin.customer.integral');

        $view->with("user", $user);

        $view->with("integral_records", $integral_records);

        $view->with("pager", $pager);

        $view->with("title", "用户积分明细");

        return $view;

    }

      /**
     * 用户余额明细
     *
     * @return void
    */
    public function reward(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        $reward = $user->reward()->first();
        if($reward != null){
            $reward = $reward->toArray();
        }

        $user->reward_info = $reward;

        $pageSize = 50;

        $reward_records =  RewardRecord::where('user_id', $id)->paginate($pageSize);

        $reward_records ->appends($request->all());

        $pager = $reward_records->links();

        $view = View('admin.customer.reward');

        $view->with("user", $user);

        $view->with("reward_records", $reward_records);

        $view->with("pager", $pager);

        $view->with("title", "用户积分明细");

        return $view;

    }



    /**
     * 总监统计
     *
     * @return void
    */
    public function statistics(Request $request)
    {
        $UserModel = User::where('user_type', '!=', 'general');

        $user_type = config('user.user_type');

        $pageSize = 20;

        $form = $request->all();

        $phone = trim($request->phone);

        if($phone != null){
            $UserModel = $UserModel->where('phone', '=', $phone);
        }

        $fullname = trim($request->fullname);

        if($fullname != null){
            $UserModel = $UserModel->where('fullname', '=', $fullname);
        }

        $year = trim($request->year);

        $month = trim($request->month);

        $level_status = trim($request->level_status);

        if($level_status != null){
            $UserModel = $UserModel->where('level_status', '=', $level_status);
        }

        $store_level = trim($request->store_level);

        if($store_level != null){
            $UserModel = $UserModel->where('store_level', '=', $store_level);
        }

        $userlist = $UserModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        foreach ($userlist as $key => $user) {
            $referrer_user_id = $user->referrer_user_id;
            if($referrer_user_id > 0){
                $referrer_user = UserCache::info($referrer_user_id);
                if($referrer_user != null){
                    $user->referrer_user = $referrer_user->toArray();
                }
            }
            $referrer_user_count = User::where('referrer_user_id', $user->id)->count();
            $user->referrer_user_count = $referrer_user_count;
            $integral = $user->integral()->first();
            if($integral != null){
                $integral = $integral->toArray();
            }
            $user->integral_amount = $integral != null ? $integral['point'] : 0;

            $reward = $user->reward()->first();
            if($reward != null){
                $reward = $reward->toArray();
            }
            $user->reward_amount = $reward != null ? $reward['amount'] : 0;

            $user_statistics = [];

            if($year == null && $month == null){
                $user_statistics = UserStatisticsModel::where('user_id', $user->id)->first();
                if($user_statistics != null){
                    $user_statistics = $user_statistics->toArray();
                }
            } else {
                $user_statistics_date_first = UserStatisticsDateModel::where('user_id', $user->id)->first();
                if($user_statistics_date_first != null){
                    $user_statistics_date = UserStatisticsDateModel::where('user_id', $user->id);
                    if($year != null){
                        $user_statistics_date = $user_statistics_date->where('year', $year);
                    }
                    if($month != null){
                        $user_statistics_date = $user_statistics_date->where('month', $month);
                    }
                    $user_statistics['vip_open_number'] = $user_statistics_date->sum('vip_open_number');
                    $user_statistics['vip_renewal_number'] = $user_statistics_date->sum('vip_renewal_number');
                    $user_statistics['store_number'] = $user_statistics_date->sum('store_number');
                }
            }

            $user->user_statistics = $user_statistics;
        }

        $userlist->appends($request->all());

        $integral_send_total = IntegralSend::sum('integral');

        $pager = $userlist->links();

        $months = [];

        for($m=1; $m<=12; $m++){
            $months[] = $m < 10 ? '0' . $m : $m;
        }

        $level_status_list = config('user.level_status');

        $store_level_list = config('store.level_text');

        $view = View('admin.customer.statistics');

        $view->with("integral_send_total", $integral_send_total);

        $view->with("userlist", $userlist);

        $view->with("level_status", $level_status_list);

        $view->with("store_level_list", $store_level_list);

        $view->with("user_type", $user_type);

        $view->with("months", $months);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "用户");

        return $view;

    }


    public function openvip(Request $request){
        $user_id = $request->user_id;
        if($user_id > 0){
            $user = User::where('id', $user_id)->first();
            if($user == null){
                return redirect(route('admin_customer'));
            }
            if($user['is_vip'] == '1'){
                return redirect(route('admin_customer'));
            }
            UserService::getInstance()->autoOpenVip($user);
            return redirect(route('admin_customer'));
        }
    }

    public function openStore(Request $request){
        $user_id = $request->user_id;
        if($user_id > 0){
            $user = User::where('id', $user_id)->first();
            if($user == null){
                return redirect(route('admin_customer'));
            }
            $store = StoreModel::where('user_id', '=', $user->id)->first();
            if($store != null && $store['expire_date'] != null){
                return false;
            }
            StoreService::autoOpenStore($user);
            return redirect(route('admin_customer'));
        }
    }

    public function userIntegralSend(Request $request){
        $result = ['code' => '2x1'];
        $user_id = $request->user_id;
        if($user_id > 0){
            $user = User::where('id', $user_id)->first();
            if($user == null){
                $result['message'] = '用户不存在!';
                return response()->json($result);
            }
            $admin_user = $this->admin_user;
            $integral = $request->integral;
            if(!is_numeric($integral) || $integral <=0){
                $result['message'] = '金额必须大于0!';
                return response()->json($result);
            }
            if($integral > 10000){
                $result['message'] = '金额必须小于10000!';
                return response()->json($result);
            }
            $content = $request->content;
            $order = \DB::transaction(function() use ($admin_user, $user, $integral, $content) {
                UserService::getInstance()->userIntegralIncome($user, $integral, '');
                $IntegralSendModel = new IntegralSend();
                $IntegralSendModel->admin_id = $admin_user->id;
                $IntegralSendModel->user_id = $user->id;
                $IntegralSendModel->integral = $integral;
                $IntegralSendModel->content = $content;
                $IntegralSendModel->save();
            });
            $result['code'] = '200';
            $result['message'] = '赠送成功！';
        } else {
            $result['message'] = '用户不存在!';
        }
        return response()->json($result);
    }

    public function loadUserIntegral(Request $request){
        $result = ['code' => '2x1'];
        $user_id = $request->user_id;
        if($user_id > 0){
            $user = User::where('id', $user_id)->first();
            if($user == null){
                $result['message'] = '用户不存在!';
                return response()->json($result);
            }
            $integral = $user->integral()->first();
            if($integral != null){
                $integral = $integral->toArray();
            }
            $send_count = IntegralSend::where('user_id', $user_id)->count();
            $send_integral_sum = IntegralSend::where('user_id', $user_id)->sum('integral');
            $result['data']['integral'] = $integral;
            $result['data']['send_integral_count'] = $send_count;
            $result['data']['send_integral_sum'] = $send_integral_sum;
            $result['code'] = '200';
            $result['message'] = '';
        } else {
            $result['message'] = '用户不存在!';
        }
        return response()->json($result);
    }

    public function setUserType(Request $request){
        $result = ['code' => '2x1'];
        $user_id = $request->user_id;
        if($user_id > 0){
            $user = User::where('id', $user_id)->first();
            if($user == null){
                $result['message'] = '用户不存在!';
                return response()->json($result);
            }
            $user_type = $request->user_type;
            if($user_type == ''){
                $result['message'] = '请选择用户类型!';
                return response()->json($result);
            }
            if(!in_array($user_type, ['manager','director'])){
                $result['message'] = '用户类型不对!';
                return response()->json($result);
            }
            $user->user_type = $user_type;
            $r = $user->save();
            if($r){
                if($user_type == 'manager'){
                    \DB::table('user')->join('user_referrer_level', 'user_referrer_level.user_id', '=', 'user.id')
                    ->where('parent_ids', 'like', '%,' . $user_id . ',%')
                    ->update(['manager_id' => $user_id, 'user.updated_at' => date('Y-m-d H:i:s')]);
                }
                else if($user_type == 'director'){
                    \DB::table('user')->join('user_referrer_level', 'user_referrer_level.user_id', '=', 'user.id')
                    ->where('parent_ids', 'like', '%,' . $user_id . ',%')
                    ->update(['director_id' => $user_id, 'user.updated_at' => date('Y-m-d H:i:s')]);
                }
            }
            $result['code'] = '200';
            $result['message'] = '保存成功';
        } else {
            $result['message'] = '用户不存在!';
        }
        return response()->json($result);
    }


    public function addUserSubIntegral(Request $request){
        $result = ['code' => '2x1'];
        $user_id = $request->user_id;
        if($user_id > 0){
            $user = User::where('id', $user_id)->first();
            if($user == null){
                $result['message'] = '用户不存在!';
                return response()->json($result);
            }
            $subIntegral = $request->subIntegral;
            $user->sub_integral_amount += $subIntegral;
            $r = $user->save();
            $content = $request->content;
            UserService::getInstance()->userSubIntegralRecord($user, [
                "type" => '1',
                "amount" => $subIntegral,
                "content" => $content,
                "remarks" => $content,
                'order_recharge_id' => 0
            ]);
            $result['code'] = '200';
            $result['message'] = '保存成功';
        } else {
            $result['message'] = '用户不存在!';
        }
        return response()->json($result);
    }

      /**
     * 用户列表
     *
     * @return void
    */
    public function level(Request $request)
    {
        $ReferrerLevelModel = ReferrerLevelModel::select('user_referrer_level.*', 'user.fullname', 'user.user_name','user.manager_id', 'user.director_id')
        ->join('user', 'user.id', '=', 'user_referrer_level.user_id');

        $pageSize = 20;

        $userlist = $ReferrerLevelModel->paginate($pageSize);

        foreach ($userlist as $key => $value) {
            $parent_id = $value->parent_id;
            $userlist[$key]->parent_user = User::where('id', $parent_id)->first();
        }

        $form = $request->all();

        $userlist->appends($request->all());

        $pager = $userlist->links();

        $view = View('admin.customer.level');

        $view->with("form", $form);

        $view->with("userlist", $userlist);

        $view->with("pager", $pager);

        $view->with("title", "用户关系");

        return $view;

    }
}