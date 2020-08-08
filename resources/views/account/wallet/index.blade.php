@extends('layouts.app')

@section('title')@lang('meta.user.index.title')@endsection

@section('header_title') 我的钱包 @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="/account"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">我的钱包</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="wrapbox cpl-fund-home">
	<div class="cpl-fund-home-count">
		<div class="cpl-fund-home-count-view">
			<div class="detailedbtn clearfix"><a href="/account/wallet/record">明细</a></div>
			<h1 class="tit"><i class="iconfont ic-money icon-wallet"></i><loc-i18n>我的钱包</loc-i18n></h1>
			<div class="cpl-fund-home-count-moneybox" style="width:146px">
				<a href="javascript:void(0);" class="cpl-fund-home-count-money">￥{{ $wallet['balance_amount'] }}</a>
				<span class="viewbtn"><i class="ic-cpl ic-eyes-open"></i></span> 
			</div>
        </div>
        <div class="cpl-fund-home-count-operate clearfix">
	        <a href="/cpl/in2TaskCash.xhtml" class="flex">
	        	<i class="iconfont icon-cash ic-withdraw"></i>
	        	<loc-i18n>提现</loc-i18n>
	        </a>
	        <a href="/cpl/in2UserAccountTransferChoose.xhtml" class="flex">
	        	<i class="iconfont icon-zhuanzhang ic-transfer"></i>
	        <loc-i18n>转账</loc-i18n></a>
    	</div>
	</div>
</div>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/index.js') }}"></script>
@endsection

