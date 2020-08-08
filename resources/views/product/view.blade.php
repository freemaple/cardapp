@extends('layouts.app')
@section('title') {{ $goods_detail['name'] or '' }} @endsection

@section('styles')
<link href="//vjs.zencdn.net/5.19/video-js.min.css" rel="stylesheet">
@endsection

@section('meta')
<meta property="og:title" content="{{ $goods_detail['name'] or '' }}" />
<meta property="og:description" content="{{ $goods_detail['name'] or ''  }}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{ Helper::route('product_view', $goods_detail['id']) }}" />
<meta property="og:image" content="{{ $goods_detail['main_image'] or '' }}" />
<meta property="og:price:amount" content="{{ $goods_detail['sku_min_market_price'] or '' }}">
<meta property="og:price:currency" content="￥">
<meta property="og:site_name" content="人人有赏" />
@endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('home') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title" title="{{ $goods_detail['name'] }}">{{ str_limit($goods_detail['name'], 20)  }}</div>
        <div class="mobile-header-right share-header-right">
            @if($session_user['is_vip'] == '1')
            <span class="share-icon js-share-product" data-type="product" data-u="vip">
                <span class="iconfont icon-share1"></span>
                <div style="color: #0942fa">分享售卖</div>
            </span>
            @else
            <span class="share-icon js-share-product" data-type="product">
                <span class="iconfont icon-share1"></span>
                <div style="color: #0942fa">分享好货</div>
            </span>
            @endif
        </div>
    </div>
</div>
@endsection
@section('content')
    <div class="goods-box">
        <div class="goods-header-info">
            <div class="image">
                <img src="{{ $goods_detail['main_image'] }}" width="80" style="height: 80px;">
            </div>
            <div class="info-box" style="padding-bottom: 5px">
                <div class="goods-name"><span display: inline-block;vertical-align: middle;>{{ $goods_detail['name'] }}</span></div>
                <!--@if($goods_detail['is_self'])<img style="width: 66px;display: inline-block;vertical-align: middle;" src="{{ Helper::asset_url('/media/images/self.png') }}">@endif</div>-->
                <div class="price-info clearfix">
                    @if($goods_detail['sku_min_market_price'] > 0)
                    <span class="s_price">
                       ￥{{ $goods_detail['sku_min_market_price'] }}
                    </span>
                    @endif
                    <span class="price">
                       ￥{{ $goods_detail['sku_min_price'] }}
                    </span>
                    <div class="clearfix" style="margin-top: 4px">
                        @if(isset($goods_detail['max_share_integral']) && $goods_detail['max_share_integral'] > 0)
                        <span class="sp-btn share-sp-btn">自购/分享赚<span>￥{{ $goods_detail['share_amount_min']  }}~{{ $goods_detail['share_amount_max']  }}</span> 红包</span>
                        @endif
                        <span style="float: right;display: inline-block;">
                            <a class="share-icon js-product-codeimage"  data-id="{{ $goods_detail['id'] }}"><span class="operate-btn">分享扫码购</span></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding-top: 110px;"></div>
        @if(!empty($goods_detail['video']))
        <div>
            <video
            height='200'
            id="my-player"
            class="video-js"
            controls
            preload="auto"
            poster="{{ Helper::asset_url('/media/images/poster.png') }}" 
            style="width: 100%;"
            data-setup='{}'>
                <source src="{{ HelperImage::storagePath($goods_detail['video']) }}" type="video/mp4"></source>
                <p class="vjs-no-js">
                    对不起，您的浏览器不支持播放
                </p>
            </video>
        </div>
        @endif
        @if($goods_detail['description'] != '')
        <div class="goods-description">
            <div class="goods-description-desc">{{ $goods_detail['description'] }}</div>
        </div>
        @endif
        <div class="product-reviews-box">
        </div>
        <div class="goods-description" style="padding-top: 5px;">
            <ul class="">
                @if(!empty($goods_detail['images']))
                @foreach($goods_detail['images'] as $dgkey => $pimg)
                    @if($pimg['type'] == 'description')
                    <li><img data-src="{{ $pimg['image'] }}" ></li>
                    @endif
                @endforeach
                @endif
            </ul>
        </div>
    </div>
    <div class="store-product-box" style="display: none">
        <div style="position: relative;"> 
            <img src="/media/images/bcate.png?1552202160" style="width: 100%;position: absolute;width: 100%;
            position: absolute;top: -12px;max-height: 60px;z-index: 1">
            <div style="position: absolute;z-index: 2;text-align: center;width: 100%;top: 2px;color: #fff;font-size: 16px">本 店 精 选 产 品</div>
        </div>
        <ul class="clearfix product-list store-product-list js-store-product-list" style="padding: 35px 0px 5px 5px">
            
        </ul>
    </div>
    <div class="self-store-product-box" style="display: none">
        <div style="position: relative;"> 
            <img src="/media/images/bcate.png?1552202160" style="width: 100%;position: absolute;width: 100%;
            position: absolute;top: -12px;max-height: 60px;z-index: 1">
            <div style="position: absolute;z-index: 2;text-align: center;width: 100%;top: 2px;color: #fff;font-size: 16px">我的共享店铺</div>
        </div>
        <ul class="clearfix product-list  js-self-product-list" style="padding: 35px 0px 5px 5px">
            
        </ul>
    </div>
@endsection
@section('footer')
<div class="goods-footer">
    <ul class="clearfix">
        <li class="home" style="width: 15%">
            <a href="{{ $goods_detail['store_link'] }}">
                <span class="iconfont icon-dianpu"></span>
                <div>
                    <span class="text">进店</span>
                </div>
            </a>
        </li>
        <li class="home" style="width: 15%">
            <a href="tel:@if($goods_detail['is_self']){{ $goods_detail['service_phone'] or '' }} @else  {{ $goods_detail['store']['contact_phone'] or ''}} @endif">
                <span class="iconfont icon-phone"></span>
                <div>
                    <span class="text">客服</span>
                </div>
            </a>
        </li>
        <li class="home" style="width: 15%">
            <a href="javascript:void(0)" class="js-wish-product @if($is_wish) is-wish @endif" @if($is_wish) data-is-wish='1' @endif data-id="{{ isset($goods_detail['id']) ? $goods_detail['id'] : 0 }}">
                <span class="iconfont icon-wish wish-icon"></span>
                <div>
                    <span class="text">收藏</span>
                </div>
            </a>
        </li>
        @if(isset($goods_detail['max_share_integral']) && $goods_detail['max_share_integral'] > 0)
         <li style="width: 27%">
            <a href="javascript:void(0)" class="js-share-product" data-type="product" data-u="vip" style="background-color: #f00;color: #fff"></span><div><span class="text">分享赚</span>
            </div>
            <span>￥{{ $goods_detail['share_amount_min'] }}~{{ $goods_detail['share_amount_max'] }}</span>
            </a>
        </li>
        <li style="width: 28%">
            <a href="javascript:void(0)" class="js-btn-buy" data-type='2' style="background-color: #ff9800;color: #fff"></span><div><span class="text">自购赚</span>
            </div>
            <span>￥{{ $goods_detail['share_amount_min'] }}~{{ $goods_detail['share_amount_max'] }}</span>
            </a>
        </li>
        @else
        <li style="width: 55%">
            <a href="javascript:void(0)" class="js-btn-buy" data-type='2' style="background-color: #ff9800;color: #fff"></span><div><span class="text">立即购买</span></div>
            </a>
        </li>
        @endif
    </ul>
</div>
<script type="text/template" id="buy-form-template">
        <form class="goods_buy_form">
            <input type="hidden" name="goods_id" value="{{ $goods_detail['id'] }}" />
            <div class="buy-box-content">
                <div class="box sku-img-info">
                    <div class="img">
                        <img src="{{ $goods_detail['main_image'] }}" class="sku-image" />
                    </div>
                    <div class="info">
                        <div class="name">{{ $goods_detail['name'] or '' }}</div>
                        <div class="price">
                            <span class="sprice-price market_price" @if($goods_detail['sku_min_market_price'] <= 0) style="display: none" @endif>
                            <span class="s-price">￥<span class="sku-market-price-value">{{ $goods_detail['sku_min_market_price'] }}</span></span>
                            </span>
                            <span class="sku-price">
                               ￥<span class="sku-price-value">{{ $goods_detail['sku_min_price'] }}</span>
                            </span>
                            <div class="sku-stock" style=" @if(!empty($goods_detail['skus']) && count($goods_detail['skus']) == 1) display: block; @else display: none; @endif font-size: 14px;margin-top: 2px">
                               库存：<span class="sku-stock-value">
                                   @if(!empty($goods_detail['skus']) && count($goods_detail['skus']) == 1)
                                   {{ $goods_detail['skus'][0]['stock'] }}
                                   @endif
                               </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sku-box">
                    <div class="sku-attributes-list">
                        <div class="attributes">
                            @if(!empty($goods_detail['attribute']))
                            @foreach($goods_detail['attribute'] as $a => $attributes)
                            <div class="attributes-item" data-id="{{ $attributes['option_id'] }}">
                                <div class="title">{{ $attributes['option_description'] }}</div>
                                <ul class="value">
                                    @foreach($attributes['attributes'] as $vkey => $attribute_value)
                                    <li class="attributes-value-item" data-id="{{ $attribute_value['option_value_id'] }}" data-value="{{ $attribute_value['option_value'] }}">
                                        <span>{{ $attribute_value['option_value'] }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="qty-box">
                        <div class="clearfix">
                            <span class="text">数量</span>
                            <div class="input">
                                <span class="sku-number-reduce qty-reduce" data-type="decrease">-</span>
                                <input class="form-control qty-input" type="number" name="qty" value="1" min="1">
                                <span class="sku-number-reduce qty-reduce" data-type="increase">+</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="btn-box">
                   <a href="javascript:void(0)" class="btn btn-primary btn-block js-buy-confirm">确定</a>
                </div>
            </div>
        </form>
</script> 
<script type="text/template" id="code-image-template">
    <div style="text-align: center;">
        <img src="" class="codeImage" style="width: 400px;margin:10px auto;max-width: 95%" />
        @if(!Helper::isApp())
        @if(Helper::isWeixin())
        <p style="padding: 10px 0px">长按发送给朋友或者保存</p>
        @else
        <p style="padding: 10px 0px">长按保存</p>
        @endif
        @endif
    </div>
</script> 
<script type="text/template" id="product-share-box-template">
    <div class="share-list-box" style="padding: 20px;width: 100%">
        <ul class="share-list">
            <li class="share-item js-copy-link">
                <div>   
                    <span class="iconfont icon-lianjie" style="font-size: 36px;color: #00f"></span>
                </div>
                <div>复制链接</div>
            </li>
            <li class="share-item js-product-codeimage" data-id="{{ $goods_detail['id'] }}">
                <div>   
                    <span class="iconfont icon-qrcode" style="font-size: 36px;color: #00f"></span>
                </div>
                <div>分享二维码</div>
            </li>
        </ul>
    </div>
</script>
<input type="hidden" id="goods_id" name="goods_id" value="{{ $goods_detail['id'] or '' }}" data-name="{{ $goods_detail['goods_name'] or '' }}" />
<input type="hidden" id="sid" name="sid" value="{{ $sid or '' }}" />
@endsection
@section('scripts')
<script type="text/javascript">
    var goods_sku_list = {!! json_encode($goods_detail['skus']) !!};
</script>
<script type="text/javascript" src="{{ Helper::asset_url('/media/scripts/view/goods.js') }}"></script>
@endsection


