@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header_title') {{ $title }} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	        <div class="mobile-header-back">
	            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
	        </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="account-warp">
	<ul class="list-group">
		<li class="list-group-item"><a href="{{ Helper::route('help_view', ['about-us']) }}">关于我们<span class="to">></span></a></li>
		<li class="list-group-item"><a href="{{ Helper::route('feedback') }}">建议反馈<span class="to">></span></a></li>
		<li class="list-group-item"><a href="{{ Helper::route('help_view', ['terms-conditions']) }}">注册协议<span class="to">></span></a></li>
		<li class="list-group-item"><a href="{{ Helper::route('help_view', ['store_agreement']) }}">店铺协议书<span class="to">></span></a></li>
	</ul>
</div>
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('media/scripts/view/account/index.js') }}"></script>
@endsection

