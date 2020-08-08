@extends('layouts.app')

@section('header_title') 我的推荐人 @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">我的推荐人</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="account-warp">
	<div class="">
		<ul class="clearfix rf-list js-rf-list">
	        @foreach($referrers['data'] as $ckey => $rf)
		    <li class="list-item js-rf-item js-rf-item-{{ $rf['id'] }} clearfix">
	            <div class="list-item-content clearfix">
	                <div class="img lazy">
                        <img data-src="{{ $rf['avatar'] or '' }}" class="lazyload" />
                    </div>
                    <div class="info">
                        <div class="info-box">
                            <div class="name">{{ $rf['fullname'] }}</div>
                            <div class="name">电话号码：{{ $rf['phone'] }}</div>
                            <div class="name">注册时间：{{ $rf['created_at'] }}</div>
                            <div class="name">荣誉功勋：{{ $rf['honor_value'] }} ：{{ $rf['honor_vip_value'] }} </div>
                        </div>
                    </div>
	            </div>
		    </li>
	        @endforeach
    	</ul>
	</div>
</div>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/index.js') }}"></script>
@endsection

