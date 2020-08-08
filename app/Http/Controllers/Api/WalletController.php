<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\UserService;
use Auth;

class WalletController extends BaseController
{
	
     /**
     * 钱包
     *
     * @return void
    */
    public function record(Request $request)
    {
        $form = $request->all();

        $user = Auth::user();

        $pageSize = config('paginate.wallet.record', 10);

        $walletRecord = $user->walletRecord()->paginate($pageSize);

        $pager = '';

        if(!empty($walletRecord)){
            $walletRecord->appends($request->all());
            $pager = $walletRecord->links();
        }

        $walletRecord = $walletRecord->toArray();

        $view = view('account.wallet.block.list')->with('walletRecords', $walletRecord['data']);

        unset($walletRecord['data']);

        $result = [];

        $result['code'] = 'Success';

        $result['data'] = $walletRecord;

        $result['view'] = $view->render();

        return json_encode($result);

    }
}
