@extends('layouts.app')

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_center') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('styles')
<style type="text/css">
	.store-info-box {
		background-color: #03a9f4;
		padding: 15px 10px;
		text-align: center;
		color: #fff;
	}
	.store-info-box a {
		font-size: 0.3rem;
		color: #fff
	}
	.store-box {
		font-size: 0.26rem;
	}
	.store-banner-box {
		position: relative;
		height: 3.6rem;
		background: #ffffff;
	}
	.store-banner-box .edit-banner-box {
		position: absolute;
		bottom: 0px;
		right: 0px;
		background: #F1F1F1;
		padding: 10px;
		color: #00f;
		opacity: 0.8;
		border-radius: 5px;
		-webkit-border-radius: 5px;
	}
	.c-span {
		padding-left: 5px;
		color: #f00
	}
	.s-btn {
		color: #00c;
		font-size: 16px;
		background-color: #fe5430;
		padding: 2px 4px;
	}
	.s-btn a {
		color: #fff;
	}
	.u-time-box {
		background: #ffffff;
		padding: 10px;
		margin-top: 10px
	}
	.s-name-nox {
		position: absolute;
		top: 0%;
		margin-top: 12px;
		left: 0%;
		width: 100%;
		font-size: 0.44rem;
		color: #f4fc1a;
		text-align: center;
		z-index: 1
	}
	.store-info-box .s_r_btn {
		padding-top: 4px;
		color: #0fc;
		font-size: 0.28rem;
		display: inline-block;
	}
	.store-box .s_gift {
		font-size: 0.28rem;
		color: #03a9f4
	}
	.s-order-status-panel {
		margin-top: 10px;
	}
	.order-status-box {
		padding: 20px 10px 0px 10px;
		background-color: #ffffff;
		color: #444;
		margin-top: 0px;
		font-size: 
	}
	.s-panel-header {
		padding: 10px;
		background-color: #fe7589;
		color: #fff;
		width: 100%;
		font-size: 0.26rem;
		position: relative;
	}
	.s-panel-header a {
		color: #fff;
		display: block;
	}
	.s-order-status-panel .order-status-item a {
		color: #03a9f4;
		font-size: 0.24rem;
	}
	.s-panel-header .s-add-prodcut-btn {
		color: #00f;
		font-size: 0.28rem;
	}
	.item-bbox {
		background-color: #ffffff;
		margin: 10px 0px;
		padding: 10px 10px;
	}
	.b-item-list {
		font-size: 0px;
	}
	.b-item {
		width: 25%;
		vertical-align: top;
		display: inline-block;
		margin-bottom: 10px;
		padding-right: 10px;
	}
	@media(max-width: 360px){
		.b-item {
			width: 33.33%
		}
	}
	.b-item-box {
		padding: 10px;
		background-color: #ffffff;
		color: #444444;
		text-align: center;
		border-radius: 5px;
		-webkit-border-radius: 5px;
		border: 1px solid #eeeeee;
	}
	.b-item-box:hover {
		background-color: #fe7589;
	    background-image: -webkit-linear-gradient(bottom, #ff587f, #fe7549);
	    background-image: linear-gradient(0deg, #ff587f 0, #fe7549);
	}
	.b-item-box a {
		color: #fe5430
	}
	.b-item .text {
		font-size: 0.24rem;
		color: #fe5430
	}
	.b-item .value {
		font-size: 0.26rem;
		color: #00f
	}
	.b-item-box:hover a {
		color: #fff;
	} 
	.b-item-box:hover .text {
		color: #fff;
	} 
	.btn-item-list .b-item {
		width: 33.33%
	}
	.btn-item-list .b-item-box {
		
	}
	.btn-item-list .b-item-box .text {
		color: #00f
	}
	.form-box {
		width: 50%;
		display: inline-block;
		vertical-align: middle;
		margin-right: -4px;
	}
	.form-box select {
		width: 100%! important
	}
</style>
@endsection

@section('content')
<div class="">
	<div class="pd-10" style="background-color: #ffffff;margin-top: 10px">
	<form method="get" class="search-form">
		<div class="form-box" style="padding-right: 5px">
			<span>年份</span>
			<select class="search-select" name="year" style="display: inline-block;width: 80px">
	            <option value="">全部</option>
	            @for($y=2018; $y<=2100; $y++)
	            <option value="{{$y}}" @if($year == $y) selected="selected" @endif>{{$y}}</option>
	            @endfor
	        </select>
		</div>
		<div class="form-box" style="padding-right: 5px">
			<span>年份</span>
			<select class="search-select" name="month" style="display: inline-block;width: 80px">
	            <option value="">全部</option>
	            @foreach($months as $m)
	            <option value="{{$m}}" @if($month == $m) selected="selected" @endif>{{$m}}</option>
	            @endforeach
	        </select>
		</div>
	</form>
</div>
	<div class="item-bbox" style="margin-top: 5px" style="background: #f5f5f5">
		<div class="b-item-list">
			<div class="b-item">
				<div class="b-item-box">
					<div class="value">
						{{ isset($user_statistics['referrer_user_count']) ? $user_statistics['referrer_user_count'] : 0 }}
					</div> 
					<span class="text">直推数</span> 
				</div>
			</div>
			<div class="b-item">
				<div class="b-item-box">
					<div class="value">
						{{ isset($user_statistics['vip_open_number']) ? $user_statistics['vip_renewal_number'] : 0 }}
					</div> 
					<span class="text">vip开通数</span> 
				</div>
			</div>
			<div class="b-item">
				<div class="b-item-box">
					<div class="value">
						{{ isset($user_statistics['vip_renewal_number']) ? $user_statistics['vip_renewal_number'] : 0 }}
					</div> 
					<span class="text">vip续费量</span>
				</div>
			</div>
			<div class="b-item">
				<div class="b-item-box">
					<div class="value">
						{{ isset($user_statistics['store_number']) ? $user_statistics['store_number'] : 0 }}
					</div>
					<span class="text">店铺总缴费量</span>
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
<script type="text/javascript">
	//基础加载
	require(['zepto', 'base', 'mylayer'], function ($, md_base, mylayer) {
	    var app = {
	        init: function(){
	            $(".search-select").on('change', function(){
	            	$(this).closest('form').submit();
	            })
	        }
	    };
	    if(typeof app.init == 'function') {
	        $(function () {
	            app.init();
	        });
	    }
	}); 
</script>
@endsection


