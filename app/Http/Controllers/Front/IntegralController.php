<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Models\User\User as UserModel;
use Auth;
use Helper;

class IntegralController extends BaseController
{

    /**
     * 积分明细
     *
     * @return void
    */
    public function integral(Request $request)
    {
        $user = Auth::user();
        $integral = $user->integral()->first();
        if($integral != null){
            if($integral->store_sales_points > $integral->point){
                $integral->store_sales_points = $integral->point;
                $integral->save();
            }
        }
        $max_sales_points = config('user.integral.max_sales_points');
        $can_toreward = 0;
        if($integral != null && $integral->store_sales_points >=  $max_sales_points){
            $can_toreward = 1;
        }
        $view = View('account.integral.index', [
            'title' => '我的积分明细',
            'user' => $user,
            'integral' => $integral,
            'can_toreward' => $can_toreward,
            'max_sales_points' => $max_sales_points
        ]);
        return $view;
    }

    /**
     * 积分转账
     *
     * @return void
    */
    public function transfer(Request $request)
    {
        $user = Auth::user();
        $integral = $user->integral()->first();
        if($user->is_vip != '1'){
            return redirect(\Helper::route('account_index'))->with('message', '开通vip可以互相转账自由交易积分！');
        }
        if($integral == null || $integral['point'] <=0){
            return redirect(\Helper::route('account_index'))->with('message', '对不起，您没有任何积分可转账!');
        }
        $uid = $request->u_id;
        $payer = UserModel::where('id', '=', $uid)->first();
       
        $view = View('account.integral.transfer', [
            'title' => '有赏积分转账',
            'user' => $user,
            'integral' => $integral,
            'payer' => $payer
        ]);
        return $view;
    }
}
