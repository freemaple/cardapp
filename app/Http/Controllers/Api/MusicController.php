<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\CardService;
use Auth;
use App\Models\Music\Music as MusicdModel;

class MusicController extends BaseController
{

    /**
     * 添加名片相册
     * @param Request $request [description]
     */
    public function getMusic(Request $request){

        $result = ['code' => ''];

        $card_id = $request->card_id;

        $musics = MusicdModel::where('enable', '=', '1')->get();
        if($music == null){
            $result['message'] = '';
            return response()->json($result);
        }
        
        //当前密码
        $result = [];
        $result['code'] = 'Success';
        $result['data'] = $musics;
        $result['message'] = '';
        return response()->json($result);
    }
}
