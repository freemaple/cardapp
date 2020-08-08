<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use Auth;
use EasyWeChat\Foundation\Application;

class CommonController extends BaseController
{
	
     /**
     * 钱包
     *
     * @return void
    */
    public function wxshare(Application $app, Request $request)
    {
        $environment = \App::environment();
        if($environment == 'local'){
            $result['status'] = '1';
            $result['data'] = [];
            return $result;
        }
        
        $url = $request->url;

        $app->js->setUrl($url);

        $config = $app->js->config(array("updateAppMessageShareData", "updateTimelineShareData", "onMenuShareQZone", "showOptionMenu", 'onMenuShareAppMessage', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ'), false);

        $result = [];

        $result['code'] = 'Success';

        $result['data'] = json_decode($config);

        return json_encode($result);

    }
}
