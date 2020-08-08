<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;

use App\Models\Card\Card as CardModel;
use App\Models\User\User as UserModel;

class CardController extends BaseController
{

    /**
     * 名片列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $CardModel = CardModel::select('card.*', 'user.fullname', 'user.phone', 'user.level_status', 'user.nickname')
        ->join('user', 'card.user_id', '=', 'user.id');

        $pageSize = 20;

        $form = $request->all();

        $name = trim($request->name);

        if($name != null){
            $CardModel = $CardModel->where('name', '=', $name);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $CardModel = $CardModel->where('created_at', '>=', $start_date);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $CardModel = $CardModel->where('created_at', '<=', $end_date);
        }

        $user_id = trim($request->user_id);

        if($user_id != null){
            $CardModel = $CardModel->where('card.user_id', '=', $user_id);
        }

        $cards = $CardModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        $cards->appends($request->all());

        $pager = $cards->links();

        $level_status = config('user.level_status');

        $view = View('admin.card.index');

        $view->with("cards", $cards);

        $view->with("level_status", $level_status);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "名片列表");

        return $view;
    }

     /**
     * 是否可同步
     *
     * @return void
    */
    public function setSyn(Request $request)
    {
        $result = [];

        $card_id = $request->card_id;

        $card = CardModel::where('id', '=', $card_id)->first();

        if(empty($card)){
            $result['code'] = '2x1';
            $result['message'] = '文章不存在';
            return response()->json($result);
        }
        if($card['is_allow_sync'] == '1'){
            $card->is_allow_sync = '0';
        } else {
            $card->is_allow_sync = '1';
        }
        $card->save();
        $result['code'] = '200';
        $result['message'] = '操作成功！';
        return response()->json($result);
    }
}