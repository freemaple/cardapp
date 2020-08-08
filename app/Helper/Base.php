<?php

namespace App\Helper;

use Jenssegers\Agent\Facades\Agent;

class Base
{   
    /**
     * URL地址输出
     * @param string $name  route 别名
     * @param array $parameters
     * @param bollean $secure
     * @return string
     */
	public static function route($name, $parameters = [], $secure = false)
    {
    	$rid = !empty($parameters['rid']) ? $parameters['rid'] : '';
    	$user = \Auth::user();
    	if($user != null && $user['is_vip'] == '1'){
    		$rid = $user->id;
    	} else {
    		if($rid == null){
	            $rid = \Cookie::get('rid', '');
	        }
    	}
    	
        if(!is_array($parameters)){
        	$n_parameters = [];
        	$n_parameters[] = $parameters;
        	$parameters = $n_parameters;
        }
       
        //加密
        if(is_numeric($rid)){
        	$rid = static::passport_encrypt($rid, 'rid');
        }
        if($rid != null){
        	$parameters['rid'] = $rid;
        }
        if($name == 'product_view'){
        	if(!empty($user) && $user['is_vip'] == '1' && static::isWeixin() && empty($parameters['sid'])){
        		$sid = $user->u_id;
        		$parameters['sid'] = $sid;
        	}
        }
    	if($secure == true){
    		$url = secure_url(route($name, $parameters, false));
    	}else{
    		$url = route($name, $parameters, true);
    	}
    	return $url;
    }

    /**
     * URL地址输出
     * @param string $name  route 别名
     * @param array $parameters
     * @param bollean $secure
     * @return string
     */
	public static function currentUrl($name, $parameters = [], $other_parameters = [], $secure = null)
    {
    	foreach ($other_parameters as $key => $value) {
    		if($value != ''){
    			$parameters[$key] = $value;
    		}
    	}
    	if($secure == true){
    		$url = secure_url(route($name, $parameters, false));
    	}else{
    		$url = route($name, $parameters, true);
    	}
    	return $url;
    }
    /**
      * 危险字符转换
      * @param  $string
      * @return string
      */
    public static function inputStr($string) {
        $string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','',$string);
        $string = str_replace(array("\0","%00","\r"),'',$string);
        $string = str_replace(array("%3C",'<'),'<',$string);
        $string = str_replace(array("%3E",'>'),'>',$string);
        $string = str_replace(array('"',"'","\t",' ',"\\"),array('"',"'",' ',' '),$string);
        $string=trim($string);
        return htmlspecialchars($string, ENT_QUOTES);
    }
	
	/**
	 * 二维数组根据某个字段排序
	 * 
	 * @param  $array $arr 数组
	 * @param  $string $field 排序字段
	 * @param  $string $sort 排序顺序 SORT_ASC升序,SORT_DESC降序
     * @return array
	 */
	public static function arr2Sort($arr, $field, $sort)
	{
		$arrSort = array();
		foreach($arr AS $uniqid => $row){
		    foreach($row AS $key=>$value){
		        $arrSort[$key][$uniqid] = $value;
		    }
		}
		array_multisort($arrSort[$field], constant($sort), $arr);
		return $arr;
	}
	/**
	 * 获取IP地址
	 */
	public static function getIPAddress(){
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
			if (isset($_SERVER["HTTP_CLIENT_IP"])){
				$proxy = $_SERVER["HTTP_CLIENT_IP"];
			}
			else{
				$proxy = $_SERVER["REMOTE_ADDR"];
			}
	
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif(isset($_SERVER['HTTP_REAL_IP'])){
			$ip = $_SERVER['HTTP_REAL_IP'];
		}
		else
		{
			if (isset($_SERVER["HTTP_CLIENT_IP"])){
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			}
			else{
				$ip = $_SERVER["REMOTE_ADDR"];
			}
		}
		return $ip;
	}
	/**
	 * 验证是否是手机移动端访问
	 * @return string 设备名称 ：操作系统
	 */
	public static function device()
	{
		$deviceType = (Agent::isMobile() ? (Agent::isTablet() ? 'Tablet' : 'Phone') : 'PC');
		return $deviceType .':'.Agent::platform();
	}


	/**
	 * 验证是否是手机移动端访问
	 * @return string 设备名称 ：操作系统
	 */
	public static function isMobile()
	{
		return (Agent::isMobile() || Agent::isTablet()) ? true : false;
	}

	public static function isApp(){
		$server = \Request::server();
		$HTTP_USER_AGENT = $server['HTTP_USER_AGENT'];
        if(strpos($HTTP_USER_AGENT, 'Html5Plus') !== false){
        	return true;
        }
        return false;
	}

	public static function isIphone(){
		$server = \Request::server();
		$HTTP_USER_AGENT = $server['HTTP_USER_AGENT'];
        if(strpos($HTTP_USER_AGENT, 'iPhone') !== false || strpos($HTTP_USER_AGENT, 'iPad') !== false){
        	return true;
        }
        return false;
	}

	public static function isSafari(){
		$server = \Request::server();
		$HTTP_USER_AGENT = $server['HTTP_USER_AGENT'];
        if(strpos($HTTP_USER_AGENT, 'Safari') !== false && strpos($HTTP_USER_AGENT, 'Chrome') == false){
        	return true;
        }
        return false;
	}

	/**
	 * 邮箱等敏感信息中间字符串用*隐藏 
	 * @param  string  $str
	 * @return string
	 */
	public static function hideNameStar($str) { 
		mb_internal_encoding('utf-8');
		if(strlen($str) == 6){
			$str =  "*" . mb_substr($str, -1);
			return $str;
		}
	    if (strpos($str, '@')) { 
	        $email_array = explode("@", $str); 
	        //邮箱前缀
	        $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3);  
	        $count = 0; 
	        $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count); 
	        $rs = $prevfix . $str; 
	    } else { 
	        $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i'; 
	        if (preg_match($pattern, $str)) { 
	            $rs = preg_replace($pattern, '$1****$2', $str);
	        } else { 
	        	if(strlen($str) > 10){
	        		$rs = mb_substr($str, 0, 2) . "*" . mb_substr($str, -1); 
	        	} else {
	        		$rs = $str;
	        	}
	            
	        } 
	    } 
	    return $rs; 
	}
	
	/**
	 * 邮箱等敏感信息中间字符串用*隐藏 
	 * @param  string  $str
	 * @return string
	 */
	public static function hideStar($str) { 
	    if (strpos($str, '@')) { 
	        $email_array = explode("@", $str); 
	        //邮箱前缀
	        $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3);  
	        $count = 0; 
	        $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count); 
	        $rs = $prevfix . $str; 
	    } else { 
	        $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i'; 
	        if (preg_match($pattern, $str)) { 
	            $rs = preg_replace($pattern, '$1****$2', $str);
	        } else { 
	            $rs = substr($str, 0, 3) . "***" . substr($str, -1); 
	        } 
	    } 
	    return $rs; 
	}
	/**
	 * url处理，替换特殊字符
	 * @param  string $url
	 * @return string
	 */
	public static function urlProcess($url) {
		return str_replace('---', '-', str_replace(array(' ',';', '/', '&', '"', "'", '#',',','?','!','.','+','*','%',','), '-', strtolower(trim($url))));
	}

	public static function weekText($week){
		$arr = ['1' => '一', '2' => '二', '3' => '三', '4' => '四', '5' => '五', '6' => '六', '7' => '日'];
		return isset($arr[$week]) ? $arr[$week] : '';
	}

	/**
	 * 生成二维码
	 */
	public static function qrcode($link, $size){
		$qrcode = \QrCode::size($size)->generate($link);
		return $qrcode;
	}

	/**
	 * 生成二维码
	 */
	public static function qrcode1($link, $size, $color = null){
		$qrcode = \QrCode::format('png');
		if($color != null){
			$qrcode = $qrcode->color($color[0], $color[1], $color[2]);
		} else {
			$qrcode = $qrcode->color(0, 0, 220);
		}
		$qrcode = $qrcode->size($size)->margin(0.1)->generate($link);
		return $qrcode;
	}

	public static function asset_url($path, $version = null){
		//获取站点配置
        $site_config = config('site');
        $base_path = '';
        if($version === null){
            //版本号
            $version = isset($site_config['version']) ? $site_config['version'] : '';
        }
		return asset(sprintf('%s%s?%s', $base_path, $path, $version));
	}

	public static function diffBetweenTwoDays($day1, $day2){
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        $day = intval(($second2 - $second1) / 86400);
        return $day < 0 ? 0 : $day;
    }

    public static function getthemonth($date){   
        $firstday = date('Y-m-01 00:00:00', strtotime($date));
        $lastday = date('Y-m-d 23:59:59', strtotime("$firstday +1 month -1 day"));
        return array($firstday,$lastday);
    }

    public static function isWeixin(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
            return true; 
        } else {
            return false; 
        }
    }

    //生成storage目录文件路径
	public static function storagePath($image_path){
		if (strpos($image_path, "http")===0 || strpos($image_path, '//')===0) {
            return $image_path;
        }
		$environment = \App::environment();
		$image_site = config('site.storage.'.$environment);
		return sprintf("%s/storage/%s", $image_site, $image_path);
	}

	//加密函数
	public static function passport_encrypt($txt, $key) {
		return base64_encode($txt);
	}
	//解密函数
	public static function passport_decrypt($txt, $key) {
		return base64_decode($txt);
	}

	public static function passport_key($txt, $encrypt_key) {
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++) {
		   $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		   $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
	}

	public static function backurl(){
		$url = \URL::previous();
		if($url == null){
			$url = statIC::route('home');
		}
		return  $url;
	}

	public static function encryStr($str){
		$str = strrev($str);
		return base64_encode($str);
	}

		/**
	 * 解密函数
	 * @param str 待解密字符串
	 * @returns {string}
	 */
	public static function str_decrypt($str) {
	    $str = base64_decode($str);
	    $str = strrev($str);
	    return $str;
	}
}