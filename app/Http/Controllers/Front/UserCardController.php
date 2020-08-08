<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use Auth;
use Helper;
use App\Models\Theme\Background;
use App\Libs\Service\CardService;
use App\Models\Card\Card as CardModel;
use App\Models\Music\Music as MusicModel;
use App\Cache\Music as MusicdCache;
use App\Cache\Theme as ThemeCache;

class UserCardController extends BaseController
{

    /**
     * 名片主页页面
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $cards = $user->card()->orderBy('id','desc')->get();

        $cards = $cards->toArray();

        foreach ($cards as $key => $card) {
            if(!empty($card['syn_card_id'])){
                $syn_card = CardModel::where('id', $card['syn_card_id'])->first();
                if($syn_card != null){
                    $syn_card = $syn_card->toArray();
                }
                $cards[$key]['syn_card'] = $syn_card;
            }
            $cards[$key]['qr'] = $this->qrcode($card, 100);
        }

        $sys_cards = CardService::getSynCardList(100, null);

        $view = view('account.card.index',[
            'user' => $user,
            'title' => '名片列表',
            'description' => '',
            'keywords' => '',
            'cards' => $cards,
            'sys_cards' => $sys_cards
        ]);
        return $view;
    }

    /**
     * 名片二维码
     *
     * @return \Illuminate\Http\Response
     */
    public function qrcode($card){
        $user = Auth::user();
        $card_link = Helper::route('card_view', [$card['card_number']]);
        $card_qrcode = Helper::qrcode1($card_link, 150);
        return  'data:image/png;base64,' . base64_encode($card_qrcode);
    }

    /**
     * 添加名片
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $user = Auth::user();
        $card_count = $user->card()->count();
        $card_can_add = $user->card_can_add;
        if($card_count >= 3 && $card_can_add <=0){
            $view = view('account.card.no_add',[
                'user' => $user,
                'title' => '无法添加'
            ]);
            return $view;
        }
        $backgrounds = Background::where('type', '0')->where('enable', '1')->get();
        if($backgrounds != null){
            $backgrounds = $backgrounds->toArray();
        }
        $musics = MusicdCache::getMusic();
        $card_music = [];
        $view = view('account.card.save',[
            'user' => $user,
            'title' => '添加名片',
            'description' => '',
            'keywords' => '',
            'save_type' => 'add',
            'backgrounds' => $backgrounds,
            'musics' => $musics,
            'card_music' => $card_music
        ]);
        return $view;
    }

    /**
     * 编辑名片
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $number)
    {
        $user = Auth::user();
        $card = $user->card()->where('card_number', $number)->first();
        if($card == null){
            return redirect(\Helper::route('account_card_index'));
        }
        $card_info = [];
        $card_info = $card->info()->first();
        if($card_info != null){
            $card_info = $card_info->toArray();
        }
        $card = $card->toArray();
        $backgrounds = Background::where('type', '0')->where('enable', '1')->get();
        if($backgrounds != null){
            $backgrounds = $backgrounds->toArray();
        }
        $musics = MusicdCache::getMusic();
        $card_music = [];
        if($card['music_id']){
            $card_music = MusicModel::where('id', $card['music_id'])->first();
        }
        $view = view('account.card.save',[
            'user' => $user,
            'title' => '编辑名片',
            'description' => '',
            'keywords' => '',
            'card' => $card,
            'card_info' => $card_info,
            'save_type' => 'edit',
            'backgrounds' => $backgrounds,
            'musics' => $musics,
            'card_music' => $card_music
        ]);
        return $view;
    }

    /**
     * 名片主页自定义
     *
     * @return \Illuminate\Http\Response
     */
    public function custom(Request $request, $number)
    {
        $user = Auth::user();
        $card = $user->card()->where('card_number', $number)->first();
        if($card == null){
             return redirect(\Helper::route('account_card_index'));
        }
        $card = CardService::getCardInfo($number);
        $backgrounds = Background::where('type', '0')->where('enable', '1')->get();
        if($backgrounds != null){
            $backgrounds = $backgrounds->toArray();
        }
        $card_qrcode = CardService::qrcode($card, 280);
        $view = view('account.card.custom',[
            'user' => $user,
            'title' => '个性化',
            'description' => '',
            'keywords' => '',
            'card' => $card,
            'backgrounds' => $backgrounds,
            'theme' => 'theme.card.simple',
            'card_qrcode' => $card_qrcode
        ]);
        return $view;
    }

     /**
     * 名片屏保保存
     *
     * @return \Illuminate\Http\Response
     */
    public function screen(Request $request)
    {
        $user = Auth::user();
        $backgrounds = ThemeCache::backgrounds();
        $card = $user->card()->where('is_default', '1')->where('enable', '1')->first();
        $card_qrcode = CardService::qrcode($card, 80);
        $view = view('account.card.screen',[
            'user' => $user,
            'title' => '选择屏保背景图片',
            'description' => '',
            'keywords' => '',
            'backgrounds' => $backgrounds,
            'card_qrcode' => $card_qrcode
        ]);
        return $view;
    }

     /**
     * 名片相册
     *
     * @return \Illuminate\Http\Response
     */
    public function album(Request $request, $id)
    {
        $user = Auth::user();
        $card = $user->card()->where('id', $id)->first();
        if($card == null){
             return redirect(\Helper::route('account_card_index'));
        }

        $card_albums = $card->albums()->orderBy('id', 'desc')->get();

        if($card_albums != null && count($card_albums)){
            $card_albums = $card_albums->toArray();
        }
        $view = view('account.card.album',[
            'user' => $user,
            'title' => '名片相册',
            'card' => $card,
            'card_albums' => $card_albums
        ]);
        return $view;
    }
}
