<?php

return [
	'level_status' =>  [
		'0' => '普通用户',
		'1' => '金麦用户',
		'2' => '钻麦用户 ',
		'3' => '铂麦vip',
		//'4' => '钻石vip'
	],
	/*'vip' =>  [
		'firstCommission1' => 40,
		'secondCommission1' => 30,
		'secondCommission2' => 50
	],
	'renewalVIP' => [
		'firstCommission1' => 20,
		'firstCommission2' => 20,
		'secondCommission1' => 20
	],*/
	'vip' =>  [
		'firstCommission1' => 50,
		'secondCommission1' => 30,
		'secondCommission2' => 50
	],
	'renewalVIP' => [
		'Commission1' => 30,
		'Commission2' => 40,
		'Commission3' => 48
	],
	'integral' => [
		'max_sales_points' => 3000
	],
	'recharge_type' => [
		'vip' => '开通vip',
		'integral' => '积分充值',
		'store' => '店铺充值',
		'card_renewal' => '名片续费',
	],
	'phonecode_type' => [
		'sign_up' => '注册',
		'password' => '登录密码重置',
		'transaction_password' => '交易密码重置',
		'payout' => '提现'
	],
	'user_type' => [
		'general' => '一般会员',
		'manager' => '总监',
		'director' => '总经理'
	],
	'task' => [
		'task_integral' => 12
	],
	'user_ref_sub_integral_amount' => 20
];