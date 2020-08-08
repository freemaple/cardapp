@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="/"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">{{ str_limit($title, 20) }}</div>
        </div>
    </div>
@endsection

@section('content')
    <div class="product-list-box js-product-scroll-container" data-action="/api/search/products?keyword={{$keyword}}" data-page="1" @if(count($products) == 0) data-load-more='-1' @endif>
        <ul class="clearfix product-list js-product-list">
            @if(count($products) > 0)
                @include("shop.block.products",['products' => $products])
            @else
            <div class="no-results">
                <div class="result-img">
                    @include('template.rote')
                </div>
                <p style="margin-top: 120px" class="result-content">
                    敬请期待，好产品即将上线<br />
                    <a href="{{ Helper::route('home') }}" class="u-link text-info">浏览其它</a>
                </p>
            </div>
            @endif
        </ul>
        <div class="waiting-load-block js-load-block" style="display: none">
            <div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ Helper::asset_url('/media/scripts/view/search.js') }}"></script>
@endsection
