<?php
namespace App\Libs\Service;

use Hash;
use Auth;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\User\VipPackage as VipPackageModel;
use App\Models\Order\Recharge as OrderRecharge;  
use App\Models\Order\RechargeWxRecord as RechargeWxRecordModel;
use EasyWeChat;
use EasyWeChat\Payment\Order;

class WXService
{
    /**
     * 
     * @param  $app
     * @param  array  $request 
     * @return array
     */
    public static function registerMessage($user, $app){
        $notice = $app->notice;
        $template_id = '7YQyKvgn47pOMHlrPQpqbyg-7eRMacw3cI-u4u1KUek';
        $data =  [
            'fullname' => $user['fullname'],
            'phone' => '(' . $user['phone'] . ')'
        ];
        $notice->uses($template_id)->withUrl('https://easywechat.org')->andData($data)->andReceiver($user->openid)->send();
    }
}