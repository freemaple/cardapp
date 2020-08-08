@extends('layouts.app')

@section('header_title') 管理中心 @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('content')
<style type="text/css">
	.txt {
		text-shadow: 4px 0 10px rgba(0, 0, 0, 0.13);
		line-height: 25px;
		text-align: center;
		font-size: 19px;
		color: #fa4f04;
	}
	.account-home-info {
		padding-bottom: 40px;
	}
</style>
<div class="account-warp">
	<div class="account-home-info clearfix" style="background: #fff">
		<div class="entry-box-logo text-center">
            <img src="{{ asset('/media/images/logo.png') }}">
        </div>
        <div class="txt">
        	<span style="font-size: 28px;">分享 创富 融合 感恩</span><br />
        	<span style="font-size: 0.32rem">个人网页 自媒体 新零售 让创业更简单</span>
    </div>
	<div class="account-menu">
		<ul class="list-group">
			<li class="list-group-item"><a href="{{ Helper::route('account_card_index') }}"><span class="icon-box icon-box-idcard"><span class="iconfont icon-idcard"></span></span>我的名片<span class="to">></span></a></li>
			<li class="list-group-item"><a href="{{ Helper::route('account_post_index') }}"><span class="icon-box icon-box-article"><span class="iconfont icon-article"></span></span>我的文章<span class="to">></span></a></li>
			<li class="list-group-item"><a href="{{ Helper::route('account_store', ['to_product' => '1']) }}"><span class="icon-box icon-box-setting"><span class="iconfont icon-share"></span></span>我的扫码购<span class="to">></span></a></li>
			@if($session_user['is_vip'] == '1')
			<li class="list-group-item"><a href="{{ Helper::route('shop') }}"><span class="icon-box icon-box-setting"><span class="iconfont icon-share1"></span></span>我要卖货<span class="to">></span></a></li>
			@endif
			<li class="list-group-item"><a href="{{ Helper::route('account_card_screen') }}"><span class="icon-box icon-box-setting"><span class="iconfont icon-setting"></span></span>屏保名片<span class="to">></span></a></li>
		</ul>
	</div>
	<div style="margin: 20px 0px 0px;text-align: center;">
		@if($session_user['is_vip'] == '1')
		<img src="{{ $card_qrcode or '' }}" width="120" />
		<p>我的名片二维码</p>
		@endif
		<p>
			<a class="btn btn-primary" href="{{ Helper::route('help_school') }}">到商学院学习</a>
		</p>
	</div>
</div>
@section('copyright', view('template.copyright'))
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection


