<?php
namespace App\Libs\Weixin;

use App\Helper\Curl;

class WxRequest
{

    private static function wxConfig(){
        $environment = \App::environment();
        $config = config("wx.".$environment);
        return $config;
    }

    /**
    * 
    * @param api $url
    * @param  $method
    * @param unknown $param_more
    */
    protected static function access($url = '', $param_more = array()){
        $config = static::wxConfig();
        $base_params = array(
            'appid' => $config['appid'], 
            'secret' => $config['secret']
        );
        $params = array_merge($param_more, $base_params);
        if (!empty($params)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= (is_string($params)) ? $params : http_build_query($params, '', '&');
        }
        return $url;
    }

    /**
    * 
    * @param api $url
    * @param  $method
    * @param unknown $param_more
    */
    protected static function curlPost($url = '',  $param_more = array()){
        $config = static::wxConfig();
        $base_params = array(
            'appid' => $config['appid'], 
            'secret' => $config['secret']
        );
        $post_data = array_merge($param_more, $base_params);
        $result = Curl::post($url, $post_data);
        return $result;
    }

    public static function jscode2session($code){

        $url = 'https://api.weixin.qq.com/sns/jscode2session';

        $data = array(
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        );

        $rs = static::curlPost($url, $data);

        return $rs;
    }
}