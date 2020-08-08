@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

@section('styles')
<style type="text/css">
    .notice-text {
        padding: 0px 5px;
    }
    .notice-text-content {
        position: absolute;
        width: 100%;
        top: 0px;
        color: #fff;
        padding: 10px 10px 10px 20px;
    }
    .xanimate {
        position: absolute;
        top: 0px;
        left: 20px;
        z-index: 1;
        padding-left: 20px;
        font-size: 12px;
        white-space: nowrap;
        animation:  wordsLoop 5s linear 0ms infinite normal;
    }

    @keyframes wordsLoop {
        0% {
            transform: translateX(0px);
            -webkit-transform: translateX(0px);
            
        }
        100% {
           transform: translateX(-100%);
            -webkit-transform: translateX(-100%);
        }
    }

    @-webkit-keyframes wordsLoop {
        0% {
            transform: translateX(0px);
            -webkit-transform: translateX(0px);
          
        }
        100% {
            transform: translateX(-100%);
            -webkit-transform: translateX(-100%);
        }
    }
</style>
@endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }} （{{$referrers['total'] }}）</div>
	    </div>
	</div>
	<div style="background: #f00;color: #fff;padding: 20px;position: relative;">
	    <div class="notice-text-box">
            <div class="notice-text-content xanimate">通告：{{ !empty($notice['content']) ? $notice['content'] : '常跟战友联系，带战友一起飞哦！' }}</div>
        </div>
	</div>
	<script type="text/template" id="weixin-box-template">
	    <div class="qr-box pop-bt-codebox">
	       <div>
	            <img src="" style="width: 100%" class="current-weixin-qr" />
	        </div>
	    </div>
	</script>
@endsection

@section('content')
@if(!empty($referrers['data']))
	<div class="js-referrer-list-box"  data-page="1" data-action="/api/account/referrer">
		<ul class="clearfix rf-list js-rf-list">
			@include('account.referrer.block.list', ['referrer_list' => $referrers['data']])
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
	        <p>对不起,您还没任何战友！</p>
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
	require(['zepto', 'base', 'scrollComponent', 'mylayer'], function ($, md_base, scrollComponent, mylayer) {
	    var app = {
	        init: function(){
	            var self = this;
	            var scroll_item = $(".js-referrer-list-box");
	            //滚动加载
	            scrollComponent.init();
	            scrollComponent.setScrollItem(scroll_item);
	            scrollComponent.setCallback(function(view, scroll_item){
	                self.scrollLoadCallback(view, scroll_item);
	            });
	            //显示微信
	            $(".js-show-weixin").on("click", function(){
	            	var qr = $(this).attr('data-weixin-qr');
	                var content = $("#weixin-box-template").html();
	                mylayer.init({
	                    content: content,
	                    close: false,
	                    class_name: "layer-weixin layer-bottom bottom-to-top",
	                    position: 'bottom',
	                    callback: function(){
	                       
	                    }
	                });
	                $(".current-weixin-qr").attr('src', qr);
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

