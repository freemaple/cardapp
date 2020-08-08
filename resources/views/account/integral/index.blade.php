@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header_title') {{ $title }} @endsection

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
		<div class="cpl-fund-home-count-view">
			<div class="detailedbtn clearfix"></div>
			<h1 class="tit" style="color: #fff"><i class="iconfont ic-money icon-wallet"></i><loc-i18n>我的有赏积分</loc-i18n></h1>
			<div class="cpl-fund-home-count-moneybox">
				<span class="cpl-fund-home-count-money" style="font-size: 18px;">总积分￥{{ isset($integral['point']) ? $integral['point'] : '0' }}</span>
				<span class="viewbtn"><i class="ic-cpl ic-eyes-open"></i></span> 
			</div>
			<div>
				<span  style="color: #fff;font-size: 16px">含店铺结算积分：{{ isset($integral['store_sales_points']) ? $integral['store_sales_points'] : '0' }}</span>
				<a class="operate-btn js-show-integral-toreward" style="margin-left: 5px" href="javascript:void(0)" data-can-toreward="{{ $can_toreward }}" data-tip="店铺结算积分满￥{{ $max_sales_points }}才可转入余额">转入余额</a></p>
			</div>
        </div>
	</div>
</div>
<div class="integral-records-list-box js-integral-records-list-box" data-action="/api/integral/record" data-page="1">
	<ul class="clearfix integral-records-list js-integral-records-list"></ul>
	<div class="waiting-load-block js-load-block" style="display: none">
    	<div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
	</div>
</div>
<script type="text/template" id="toreward-template">
	<div class="" style="padding: 10px;max-width: 95%;width: 360px">
		<div style="color: #fe7589;text-align: center;font-size: 18px">店铺结算积分转入余额</div>
	    <div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>金额</div>
			<input type="number" class="form-control integral_toreward_amount" required="required" name="amount" min="0" max="{{ isset($integral['point']) ? $integral['point'] : '0' }}" />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control reward_transaction_password" type="password" name="transaction_password" required="required" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div class="form-group">
			<span class="text-info">注：店铺结算积分满{{ $max_sales_points }}可转入余额！</span>
		</div>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block js-integral-toreward">确认转入</a>
		</div>
	</div>
</script>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/integral.js') }}"></script>
@endsection

