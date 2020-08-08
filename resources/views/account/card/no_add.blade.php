@extends('layouts.app')
@section('styles')
<style type="text/css">
    .no-results .result-content {
        margin-top: 0px;
    }
</style>
@endsection
@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="{{ Helper::route('home') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">名片已达可用次数</div>
	    </div>
	</div>
@endsection
@section('content')
<div class="no-results">
    <div class="result-content">
        <p>“我王是VIP已赠送三宫，如要六院，需国库拨款建设！</p>
        <p>
            VIP特价<span class="text-red">￥68</span>，增加<span class="text-red">6</span>个名片！
            拨款建设
        </p>
    </div>
     <div class="control_group">
        <a class="btn btn-success" href="{{ Helper::route('checkout_card_renewal') }}">充值开通</a>
    </div>
</div>
@endsection

