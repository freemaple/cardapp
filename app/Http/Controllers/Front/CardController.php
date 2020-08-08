<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Cache\Card as CardCache;
use App\Models\Card\Card as CardModel;
use App\Models\Store\Store as StoreModel;
use App\Cache\User as UserCache;
use App\Libs\Service\CardService;

class CardController extends BaseController
{

    /**
     * 名片主页
     *
     * @return void
    */
    public function view(Request $request, $number)
    {
        $card = CardCache::getCard($number);
        $rid = $request->rid;
        if($card == null){
            //返回视图
            $view = view('card.no_exits');
            $view->with('title', '名片不存在或者已经下架');
            return $view;
        }
        $ad_image = '';
        $user = UserCache::info($card['user_id']);
        $microlinks = $card['card_microlinks'];
        $card_qrcode = CardService::qrcode($card, 280);
        $user_id = $user->id;
        $store = StoreModel::where('user_id', $user_id)->first();
        if($store != null && $store['banner'] != ''){
            $ad_image = \HelperImage::storagePath($store['banner']);
        }
        $date = date('Y-m-d H:i:s');
        $store_enable = 0;
        if(!empty($store) && $store['status'] == '2'){
            $expire_date = $store->expire_date;
            if($expire_date != null && $date <= $expire_date){
                $store_enable = 1;
            }
        }

        if($store_enable){
            $ad_link = \Helper::route('store_view', [$store['id']]);
            $ad_name = $store['name'];
        } else {
            $ad_link = \Helper::route('shop');
            $ad_name = '有赏自营商城';
        }
        if(empty($ad_image)){
            $ad_image = \Helper::asset_url('/media/images/default_store_banner.png');
        }
        $share_data = [
            'title' => $card['name'],
            'content' => isset($card['card_info']['organization']) ? $card['card_info']['organization'] : $user['fullname'],
            'url' => \Helper::route('card_view', [$card['card_number']]),
            'image' => \HelperImage::getavatar($user['avatar'])
        ];
        $view = view('card.index',[
            'title' => $card['name'],
            'description' => $card['name'],
            'user' => $user,
            'card' => $card,
            'microlinks' => $microlinks,
            'theme' => 'theme.card.simple_view',
            'card_qrcode' => $card_qrcode,
            'rid' => $rid,
            'ad_link' => $ad_link,
            'ad_name' => $ad_name,
            'store_enable' => $store_enable,
            'share_data' => $share_data
        ]);
        return $view;
    }

     /**
     * 名片浏览记录
     * @param  Request $request 
     * @return string           
     */
    public function viewed(Request $request){
        $data = $request->all();
        //数据校验
        $validator = \Validator::make($data, [
            'id' => 'required',
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->id;
        $card = CardModel::where('id', '=', $id)->first();
        if($card != null){
            $user_card_view = \Cookie::get('card_view', '');
            if($user_card_view == null){
                $card->view_number = $card->view_number + 1;
                $card->save();
                \Cookie::queue('card_view', 1, 0.5);
            }
        }
    }
    
}