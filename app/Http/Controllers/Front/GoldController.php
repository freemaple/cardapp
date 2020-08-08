<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Libs\Service\UserService;
use App\Models\User\User as UserModel;
use Auth;
use Session;
use Helper;
use App\Models\Gift\Gift as GiftModel;


class GoldController extends BaseController
{

    /**
     * 金麦
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $data = UserService::getInstance()->getGoldData();

        $data['user'] = $user;

        $data['title'] = '我的金麦穗';
        
        $view = view('account.gold.index',$data);

        return $view;
    }
}
