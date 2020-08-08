<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Models\User\User as UserModel;
use App\Models\Payout\Apply as PayoutApplyModel;
use Auth;
use Helper;

class PayoutController extends BaseController
{

    /**
     * 提现申请
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $payout_status = config('payout.status_text');
        $pageSize = config('paginate.payout', 100);
        $payoutApplys = PayoutApplyModel::where('user_id', '=', $user->id)->orderBy('id', 'desc')->paginate($pageSize);
        $view = view('account.payout.index',[
            'user' => $user,
            'title' => '提现记录',
            'description' => '',
            'keywords' => '',
            'payoutApplys' => $payoutApplys,
            'payout_status' => $payout_status
        ]);
        return $view;
    }

    /**
     * 提现申请
     *
     * @return \Illuminate\Http\Response
     */
    public function apply()
    {
        $user = Auth::user();
        $banks = $user->banks()->get();
        $view = view('account.payout.apply',[
            'user' => $user,
            'title' => '提现申请',
            'description' => '',
            'keywords' => '',
            'banks' => $banks
        ]);
        return $view;
    }
}
