@extends('layouts.app')

@section('title') {{ $title }} @endsection

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
    <div class="product-list-box js-product-scroll-container" data-action="/api/selfProducts" data-page="1" @if($products['last_page'] == '1') data-load-more='-1' @endif>
        <ul class="clearfix product-list js-product-list">
            @if(count($products) > 0)
                @include("shop.block.products",['products' => $products['data']])
            @else
            <div class="no-results">
                <div class="result-img">
                    <img src="{{ Helper::asset_url('/media/images/em.gif') }}" width="100" />
                    <p>
                        敬请期待，好产品即将上线<br />
                        <a href="{{ Helper::route('home') }}" class="u-link text-info">浏览其它</a>
                    </p>
                </div>
            </div>
            @endif
        </ul>
        <div class="waiting-load-block js-load-block" style="display: none">
            <div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
        </div>
    </div>
    <div style="display: none">
        <div style="text-align: center;padding: 20px 0px">
            <p class="self_help_qr" style="display: none;">
                <img src="{{ Helper::asset_url('/media/images/self_weiqin.jpg') }}" width="100" />
            </p>
            <p>
                <a style="color: #00f" href="javascript:void(0)" class="js-show-help-qr">有赏自营专区产品部</a>
            </p>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ Helper::asset_url('/media/scripts/view/shop.js') }}"></script>
@endsection
