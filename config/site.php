<?php

return [
	// 站点简码
	'site_code' => 'TP',
	'ga_open' => false,
	'secure' => false,
	// 站点名称
	'site_name' => '',
	//版本
	'version' => '1.1.4089',
	//活动、设计图片域名
	'site_image' =>  [
		'local' => '',
		'production' => 'https://image.renrenyoushang.com'
	],
	'storage' => [
		'local' => '',
		'production' => ''
	],
	'baidu_key' => 'd8acabffd0d64cdd906d84d9c935209d',
	'test_vip' => env('test_vip', '0'),
	'test_store' => env('test_store', '0'),
];
