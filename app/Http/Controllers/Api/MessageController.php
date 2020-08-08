<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use Auth;
use App\Models\Message\Message as MessageModel;
use App\Libs\Service\MessageService;
use App\Models\Store\Store as StoreModel;
use App\Models\Order\Order as OrderModel; 

class MessageController extends BaseController
{

    /**
     * 消息记录
     *
     * @return void
    */
    public function messageList(Request $request)
    {
        $user = Auth::user();

        $user = Auth::user();

        $user_id = $user->id;

        $pageSize = config('paginate.message', 100);

        //消息列表
        $message_list = MessageService::getUserMessage($user_id, $pageSize);

        $result = ['code' => 'Success'];

        if($request->type == 'app'){

        } else {
            $view = view('account.message.block.list')->with('message_list', $message_list)->render();
            unset($message_list['data']);
            $result['view'] = $view;
        }

        $message_list = $message_list->toArray();

        $result['data']['messages'] = $message_list;

        return response()->json($result);

    }

    /**
     * 标记已读
     *
     * @return void
    */
    public function setMessageAllRead(Request $request)
    {
        $form = $request->all();
        $user = Auth::user();
        MessageModel::where('user_id', $user->id)->where('is_read', '=', '0')->update([
            'is_read' => '1'
        ]);
        $result['code'] = 'Success';
        $result['message'] = '';
        return response()->json($result);
    }

    /**
     * 获取未读数量
     *
     * @return void
    */
    public function noReadNumber(Request $request)
    {
        $form = $request->all();
        $user = Auth::user();
        if(empty($user)){
            $result['code'] = '2x1';
            return response()->json($result);
        }
        $count = MessageModel::where('user_id', $user->id)->where('is_read', '=', '0')->count();

        $store = StoreModel::where('user_id', '=', $user->id)->first();
        $store_shipping_order_count = OrderModel::where('seller_id', '=', $user->id)->where('order_status_code', '=', 'shipping')->count();
        $result['code'] = 'Success';
        $result['data'] = ['count' => $count, 'store_shipping_order_count' => $store_shipping_order_count];
        $result['message'] = '';
        return response()->json($result);
    }
}
