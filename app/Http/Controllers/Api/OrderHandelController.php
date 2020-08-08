<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use App\Libs\Service\OrderService;
use App\Libs\Service\UserService;
use EasyWeChat\Foundation\Application;
use App\Models\Order\Order as OrderModel;  
use App\Models\User\Address as AddressModel;
use  App\Libs\Service\ProductDispalyService;
use App\Models\Product\Sku as ProductSkuModel; 
use App\Models\Product\Product as ProductModel;
use App\Libs\Service\CartService;


class OrderHandelController extends BaseController
{

    /**
     * order
     *
     * @return void
    */
    public function refund(Request $request, Application $app)
    {
        $result = ['code' => '']；

        $order_no = $request->order_no;

        if($order_no != null){
            $refundNo = 'Test' .md5(time());
            $response = $app->payment->refund($order_no, $refundNo, 1, 0.5, null, 'out_trade_no', 'REFUND_SOURCE_RECHARGE_FUNDS', '退款');
            dd($response);
        }
    }
}