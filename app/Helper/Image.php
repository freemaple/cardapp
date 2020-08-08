<?php

namespace App\Helper;

use File;

class Image
{   
	//生成storage目录文件路径
	public static function storagePath($image_path){
		if (strpos($image_path, "http")===0 || strpos($image_path, '//')===0) {
            return $image_path;
        }
		$environment = \App::environment();
		$image_site = config('site.site_image.'.$environment);
		if($environment == 'local'){
			return sprintf("%s/%s", $image_site, $image_path);
		} else {
			return sprintf("%s/%s", $image_site, $image_path);
		}
	}

	//生成storage目录文件路径
	public static function background($image_path){
		if (strpos($image_path, "http")===0 || strpos($image_path, '//')===0) {
            return $image_path;
        }
		$environment = \App::environment();
		$image_site = config('site.site_image.'.$environment);
		return sprintf("%s/storage/background/%s", $image_site, $image_path);
	}

	public static function getProductImage($spu, $image, $size = null) {
		if(empty($image)){
			return "";
		} else {
			$dp = substr($spu, 0, 2);
			$directory = static::storagePath('product/' . $dp);
			if($size == null){
				return sprintf('%s/%s.jpg',  $directory, $image);	
			} else {
				return sprintf('%s/%s_%s.jpg',  $directory, $image, $size);
			}	
		}
	}

	public static function getavatar($avatar){
		if($avatar == null){
			return url('/media/images/default.png');
		} else {
			return static::storagePath($avatar);
		}
    }
}