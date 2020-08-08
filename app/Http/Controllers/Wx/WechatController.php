<?php

namespace App\Http\Controllers\Wx;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use EasyWeChat\Foundation\Application;
use Log;
use App\Models\User\User as UserModel;
use App\Helper\Base as Helper;


class WechatController extends Controller
{

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve(Application $wechat)
    {
        $wechat->server->setMessageHandler(function($message){
            return "欢迎关注";
        });

        return $wechat->server->serve();
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function buttons(Application $wechat)
    {
        $menu = $wechat->menu;

        $buttons = [
            [
                "type" => "click",
                "name" => "今日歌曲",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://www.soso.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];
        $menu->add($buttons);
    }

     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function pay(Application $wechat)
    {
        echo 'test';
    }

     /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function token(Application $wechat, Request $request)
    {
       //获得参数 signature nonce token timestamp echostr
        $nonce     = $request->nonce;
        $token     = 'RENREN';
        $timestamp = $request->timestamp;
        $echostr   = $request->echostr;
        $signature = $request->signature;
        //形成数组，然后按字典序排序
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str == $signature && $echostr ){
            //第一次接入weixin api接口的时候
            echo  $echostr;
            exit;
        }
    }
}