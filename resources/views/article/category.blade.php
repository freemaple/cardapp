@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="{{ Helper::route('article') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ !empty($category) ? $category['name'] : '文库' }}</div>
	    </div>
	</div>
    <div class="site-cate-fixed-box">
        <div class="site-cate-nav site-cate-fixed">
            <ul class="site-cate-nav-list">
                <li class="cate-nav-item  @if(empty($category['id'])) current @endif" data-id="0">
                <a href="{{ Helper::route('article_category_view', [0]) }}"><span>全部</span></a></li>
                @if(!empty($categorys))
                @foreach($categorys as $pkey => $cate)
                <li class="cate-nav-item  @if(!empty($category['id']) &&  $category['id'] == $cate['id']) current @endif" data-id="{{ $cate['id'] }}"><a href="{{ Helper::route('article_category_view', [$cate['id']]) }}"><span>{{ $cate['name'] }}</span></a></li>
                @endforeach
                @endif
            </ul>
        </div>
    </div>
@endsection

@section('content')
@if(!empty($posts) && count($posts))
<div class="js-category-post-list-box" data-action="/api/category/post?category_id={{ $category['id'] or '0' }}" data-page="1">
    <ul class="clearfix js-category-post-list">
        @include('article.block.post', ['posts' => $posts])
    </ul>
    <div class="waiting-load-block js-load-block"  style="display: none">
        <div class="waiting-loading"></div>
        <div class="text">Loading...</div>
    </div>
</div>
@else
    <div class="no-results">
        <div class="result-img">
            <div class="result-img">@include('template.rote')</div>
        </div>
        <div class="result-content">
            此宝地尚未开采,需我王来坐拥江山！<br />
            <a href="{{ Helper::route('account_post_add') }}" class="u-link text-info">(请您马上登基)</a>
        </div>
    </div>
@endif
@endsection
@section('scripts')
<script type="text/javascript">
    //基础加载
    require(['zepto', 'base', 'scrollComponent', 'echo'], function ($, md_base, scrollComponent, echo) {
        var app = {
            init: function(){
                var self = this;
                var scroll_item = $(".js-category-post-list-box");
                //滚动加载
                scrollComponent.init();
                scrollComponent.setScrollItem(scroll_item);
                scrollComponent.setCallback(function(view, scroll_item){
                    self.scrollLoadCallback(view, scroll_item);
                });
            },
             //滚动加载回调
            scrollLoadCallback: function(view, scrollitem){
                scrollitem.find('.js-category-post-list').append(view);
                echo.init();
            }
        }
        app.init();
    }); 
</script>
@endsection



