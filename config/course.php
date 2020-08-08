<?php

return [
	'order' => [
		'payment_method' => [
			['code' => 'offline', 'name' => '线下'],
			//['code' => 'alipay', 'name' => '支付宝']
		],
		'status' => [
			'1' => '待审核',
			'2' => '进行中',
			'3' => '完成',
			'0' => '取消'
		],
		'payment_status' => [
			'1' => '待支付',
			'2' => '已支付',
			'0' => '取消'
		],
		'record' =>  [
			'status' => [
				'1' => '审核中',
				'2' => '授课中',
				'3' => '已完成',
				'0' => '已取消'
			],
			'search_status' => [
				'processing' => [
					'text' => '进行中', 
					'value' => [1,2]
				],
				'completed' => [
					'text' => '已完成', 
					'value' => 3
				],
				'cancel' =>  [
					'text' => '已取消', 
					'value' => 0
				]
			]
		]
	]
];