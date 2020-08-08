<?php

namespace App\Helper;

class Curl
{

    /**
    * 
    * @param api $url
    * @param  $method
    * @param unknown $param_more
    */
    public static function access($url = '', $params = array()){
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
    public static function post($url = '',  $param = array()){
        $post_data = http_build_query($param);
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 我们在POST数据哦！
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        return json_decode($output, true);
    }


    /**
    * 
    * @param api $url
    * @param  $method
    * @param unknown $param_more
    */
    public static function get($url = '',  $param = array()){
        $url = $this->accessUrl($url, $param);
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        return json_decode($output, true);
    }
}