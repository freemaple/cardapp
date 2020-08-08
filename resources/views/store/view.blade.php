@extends('layouts.app')

@section('styles')
<style type="text/css">
	.store-info-box {
		background-color: #03a9f4;
		padding: 20px 10px;
		text-align: center;
		color: #fff;
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
</style>
@endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	        <div class="mobile-header-right share-header-right">
	        	@if(!empty($store) && $store_enable == 1)
            	<span class="share-icon js-share-link"><span class="iconfont icon-share1"></span><div style="color: #0942fa">分享好店</div></span>
            	@endif
        	</div>
	    </div>
	</div>
@endsection

@section('content')
@if(!$store_enable && (empty($session_user) || $session_user['id'] != $store['user_id']))
<div class="no-results">
        <div class="result-img">@include('template.rote')</div>
	    <div class="result-content">
	    	@if(empty($store))
	    	<p>店铺不存在</p>
	    	@elseif($store['status'] == '1')
	    		<p>店铺已下架</p>
	    	@else 
	    		<p>店铺未上架</p>
	    	@endif
	    </div>
	    <div class="control-group">
	    	@if(!empty($store) && $store['user_id'] == $user['id'])
	    	<a class="btn btn-primary" href="{{ Helper::route('account_store') }}">返回店铺</a>
	    	@else
	        <a class="btn btn-primary" href="{{ Helper::route('home') }}">浏览其它</a>
	        @endif
	    </div>
	</div>
@else
<div class="bg-f">
	<div class="store-box">
		<div class="store-item">
			<div style="position: relative;">
				<a href="{{ Helper::route('store_info_view', [$store['id']]) }}"><img src="{{ Helper::asset_url('/media/images/bstore.gif') }}" style="padding: 0px 10px">
				<div style="position: absolute;top: 12px;left: 0%;width: 100%;font-size: 0.44rem;color: #f4fc1a;text-align: center;z-index: 1">{{ $store['name'] or '' }}</div>
					<span style="position: absolute;right: 20px;top: 20px;"><div style="color: #fff;font-size: 14px;">详情</div></span>
				</a>
			</div>
			<div class="img js-store-banner-upload"><img src="@if(empty($store['banner'])) {{ Helper::asset_url('/media/images/default_store_banner.png') }} @else {{ HelperImage::storagePath($store['banner']) }} @endif" /></div>
			<div style="padding: 0px 10px 10px 10px">
				<div class="name">
					<a href="{{ Helper::route('store_info_view', [$store['id']]) }}">{{ $store['name'] }}</a>
					<span class="pull-right">浏览量 <span class="text-info value">{{ $store['view_number'] }}</span>次</span>
				</div>
			</div>
		</div>
	</div>
</div>
@if(count($products) > 0)
<div class="store-product-box">
	<div style="position: relative;"> 
    	<img src="/media/images/bcate.png?1552202160" style="width: 100%;position: absolute;width: 100%;
    	position: absolute;top: -12px;max-height: 60px;z-index: 2">
    	<div style="position: absolute;z-index: 3;text-align: center;width: 100%;top: 2px;color: #fff;font-size: 16px">本 店 精 选 产 品</div>
    </div>
	<ul style="padding-top: 38px">
		@include("shop.block.products",['products' => $products])
	</ul>
</div>
@endif
<div class="store-product-box">
	<div style="position: relative;"> 
    	<img src="/media/images/bcate.png?1552202160" style="width: 100%;position: absolute;width: 100%;
    	position: absolute;top: -12px;max-height: 60px;z-index: 2">
    	<div style="position: absolute;z-index: 3;text-align: center;width: 100%;top: 2px;color: #fff;font-size: 16px">我的共享店铺</div>
    </div>
	<ul style="padding-top: 38px">
		@include("shop.block.products",['products' => $self_products])
	</ul>
</div>
<input type="hidden" id="store_id" name="store_id" value="{{ $store['id'] or '' }}"   />
<input type="hidden" id="is_viewd"  value="{{ $is_viewd }}"  />
@endif
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/store.js') }}"></script>
@endsection