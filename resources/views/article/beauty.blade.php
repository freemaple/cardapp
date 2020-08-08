@extends('layouts.app')

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="{{ Helper::route('article') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('content')
@if(!empty($posts) && count($posts))
<div class="js-beauty-post-list-box" data-action="/api/beauty_post" data-page="1">
    <ul class="clearfix js-beauty-post-list">
        @include('article.block.post', ['posts' => $posts])
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
            此宝地尚未开采,需我王来坐拥江山！<br />
            <a href="{{ Helper::route('account_post_add') }}" class="u-link text-info">(请您马上登基)</a>
        </div>
    </div>
@endif
@endsection
@section('copyright', view('template.copyright'))
@section('scripts')
<script type="text/javascript">
    //基础加载
    require(['zepto', 'base', 'scrollComponent', 'echo'], function ($, md_base, scrollComponent, echo) {
        var app = {
            init: function(){
                var self = this;
                var scroll_item = $(".js-beauty-post-list-box");
                //滚动加载
                scrollComponent.init();
                scrollComponent.setScrollItem(scroll_item);
                scrollComponent.setCallback(function(view, scroll_item){
                    self.scrollLoadCallback(view, scroll_item);
                });
            },
            //滚动加载回调
            scrollLoadCallback: function(view, scrollitem){
                scrollitem.find('.js-beauty-post-list').append(view);
                echo.init();
            }
        }
        $(function(){
            app.init();
        });
    }); 
</script>
@endsection

