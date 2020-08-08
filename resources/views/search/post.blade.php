@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">{{ str_limit($title, 20) }}</div>
    </div>
</div>
@endsection

@section('content')
    @if(!empty($posts))
        <div class="product-list-box">
            <div class="product-list-box js-search-post-container" data-action="/api/search/post?keyword={{ $keyword or '' }}" data-page="1">
                <ul class="clearfix js-search-post-list">
                    @include("search.block.post",['posts' => $posts])
                </ul>
                <div class="waiting-load-block js-load-block" style="display: none">
                    <div class="waiting-loading"></div>
                    <div class="text">Loading...</div>
                </div>
            </div>
        </div>
    @else
        <div class="no-results">
            <div class="result-img">@include('template.rote')</div>
            <div class="result-content">
                <p>您的能量太强大了，该页面不存在都被你找到了</p>
            </div>
        </div>
    @endif
    <input type="hidden" id="currentkeyword" value="{{ $keyword or '' }}" data-type='search' />
@endsection

@section('copyright', view('template.copyright'))

@section('scripts')
<script type="text/javascript">
    //基础加载
    require(['zepto', 'base', 'scrollComponent'], function ($, md_base, scrollComponent) {
        var app = {
            init: function(){
                var self = this;
                var scroll_item = $(".js-search-post-container");
                //滚动加载
                scrollComponent.init();
                scrollComponent.setScrollItem(scroll_item);
                scrollComponent.setCallback(function(view, scroll_item){
                    self.scrollLoadCallback(view, scroll_item);
                });
            },
            //滚动加载回调
            scrollLoadCallback: function(view, scrollitem){
                scrollitem.find('.js-search-post-list').append(view);
            }
        }
        $(function(){
            app.init();
        });
    }); 
</script>
@endsection