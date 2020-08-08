@extends('layouts.app')

@section('title') {{ $category['name'] or '' }} @endsection

@section('styles')
<style type="text/css">
    .site-cate-nav {
        height: auto !important;
        overflow-x: unset !important;
        overflow-y: unset !important;
    }
    .site-cate-nav-list {
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        height: 44px;
    }
    .show-sort-btn {
        position: absolute;
        right: 0px;
        top: 0px;
        padding: 11px 10px;
    }
    .site-cate-nav .sort-box{ 
        position: absolute;
        background: #fff;
        z-index: 10;
        left: 0px;
        top: 44px;
        width: 100%;
        visibility: hidden;
    }
    .site-cate-nav.sort .sort-box{ 
        visibility: visible;
    }
    .sort-box li {
        display: block !important;
        width: 100%;
        padding: 10px;
        background: #fff;
        border: 1px solid #eee;
    }
     .sort-box li.current {
        color: #f00;
     }
    .sort-layer {
        display: none;
        position: fixed;
        z-index: 2;
        margin: 0px;
        padding: 0px;
        border: none;
        width: 100%;
        height: 100%;
        position: fixed;
        top: 60px;
        left: 0px;
        display: none;
        background-color: #cccccc;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 1;
        -webkit-opacity: 1;
        -moz-opacity: 1;
        filter: alpha(opacity=80);
    }
</style>
@endsection

@section('meta')
    <meta property="og:title" content="{{ $category['name'] or '' }}" />
    <meta property="og:description" content="{{ $category['name'] or '' }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('category_view', $category['id']) }}" />
    <meta property="og:image" content="{{ $category['imgUrl'] or ''}}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />
@endsection

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="{{ Helper::route('home') }}" class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">{{ $category['name'] or '' }}</div>
            <div class="mobile-header-right">
                <span class="share-icon js-share-link"><span class="iconfont icon-share"></span></span>
            </div>
        </div>
    </div>
    @if(!empty($categorys))
    <div class="site-cate-fixed-box">
        <div class="site-cate-nav site-cate-fixed" style="padding-right: 36px">
            <ul class="site-cate-nav-list" style="overflow: auto;">
                <li class="cate-nav-item  @if($category['id'] == 'all') current @endif" data-id="all"><a href="{{ Helper::route('category_view', 'all') }}"><span>全部</span></a></li>
                @foreach($categorys as $pkey => $cate)
                <li class="cate-nav-item  @if($category['id'] == $cate['id']) current @endif" data-id="{{ $cate['id'] }}"><a href="{{ Helper::route('category_view', $cate['id']) }}"><span>{{ $cate['name'] }}</span></a></li>
                @endforeach
            </ul>
            <span class="show-sort-btn js-show-sort">
                <span class="iconfont icon-paixu" style="color: #444"></span>
            </span>
            <div  class="sort-box" style="position: absolute;background-color: #fff">
                <ul>
                     <li class="sort-product" data-sort="all">按综合排序</li>
                    <li class="sort-product" data-sort="sales_numbers">按销量排序</li>
                    <li class="sort-product" data-sort="created">按上架时间</li>
                    <li class="sort-product" data-sort="rating">按评分排序</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('content')
    <div class="product-list-box js-product-scroll-container" data-action="/api/category/products/{{ $category['id'] }}" data-page="1" @if(count($products) == 0) data-load-more='-1' @endif>
        <ul class="clearfix product-list js-product-list js-cate-product-list">
            @if(count($products) > 0)
                @include("shop.block.products",['products' => $products])
            @else
            <div class="no-results">
                <div class="result-img">@include('template.rote')</div>
                <div class="result-content">
                    <p>敬请期待，好产品即将上线</p>
                </div>
            </div>
            @endif
        </ul>
        <div class="waiting-load-block js-load-block" style="display: none">
            <div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
        </div>
    </div>
    <script type="text/template" id="goods-list-empty-template">
        <div class="no-results">
            <div class="result-img">@include('template.rote')</div>
            <div class="result-content">
                <p>{{ trans('view.site.no_find_any_results') }}</p>
            </div>
        </div>
    </script>
    <div class="sort-layer"></div>
@endsection

@section('footer')@endsection

@section('scripts')
    <script type="text/javascript" src="{{ Helper::asset_url('/media/scripts/view/shop.js') }}"></script>
@endsection
