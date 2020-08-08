<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use Auth;
use Helper;

use App\Models\Icon\Icon;

use App\Libs\Service\CardService;

class MicrolinkController extends BaseController
{

    /**
     * 帐号设置页面
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $card_id = $request->cid;
        $card = $user->card()->where('card.id', '=', $card_id)->first();
        if($card == null){
            return null;
        }
        $microlinks = CardService::getCardMicrolink($card);
        $icons = Icon::where('enable', '1')->get();
        $view = view('account.microlink.index',[
            'user' => $user,
            'title' => '微链接',
            'description' => '',
            'keywords' => '',
            'icons' => $icons,
            'card' => $card,
            'microlinks' => $microlinks
        ]);
        return $view;
    }
}
