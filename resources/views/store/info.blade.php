@extends('layouts.app')

@section('styles')
<style type="text/css">
	.store-block {
		padding: 10px;
		border-bottom: 1px solid #e2e2e2;
		line-height: 25px;
		font-size: 12px;
	}
	.s-av {
		float: left;
		width: 15%;
		margin-right: 2%;
	}
	.s-av img {
		width: 100%
	}
	.s-info {
		float: left;
		width: 83%;
	}
	.license-f li {
		float: left;
		width: 33.33%;
		position: relative;
	}
	.license-f li img {
		width: 100%;
	}
	.layer {
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0px;
		left: 0px;
		background-color: rgba(0, 0, 0, 0.5); 
		background-size: 100%;
		background-image: url({{Helper::asset_url('/media/images/clayer.png')}});
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
	    </div>
	</div>
@endsection

@section('content')
@if(!$store_enable  && (empty($session_user) || $session_user['id'] != $store['user_id']))
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
	    	@if(!empty($store) && $store['user_id'] == $seller_user['id'])
	    	<a class="btn btn-primary" href="{{ Helper::route('account_store') }}">返回店铺</a>
	    	@else
	        <a class="btn btn-primary" href="{{ Helper::route('home') }}">浏览其它</a>
	        @endif
	    </div>
	</div>
@else
<div class="bg-f">
	<div class="store-block clearfix">
		<div class="s-av">
			<a href="{{ !empty($card) ? Helper::route('card_view', [$card['card_number']]) : '' }}"><img src="{{ HelperImage::getavatar($store_user['avatar']) }}"></a>
		</div>
		<div class="s-info">
			<div>{{ $store['name'] }}</div>
			<div>店铺荣誉：{{ $store['rating_honor'] }}</div>
			<div class="rating-box">
				综合评价 
				<ul class="rating-star clearfix" style="display: inline-block;">
					@for($i = 1; $i<=5; $i++)
					<li class="@if($i<=$store['rating'])select @endif"></li>
					@endfor
				</ul>
				{{ $store['rating'] }}
			</div>
		</div>
	</div>
	<div class="store-block clearfix">
		<div>店长姓名 {{ $store['contact_user_name'] }}</div>
		<div>服务电话 {{ $store_user['phone'] }}</div>
		<div>所在地 {{ $store['provice'] }} {{ $store['city'] }}</div>
		<div>开店时间 {{ $store['created_at'] }}</div>
	</div>
	@if(!empty($store['business_license_front']) || !empty($certificateImage))
	<div class="store-block clearfix">
		<div>店铺资质信息</div>
		<ul class="license-f">
			@if(!empty($store['business_license_front']))
			<li>
				<img src="{{ HelperImage::storagePath($store['business_license_front']) }}" class="img" />
				<div class="layer"></div>
			</li>
			@endif
			@foreach($certificateImage as $ckey => $cImage)
			<li>
				<img src="{{ HelperImage::storagePath($cImage['image']) }}" />
				<div class="layer"></div>
			</li>
			@endforeach
		</ul>
	</div>
	@endif
</div>
@endif
@endsection
@section('footer')
	
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/store.js') }}"></script>
@endsection