<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User\User as UserModel;
use App\Models\User\Gold as UserGoldModel;
use App\Models\Gold\Config as GoldConfigModel;
use App\Models\Gold\GoldDayConfig as GoldDayConfigModel;
use App\Models\User\GoldDay as UserGoldDayModel;

class GoldController extends BaseController
{

     /**
     * 后台系统首页
     *
     * @return void
     */
    public function index(Request $request)
    {
        $UserGoldModel = UserGoldModel::select('user_gold.*', 'user.fullname', 'user.avatar', 'user.phone')->join('user', 'user.id', '=', 'user_gold.user_id');


        $phone = trim($request->phone);

        if($phone != null){
            $UserGoldModel = $UserGoldModel->where('phone', '=', $phone);
        }

        $fullname = trim($request->fullname);

        if($fullname != null){
            $UserGoldModel = $UserGoldModel->where('fullname', '=', $fullname);
        }

        $pageSize = 20;

        $form = $request->all();

        $userGolds = $UserGoldModel->orderBy('created_at', 'desc')->paginate($pageSize);

        $goldConfig = GoldConfigModel::first();

        $gift_unit = $goldConfig['gift_unit'];

        foreach($userGolds as $ukey => $userGold){
            $gold_total = $userGold->gold_number * $gift_unit;
            $userGold->gold_total = $gold_total;
            $total_amount = $userGold['bonus_amount'] + $gold_total;
            $userGold->total_amount = $total_amount;
            $gift_commission = 0;
            $user = UserModel::where('id', $userGold->user_id)->first();
            if(!empty($user)){
                $user_commission = $user->commission()->first();
                if(!empty($user_commission)){
                    $gift_commission = $user_commission->gift_commission;
                }
            }
            $userGold->gift_commission = $gift_commission;
        }

        $userGolds->appends($request->all());

        $pager = $userGolds->links();

        $user_gold_numbers = UserGoldModel::sum('gold_number');


        $view = View('admin.gold.index');

        $view->with("userGolds", $userGolds);

        $view->with("user_gold_numbers", $user_gold_numbers);

        $view->with("gift_unit", $gift_unit);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "金麦");

        return $view;
    }

    /**
     * 后台系统首页
     *
     * @return void
     */
    public function day(Request $request)
    {
        $date = $request->date;

        $fullname = $request->fullname;

        $goldDayConfig = GoldDayConfigModel::where('date', $date)->first();

        $pageSize = 20;

        $form = $request->all();

        $userGoldDays = UserGoldDayModel::select('user_gold_day.*', 'user.fullname', 'user.avatar', 'user.phone')
        ->join('user', 'user.id', '=', 'user_gold_day.user_id');

        if(!empty($date)){
            $userGoldDays = $userGoldDays->where('date', $date);
        }

        if(!empty($fullname)){
            $userGoldDays = $userGoldDays->where('user.fullname', $fullname);
        }
    
        $userGoldDays = $userGoldDays->orderBy('created_at', 'desc')->paginate($pageSize);

        $userGoldDays->appends($request->all());

        $pager = $userGoldDays->links();

        $view = View('admin.gold.day');

        $view->with("goldDayConfig", $goldDayConfig);

        $view->with("userGoldDays", $userGoldDays);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "金麦");

        return $view;
    }

}