<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Auth;
use App\Models\Theme\Background;


class BackgroundController extends BaseController
{

    /**
     * 编辑
     * @param  Request $request 
     * @return string           
     */
    public function upload(Request $request){
        $image = $request->image;
        $Background = new Background();
        $Background->image = $image;
        $Background->save();
        $result['code'] = "Success";
        $result['message'] = '保存成功';
        return json_encode($result);
    }

}
