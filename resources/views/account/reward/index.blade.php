@extends('layouts.app')

@section('title'){{ $title }}@endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="/account"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="wrapbox cpl-fund-home">
	<div class="cpl-fund-home-count">
		<div class="cpl-fund-home-count-view">
			<div class="detailedbtn clearfix"></div>
			<h1 class="tit"><i class="iconfont ic-money icon-wallet"></i><loc-i18n>我的余额</loc-i18n></h1>
			<div class="cpl-fund-home-count-moneybox">
				<span class="cpl-fund-home-count-money">￥{{ isset($reward['amount']) ? $reward['amount'] : 0 }}</span>
				<span class="viewbtn"><i class="ic-cpl ic-eyes-open"></i></span> 
			</div>
			<div style="color: #fff">
				<span>可用:￥</span><span>{{ $reward['amount'] -  $reward['freeze_amount'] }} </span>
				@if(isset($reward['freeze_amount']))
				<span style="padding: 0px 5px"><span>交易中:￥</span><span>{{ $reward['freeze_amount'] }}</span></span>
				@endif
			</div>
        </div>
        <div class="cpl-fund-home-count-operate clearfix">
	        <a href="{{ Helper::route('account_payout_index') }}" class="flex">
	        	<i class="iconfont icon-cash ic-withdraw"></i>
	        	<loc-i18n>提现记录</loc-i18n>
	        </a>
	        <a href="{{ Helper::route('account_payout_apply') }}" class="flex">
	        	<i class="iconfont icon-zhuanzhang ic-transfer"></i>
	        <loc-i18n>申请提现</loc-i18n></a>
    	</div>
	</div>
</div>
<div class="reward-records-list-box js-reward-records-list-box" data-action="/api/reward/record" data-page="1">
	<ul class="clearfix reward-records-list js-reward-records-list"></ul>
	<div class="waiting-load-block js-load-block" style="display: none">
    	<div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
	</div>
</div>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/reward.js') }}"></script>
@endsection

