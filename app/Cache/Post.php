<?php

namespace App\Cache;

use App\Libs\Service\PostService;

class Post
{
	public static function getPost($post_number = '')
	{
		$key = 'post:' . $post_number;
		$post = Base::cacheGet($key);
		if(empty($post)){
			$post = PostService::getPost($post_number);
			Base::cachePut($key, $post);
		}
		return $post;
	}

	public static function clearPostCache($post_number = '')
	{
		$key = 'post:' . $post_number;
		$post = Base::cacheGet($key);
		if($post != null){
			Base::cacheDel($key);
		}
		return $post;
	}

	public static function recomBeautyPost($post_number = '')
	{
		$key = 'beautyPost';
		$beautyPost = Base::cacheGet($key);
		if($beautyPost == null){
			$beautyPost = PostService::beautyPost(10);
		}
		return $beautyPost;
	}

	public static function clearRecomBeautyPost()
	{
		$key = 'beautyPost';
		$beautyPost = Base::cacheGet($key);
		if($beautyPost != null){
			Base::cacheDel($key);
		}
	}
}