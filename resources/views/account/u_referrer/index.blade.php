@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}（{{$referrers['total'] }}）</div>
	    </div>
	</div>
@endsection

@section('content')
@if(!empty($referrers['data']))
	<div class="js-ureferrer-list-box"  data-page="1" data-action="/api/account/u_referrer">
		<ul class="clearfix rf-list js-rf-list">
			@include('account.u_referrer.block.list', ['referrer_list' => $referrers['data']])
		</ul>
		<div class="waiting-load-block js-load-block"  style="display: none">
	    	<div class="waiting-loading"></div>
	    	<div class="text">Loading...</div>
		</div>
	</div>
@else
	<div class="no-results">
	    <div class="result-img">@include('template.rote')</div>
	    <div class="result-content">
	        <p>对不起,您还没任何粉丝！</p>
	    </div>
	</div>
@endif
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script>
	//基础加载
	require(['zepto', 'base', 'scrollComponent'], function ($, md_base, scrollComponent) {
	    var app = {
	        init: function(){
	            var self = this;
	            var scroll_item = $(".js-ureferrer-list-box");
	            //滚动加载
	            scrollComponent.init();
	            scrollComponent.setScrollItem(scroll_item);
	            scrollComponent.setCallback(function(view, scroll_item){
	                self.scrollLoadCallback(view, scroll_item);
	            });
	        },
	        //滚动加载回调
	        scrollLoadCallback: function(view, scrollitem){
	            scrollitem.find('.js-rf-list').append(view);
	        }
	    }
	    if(typeof app.init == 'function') {
	        $(function () {
	            app.init();
	        });
	    }
	}); 
</script>
@endsection

