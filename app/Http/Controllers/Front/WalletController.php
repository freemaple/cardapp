<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Models\User\User as UserModel;
use Auth;
use Helper;

class WalletController extends BaseController
{

    /**
     * 钱包
     *
     * @return void
    */
    public function wallet(Request $request)
    {
        $form = $request->all();

        $user = Auth::user();

        $wallet = $user->wallet()->first();

        $view = View('account.wallet.index', [
            'title' => '我的钱包',
            'form' => $form,
            'user' => $user,
            'wallet' => $wallet
        ]);

        return $view;

    }

    /**
     * 钱包记录
     *
     * @return void
    */
    public function walletRecord(Request $request)
    {
        $form = $request->all();

        $user = Auth::user();

        $pageSize = config('paginate.wallet.record', 10);

        $walletRecord = $user->walletRecord()->paginate($pageSize);

        $walletRecord = $walletRecord->toArray();

        $view = View('account.wallet.record', [
            'title' => '我的钱包',
            'form' => $form,
            'user' => $user,
            'walletRecord' => $walletRecord
        ]);

        return $view;

    }

}
