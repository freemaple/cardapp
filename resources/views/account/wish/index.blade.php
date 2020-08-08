@extends('layouts.app')

@section('title') 产品 @endsection

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">{{ $title }}</div>
        </div>
    </div>
@endsection

@section('content')
    <div class="product-list-box js-wish-scroll-container" data-action="/api/wish/list" data-page="1" @if($products['last_page'] <= 1) data-load-more='-1' @endif>
        <ul class="clearfix product-list js-product-list">
            @if(count($products['data']) > 0)
                @include("account.wish.block.list",['products' => $products['data']])
            @else
            <div class="no-results">
                <div class="result-img">@include('template.rote')</div>
                <div class="result-content">
                    <p>
                        您还没有喜欢的产品！<br />
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
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ Helper::asset_url('/media/scripts/view/account/wish.js') }}"></script>
@endsection
