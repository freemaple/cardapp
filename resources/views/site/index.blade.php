@extends('layouts.app')

@section('styles')
<style type="text/css">
    body {
        background: #f5f5f5;
    }
    .swiper-container {
        background: #ffffff;
    }
    .layer-homescreen-box .layerbox-wrapper {
        max-width: 90%;
        width: 440px;
        margin: auto;
    }
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .tipHeader {
        font-size: 0.3rem;
        color: #fe5430;
        padding: 0px 0px 10px 0px;
    }
    .home-nav-box {
        padding: 0px 5px 10px 5px;
        background: #fff;
    }
    .site-header-box {
        padding: 0px 44px 0px 44px;
        background: #ffffff;
    }
    .mlogo {
        position: absolute;
        width: 44px;
        text-align: center;
        height: 49px;
        line-height: 49px;
        z-index: 1;
        left: 0px;
        top: 0px;
    }
    .mlogo img {
        width: 30px;
        margin-top: 8px;
    }
    .mmenu {
        position: absolute;
        width: 44px;
        text-align: center;
        height: 49px;
        line-height: 49px;
        z-index: 1;
        right: 0px;
        top: 0px;
    }
    .mmenu a {
        display: block;
        color: #999999;
    }
    .icon-menu {
        font-size: 24px;
        color: #999999;
    }
    .search-box {
        background: #eeeeee;
        border: none;
    }
    .search-box input {
        background: #eeeeee;
        border: none;
    }
   .home-nav-item {
        display: -webkit-flex;
        display: flex;
        -webkit-flex-direction: column;
        flex-direction: column;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-align-items: center;
        align-items: center;
        width: 1.94667rem;
        padding-top: 10px;
        float: left;
        width: 20%
    }
    .home-nav-item a {
        display: block;
        text-align: center;
    }
    .home-nav-item img {
        width: 0.75rem;
        height: 0.75rem;
    }
    .home-nav-item span {
        display: block;
        line-height: .45333rem;
        margin-top: .13333rem;
        font-size: .242rem;
        color: #666;
    }
</style>
@endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <span class="mlogo"><img class="lazyload" data-img="/media/images/logo.png" /></span>
        <div class="site-header-box clearfix">
            <form action="{{ Helper::route('search') }}">
                <div class="search-box">
                    <span class="mark">
                        <i class="iconfont icon-search"></i>
                    </span>
                    <input type="text" class="flex pub-input" name="keyword" placeholder="请输入商品名称搜索" />
                </div>
            </form>
        </div>
        <span class="mmenu"><a href="{{ Helper::route('category_view', 'all') }}"><span class="iconfont icon-menu"></span></a></span>
        <img class="lazyload" data-img="/media/images/h.png" style="width: 100%" />
    </div>
</div>
@endsection

@section('content')

@if(!empty($banners))
<div class="swiper-container">
    <ul class="swiper-wrapper">
        @foreach($banners as $bkey => $b)
        <li class="swiper-slide"><a title="{{ $b['alt'] or '' }}" href="{{ $b['url'] or '' }}"><img class="banner-image" src="{{ $b['image'] }}" alt="{{ $b['alt'] or '' }}"></a></li>
        @endforeach
    </ul>
</div>
@endif

<div class="home-nav-box">
    <div class="home-nav-list clearfix">
        <div class="home-nav-item">
            <a href="{{ Helper::route('article') }}">
                <img src="{{ Helper::asset_url('/media/images/icon/art.png') }}">
                <span>文库</span>
            </a>
        </div>
       <div class="home-nav-item">
            <a href="{{ Helper::route('category_view', 'all') }}">
                <img src="{{ Helper::asset_url('/media/images/icon/cate1.png') }}">
                <span>分类</span>
            </a>
        </div>
        @if(!empty($session_user))
        <div class="home-nav-item">
            <a href="{{ Helper::route('account_vipUpgrade') }}">
                <img src="{{ Helper::asset_url('/media/images/icon/gift.png') }}">
                <span>金麦大礼包</span>
            </a>
        </div>
        @else
        <div class="home-nav-item">
            <a href="{{ Helper::route('shop') }}">
                <img src="{{ Helper::asset_url('/media/images/icon/shop.png') }}">
                <span>共享共推</span>
            </a>
        </div>
        @endif
        <div class="home-nav-item">
            <a href="{{ Helper::route('help_school') }}">
                <img src="{{ Helper::asset_url('/media/images/icon/sch.png') }}">
                <span>商学院</span>
            </a>
        </div>
        <div class="home-nav-item">
            <a href="{{ Helper::route('merchant') }}">
                <img src="{{ Helper::asset_url('/media/images/icon/mer.png') }}">
                <span>同城商家</span>
            </a>
        </div>
    </div>
</div>
@if(!empty($products))
<a href="{{ Helper::route('product_view', $products[0]['id']) }}">
    <div style="width: 100%;height: 2.82rem;position: relative;overflow-y:hidden;">
        <img src="/media/images/share.png?v=1" style="width: 100%" />
        <div style="position: absolute;left: 70%;width: 40%;height: 1.64rem;top: 50%;transform: translateY(-50%);">
            <img src="{{ $products[0]['image'] }}" style="height: 100%; width: auto;margin: auto" />
        </div>
    </div>
 </a>
@endif
@if(!empty($session_user) && $session_user->is_vip != '1')
<div>
    <a href="{{ Helper::route('account_vipUpgrade') }}">
        <img src="/media/images/vip_gift.jpg" style="width: 100%">
    </a>
</div>
@endif
<div class="site-pro site-viewd-box" style="display: none">
    <div class="site-pro-header">
        我的足迹
        <a style="display: none" class="right-btn more-link" href="{{ Helper::route('viewdhistory') }}">更多</a>
    </div>
    <ul class="site-pro-list viewd_goods_list clearfix">
        
    </ul>
</div>
@if(count($products) > 0)
<div class="site-pro">
    <div class="site-pro-header">
        我的共享店铺
        <a class="right-btn" href="{{ Helper::route('shop') }}">全部</a>
    </div>
    <ul class="product-list clearfix">
        @include("shop.block.products",['products' => $products])
    </ul>
    <div style="padding: 10px 10px 30px 10px;text-align: center;background: #fff">
        <a class="" style="color: #fff;padding: 5px 10px;border-radius: 25px;display:block;background: #e7567b" href="{{ Helper::route('shop') }}">查看全部>></a>
    </div>
</div>
@endif

<div style="position: relative;"> 
    <img class="lazyload" data-img="{{ Helper::asset_url('/media/images/bcate.png') }}" style="width: 100%;position: absolute;width: 100%;
    position: absolute;top: -12px;max-height: 60px;z-index: 2" />
    <div class="site-cate-nav home-cate-nav">
        <ul>
            <li><a  href="{{ Helper::route('shop') }}"><span>共享专区</span></a></li>
            @if(!empty($categorys) && count($categorys) > 0)
            @foreach($categorys as $ckey => $category)
            <li><a  href="{{ Helper::route('category_view', $category['id']) }}"><span>{{ $category['name'] }}</span></a></li>
            @endforeach
            @endif
        </ul>
    </div>
    <div class="js-product-scroll-container" data-action="/api/products" style="position: relative;top: -21px;" data-page="1" @if($store_products['last_page'] == 1) data-load-more='-1' @endif>
        <ul class="clearfix product-list home-product-list js-product-list" style="padding: 0px 0px 5px 5px">
            @include("shop.block.products",['products' => $store_products['data']])
        </ul>
        <div class="waiting-load-block js-load-block" style="display: none">
            <div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
        </div>
    </div>
</div>
<input type="hidden" id="homescreen-template" value="1">
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ Helper::asset_url('/media/scripts/view/shop.js') }}"></script>
    <script type="text/javascript">
        //基础加载
        require(['zepto', 'base', 'mylayer', 'scrollComponent', 'lazyload'], function ($, md_base) {
            var ids = window.localStorage.getItem('viewd_goods_id');
            if(!ids){
                ids = [];
            } else {
                ids = JSON.parse(ids);
            }
            if(ids.length > 0){
                var is_more = ids.length > 4 ? true : false;
                ids = ids.slice(0, 5);
                $.ajaxGet('/api/getViewdProducts', {
                    goods_ids: ids.join(',')
                }, function(rst){
                    if(rst.view){
                        $(".site-viewd-box").show();
                        $(".viewd_goods_list").html(rst.view);
                        if(is_more){
                            $(".site-viewd-box .more-link").show();
                        } else {
                             $(".site-viewd-box .more-link").hide();
                        }
                    }
                })
            }
        });
    </script>
@endsection


@section('copyright', view('template.copyright'))
