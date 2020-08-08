<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Card\Card as CardModel;
use App\Models\Card\CardInfo as CardInfoModel;
use App\Models\Microlink\Microlink;
use App\Models\Post\Post as PostModel;
use App\Cache\Card as CardCache;
use App\Models\CardAlbum;
USE App\Models\Card\CardMicrolink;
use App\Models\Music\Music as MusicModel;
use App\Models\Theme\Background;

class CardService
{
    /**
     * 保存用户基本信息
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public static function saveInfo($user = null, $request){
        $result = [];
        $user_id = $user->id;
        if($request->save_type == 'add'){
            $card = new CardModel();
            $card->user_id = $user_id;
            $card->card_number = static::generateCardNumber($user_id);
            $u_card = $user->card()->first();
            if($u_card == null){
                $card->is_default = '1';
            }
        } else {
            $id =  $request->id;
            $card = $user->card()->where('id', $id)->first();
            if($card == null){
                $result['code'] = "no_exits";
                $result['message'] = '名片不存在!';
                return $result;
            }
        }
        if(isset($request->syn_card_id) && $request->syn_card_id > 0){
            $card->syn_card_id = $request->syn_card_id;
        }
        $card->name = trim($request->name);
        if(isset($request->background_image)){
            $card->background_image = $request->background_image;
        }
        if(isset($request->music)){
            $card->music = trim($request->music);
        }
        if(isset($request->music_id)){
            $card->music_id = intval($request->music_id) ? $request->music_id : 0;
        }
        
        $card->save();
        $card_info = $card->info()->first();
        if($card_info == null){
            $card_info = new CardInfoModel();
            $card_info->card_id = $card->id;
        }
        if(isset($request->organization)){
            $card_info->organization = trim($request->organization);
        }
        if(isset($request->department)){
            $card_info->department = trim($request->department);
        }
        if(isset($request->position)){
            $card_info->position = trim($request->position);
        }
        if(isset($request->province)){
            $card_info->province = trim($request->province);
        }
        if(isset($request->city)){
            $card_info->city = trim($request->city);
        }
        if(isset($request->district)){
            $card_info->district = trim($request->district);
        }
        if(isset($request->address_street)){
            $card_info->address_street = trim($request->address_street);
        }
        $card_info->save();
        if(isset($request->fullname)){
            $user->fullname = trim($request->fullname);
        }
        if(isset($request->weixin_qr)){
            $weixin_qr = $request->weixin_qr;
            $qr = static::base64Wxupload($weixin_qr, '/weixin');
            if($qr && $qr['status']){
                $user->weixin_qr = $qr['filepath'];
            }
        }

        CardCache::clearCardCache($card['card_number']);
    
        $user->save();
        $data = [];
        $data['link'] = \Helper::route('account_card_custom', [$card['card_number']]);
        $result['data'] = $data;
      	$result['code'] = "Success";
        $result['message'] = '保存成功';
        return $result;
    }

    /**
     * 上传产品图片
     */
    public static function base64Wxupload($base64_image_content, $directory) {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $matches)){
            //图片路径地址    
            $fullpath = 'storage/' . $directory;
            if(!is_dir($fullpath)){
                mkdir($fullpath, 0777, true);
            }
            $type = $matches[2];
            $content_arr = explode($matches[0], $base64_image_content);
            $img = base64_decode($content_arr[1]);
            $filename = md5(date('YmdHis').rand(1000, 999999)). '.jpg';
            $filepath = 'weixin/' . $filename;
            $savepath = storage_path() . '/app/static/' . $filepath;
            //服务器文件存储路径
            if (file_put_contents($savepath, $img)){
                $result['status'] = 1;
                $result['filename'] = $filename;
                $result['filepath'] = $filepath;
                $result['filelink'] = \HelperImage::storagePath($filepath);
                return $result;
            }else{
                $result['status'] = 0;
                $result['message'] = '保存失败';
                return $result;
            }
        } else {
            $result['status'] = 0;
            $result['message'] = '不是有效的图片';
            return $result;
        }
    }

    /**
     * 生成编号
     */
    public static function generateCardNumber($user_id){
        $time_str = date('Ym');
        $md5_str = md5(rand(1, 1000) . $user_id);
        $number = $time_str.'C'.substr($md5_str, 0, 5);
        return $number;
    }

    //获取名片
    public static function getCard($card_number){
        $card = CardModel::where('card_number', $card_number)->first();
        if($card == null){
            return $card;
        }
        $card_album = $card->albums()->orderBy('id', 'desc')->get();
        $card_album = $card_album->toArray();
        $card['card_album'] = $card_album;
        if($card != null){
            $card_info = $card->info()->first();
            $card = $card->toArray();
            if($card_info != null){
                $card_info = $card_info->toArray();
            }
            $card['card_info'] = $card_info;
        }
        $card_music = [];
        if($card['music_id']){
            $card_music = MusicModel::where('id', $card['music_id'])->first();
            if($card_music != null){
                $card_music = $card_music->toArray();
            }
        }
        $card['card_music'] = $card_music;
        return $card;
    }

    public static function getCardInfo($card_number){
        $card = CardModel::where('card_number', $card_number)->first();
        if($card == null){
            return null;
        }

        $card_info = $card->info()->first();
        if($card_info != null){
            $card_info = $card_info->toArray();
        }
        $card['card_info'] = $card_info;

        $syn_card_id = $card['syn_card_id'];
        $syn_microlinks = [];
        if($syn_card_id > 0){
            $syn_card = CardModel::where('id', '=', $syn_card_id)->first();
            if($syn_card != null){
                $syn_microlinks = CardService::getCardMicrolink($syn_card);
                if($syn_microlinks != null){
                    $syn_microlinks = $syn_microlinks->toArray();
                }
                $syn_card_album = $syn_card->albums()->orderBy('id', 'desc')->get();
                $syn_card_album = $syn_card_album->toArray();
            }
            if(!empty($syn_card['background_image'])){
                $card['background_image'] = $syn_card['background_image'];
            }
        }

        $microlinks = static::getCardMicrolink($card);
        if($microlinks != null){
            $microlinks = $microlinks->toArray();
        }
        $card_microlinks = array_merge($syn_microlinks, $microlinks);

        $card['card_microlinks'] = $card_microlinks;


        $card_album = $card->albums()->orderBy('id', 'desc')->get();
        $card_album = $card_album->toArray();
        if(!empty($syn_card_album)){
            $card_album = array_merge($syn_card_album, $card_album);
        }
        $card['card_album'] = $card_album;
        
        $card_music = [];
        if($card['music_id']){
            $card_music = MusicModel::where('id', $card['music_id'])->first();
            if($card_music != null){
                $card_music = $card_music->toArray();
            }
        }
        $card['card_music'] = $card_music;
        $card = $card->toArray();
        return $card;
    }

    //获取名片微链接
    public static function getCardMicrolink($card){
        if($card == null){
            return [];
        }
        $user_id = $card['user_id'];
        $microlinks = Microlink::select('microlink.*', 'icon.svg')
        ->join('card_microlink', 'card_microlink.microlink_id', '=', 'microlink.id')
        ->leftjoin('icon', 'icon.id', '=', 'microlink.icon_id')->where('microlink.user_id', '=', $user_id)
        ->where('card_microlink.card_id', '=', $card['id'])
        ->orderBy('microlink.id', 'desc')
        ->get();
        return $microlinks;
    }

    /**
     * 名片二维码
     *
     * @return \Illuminate\Http\Response
     */
    public static function qrcode($card, $size){
        $card_link = Helper::route('card_view', $card['card_number']);
        $card_qrcode = Helper::qrcode1($card_link, $size);
        return  'data:image/png;base64,' . base64_encode($card_qrcode);
    }

    //创建默认名片
    public static function createDefaultCard($user){
        $card = $user->card()->first();
        if($card == null){
            $user_id = $user->id;
            $card = new CardModel();
            $card->user_id = $user_id;
            $card->name = '我的名片';
            $card->card_number = static::generateCardNumber($user_id);
            $card->is_default = '1';
            $card->enable = '1';
            $card->music_id = config('card.default_music_id');
            $Background = Background::where('type', '0')->where('enable', '1')->first();
            $card->background_image = !empty($Background) ? $Background['image'] : '';
            $card->save();
            $microlinks = config('card.default_microlink');
            foreach ($microlinks as $key => $m) {
                $microlink = new Microlink();
                $microlink->user_id = $user->id;
                $microlink->name = $m['name'];
                $microlink->icon_id = $m['icon_id'];
                $microlink->link = '';
                $microlink->save();
                $CardMicrolink = new CardMicrolink();
                $card_id = $card->id;
                $CardMicrolink->card_id = $card_id;
                $CardMicrolink->user_id = $user->id;
                $CardMicrolink->microlink_id = $microlink->id;
                $CardMicrolink->save(); 
            }
        }
    }

    public static function getSynCardList($pageSize, $request){

        $cards = CardModel::select('card.*', 'card_info.organization')->leftjoin('card_info', 'card_info.card_id', '=', 'card.id')->where('is_allow_sync', '1');

        if(isset($request->organization)){
            $cards = $cards->where('card_info.organization', 'like', '%'. sprintf("%s", $request->organization). '%');
        }
        $cards = $cards->orderBy('id','desc');
        $cards = $cards->paginate($pageSize);

        foreach ($cards as $key => $card) {
            $cards[$key]['qr'] = static::qrcode($card, 280);
        }
        return $cards;
    }

    public static function getDefaultCard($user_id){
        $card = CardModel::where('user_id', $user_id)
        ->orderBy('card.is_default', 'desc')
        ->orderBy('card.created_at', 'desc')->first();
        return $card;
    }
}