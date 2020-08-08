<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Models\User\User as UserModel;
use Auth;
use Helper;

class RewardController extends BaseController
{

    /**
     * 钱包
     *
     * @return void
    */
    public function reward(Request $request)
    {
        $user = Auth::user();
        $reward = $user->reward()->first();
        $view = View('account.reward.index', [
            'title' => '我的余额',
            'user' => $user,
            'reward' => $reward
        ]);
        return $view;
    }
}
