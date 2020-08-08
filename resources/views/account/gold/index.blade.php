@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header_title') {{ $title }} @endsection

@section('styles')
<style type="text/css">
	.mobile-header-box {
		border-bottom: 0px;
		background-color: #0b93ff;
		 background-image: none;
	}
	.cpl-fund-view {
		background-color: #0b93ff;
    	padding-bottom: 15px;
    	color: #fff;
    	padding: 10px;;
    	line-height: 25px;
	}
	.cpl-fund-panel {
		padding: 10px 0px;
		color: #333333;
		background-color: #ffffff;
		line-height: 25px
	}
	.money-list {
		font-size: 0.3rem;
	}
	.cpl-fund-panel .money-list {
		width: 80%;
		margin: 5px auto 10px auto;
	}
	.tit {
    	color: #ffc8c8;
   		font-size: 0.3rem;
    	text-align: center;
    	padding: 10px 0px;
	}
	.tit i {
	    color: #f9d30c;
	    font-size: 34px;
	    margin-right: 5px;
	    display: inline-block;
	    vertical-align: middle;
	}
	.cpl-fund-panel .tit {
		color: #444444;
		 font-size: 0.32rem;
	}
	.tit-title {
		font-size: 0.3rem;
    	color: #666666;
	}
	.money-left {
		float: left;
		text-align: center;
	}
	.money-right {
		float: right;
		text-align: center;
	}
	.money-title {
		color: #f1f1f1;
		font-size: 0.24rem;
	}
	.btn-op {
		color: #e74c3c;
		height: 34px;
		line-height: 34px;
	}
	.cpl-fund-panel  .money-title {
		color: #999999;
		font-size: 0.24rem;
	}
	.m-list-item {
		padding: 10px 20px;
		border-top: 1px solid #f1f1f1;
	}
	.m-list-item .text {
		margin-right: 30px;
		display: inline-block;
	}
</style>
@endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="wrapbox cpl-fund-home">
	<div class="cpl-fund-home-count">
		<div class="cpl-fund-view">
			<h1 class="tit" style="color: #fff"><loc-i18n>麦仓总资产 ￥{{ $total_amount }}</loc-i18n></h1>
			<div class="money-list clearfix">
				<div class="money-left">
					<span class="money-title">今日置换</span>
					<p>{{ $gift_unit }}麦粒 / 支</p>
				</div>
				<div class="money-right">
					<span class="money-title">今日麦穗红利</span>
					<p>￥{{ $day_bonus_unit }} / 支</p>
				</div>
			</div>
        </div>
        <div class="cpl-fund-panel">
			<div class="tit">
				<span class="tit-title">有赏红利余额</span>
				<div class="" style="padding: 5px 0px">
					<span">￥{{ $bonus_amount }}</span>
					@if($bonus_amount > 0)
					<p><span class="btn-op js-show-bonusToReward">转出使用</span></p>
					@endif
				</div>
			</div>
			<div class="money-list clearfix">
				<div class="money-left">
					<span class="money-title">累计收益</span>
					<p>￥{{ $income_total }}</p>
				</div>
				<div class="money-right">
					<span class="money-title">昨日收入</span>
					<p>+{{ $yesterday_bonus_amount }}</p>
				</div>
			</div>
			<div class="m-list">
				<div class="m-list-item">
					<div class="text">
						金麦穗价值<br />
						￥{{ $gold_total }}
					</div>
					@if($gold_total > 0)
					<span class="btn-op js-show-goldComtoReward">转到余额使用</span>
					@endif
				</div>
				<div class="m-list-item">
					<div class="text">
						金麦穗数量<br />
						{{ $gold_number }}支
					</div>
					@if($gift_commission > 0 && $user->is_vip)
					<span class="btn-op js-show-giftComtoGold">加持转入</span>
					@endif
				</div>
			</div>
        </div>
	</div>
</div>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script type="text/template" id="bonusToReward-template">
	<div class="reward-toIntegral-box" style="padding: 10px;max-width: 95%;width: 360px">
		<div class="toIntegral-header">红利转出使用</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control bonus_to_reward_tpwd" type="password" name="transaction_password" required="required" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div class="form-group">注：转出红利=10%转户手续，45%有赏积分，45%余额</div>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block js-bonusToReward">确认转出</a>
		</div>
	</div>
</script>
<script type="text/template" id="goldComtoReward-template">
	<div class="reward-toIntegral-box" style="padding: 10px;max-width: 95%;width: 360px">
		<div class="toIntegral-header">金麦穗价值转出余额</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control gold_to_reward_tpwd" type="password" name="transaction_password" required="required" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block js-goldComtoReward">确认转出</a>
		</div>
	</div>
</script>
<script type="text/template" id="giftComtoGold-template">
	<div class="reward-toIntegral-box" style="padding: 10px;max-width: 95%;width: 360px">
		<div class="toIntegral-header">麦粒置换金麦穗</div>
		<div class="form-group">
			<div class="form-group-label">当前剩余麦粒：<text class="text-red" style="font-size: 0.32rem">{{ $gift_commission }}</text></div>
		</div>
	    <div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>麦粒</div>
			<input type="number" class="form-control giftcom_to_gold_number" required="required" name="amount" min="0" step="1"  />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control giftcom_to_gold_tpwd" type="password" name="transaction_password" required="required" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block js-giftComtoGold">确认转换</a>
		</div>
	</div>
</script>
<script src="{{ Helper::asset_url('/media/scripts/view/account/gold.js') }}"></script>
@endsection

