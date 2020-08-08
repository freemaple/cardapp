@extends('layouts.app')

@section('title'){{ $title }}@endsection

@section('header_title') {{ $title }} @endsection

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="{{ Helper::route('product_view', [$product['id']]) }}"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">{{ $title }}</div>
        </div>
    </div>
@endsection

@section('content')
<div class="product-reviews-list-box js-product-reviews-list-box" data-action="/api/product/reviews?product_id={{ $product['id'] }}" data-page="1" @if($reviews['last_page'] == 1) data-load-more='-1' @endif>
    <ul class="clearfix product-reviews-list js-product-reviews-list">
        @include('product.block.review_list', ['review_list' => $reviews['data']])
    </ul>
    <div class="waiting-load-block js-load-block" style="display: none">
        <div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
    </div>
</div>
@endsection
@section('footer')
@endsection
@section('scripts')
<script type="text/javascript">
    //基础加载
    require(['zepto', 'base', 'mylayer', 'scrollComponent'], function ($, md_base, mylayer, scrollComponent) {
        var app = {};
        //公共事件
        app.init = function(){
            var self = this;
            var scroll_item = $(".js-product-reviews-list-box");
            //滚动加载
            scrollComponent.init();
            scrollComponent.setScrollItem(scroll_item);
            scrollComponent.setCallback(function(view, scroll_item){
                self.scrollLoadCallback(view, scroll_item);
            });
        };
        //滚动加载回调
        app.scrollLoadCallback = function(view, scrollitem){
            scrollitem.find('.js-product-reviews-list').append(view);
        };
        if(typeof app.init == 'function') {
            $(function () {
                app.init();
            });
        }
    }); 

</script>
@endsection

