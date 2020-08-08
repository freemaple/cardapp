<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AppController extends Controller
{

    /**
     * 首页产品
     * @param  Request $request 
     * @return string           
     */
    public function update(Request $request){

        $appid = $request->appid;  

        $version =  $request->version;//客户端版本号  

        //默认返回值，不需要升级 
        $result = ['message' => '', 'code' => '2x1']; 

        $app_version = config('wap.version');

        if (isset($appid) && isset($version)) {  
            //这里是示例代码，真实业务上，最新版本号及relase notes可以存储在数据库或文件中  
            if($version !== $app_version){  
                $result = ['code' => '200'];
                $result['data'] = [
                    'version' => $version,
                    'must_update' => 1,
                    'force_update' => 0,
                    'status' => 1
                ];
                $result['data']['title'] = "应用更新";  
                $result['data']['content'] = "应用更新";  
                //应用升级包下载地址  
                $result['data']['url'] = "https://www.renrenyoushang.com/app/renrenyoushang.apk";
            }   
        }
        return response()->json($result);
    }
}
