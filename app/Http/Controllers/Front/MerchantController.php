<?php

namespace App\Http\Controllers\Front;

use App\Libs\Service\PostService;
use Illuminate\Http\Request;
use Helper;
use App\Models\User\User as UserModel;

use App\Models\Store\Store as StoreModel;
use App\Cache\Position as PositionCache;

class MerchantController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $form = $request->all();

        $provices = PositionCache::provices();

        $view = View('merchant.index');

        $view->with([
            'title' => "同城附近商家",
            'provices' => $provices,
            'form' => $form
        ]);

        return $view;
    }
}