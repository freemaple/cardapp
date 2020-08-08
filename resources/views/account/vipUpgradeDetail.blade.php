@extends('layouts.app')
@section('title') {{ $goods_detail['name'] or '' }} @endsection

@section('styles')
<link href="//vjs.zencdn.net/5.19/video-js.min.css" rel="stylesheet">
@endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title" title="{{ $goods_detail['name'] }}">{{ str_limit($goods_detail['name'], 20)  }}</div>
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
                <div class="price-info clearfix">
                    @if($gift['market_price'] > 0)
                    <span class="s_price">
                       ￥{{ $gift['market_price'] }}
                    </span>
                    @endif
                    <span class="price">
                       ￥{{ $gift['price'] }}
                    </span>
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
        <div class="goods-description" style="padding-top: 5px;">
            <ul class="">
                @if(!empty($goods_detail['images']))
                @foreach($goods_detail['images'] as $dgkey => $pimg)
                    @if($pimg['type'] == 'description')
                    <li><img src="{{ $pimg['image'] }}" ></li>
                    @endif
                @endforeach
                @endif
            </ul>
        </div>
    </div>
@endsection
@section('footer')
<div class="goods-footer">
    <ul class="clearfix">
        <li style="width: 100%">
            <a data-href="{{ $checkoutUrl }}" class="js-btn-buy" style="background-color: #f00;color: #fff"></span><div><span class="text">立即购买</span></div>
            </a>
        </li>
    </ul>
</div>
<input type="hidden" id="goods_id" name="goods_id" value="{{ $goods_detail['id'] or '' }}" data-name="{{ $goods_detail['goods_name'] or '' }}" />
<input type="hidden" id="checkoutUrl" name="checkoutUrl" value="{{ $checkoutUrl or '' }}"  />
@endsection
@section('scripts')
<script type="text/javascript">
    var goods_sku_list = {!! json_encode($goods_detail['skus']) !!};
</script>
<script type="text/template" id="buy-form-template">
    <form class="goods_buy_form">
        <div class="buy-box-content">
            <div class="box sku-img-info">
                <div class="img">
                    <img src="{{ $goods_detail['main_image'] }}" class="sku-image" />
                </div>
                <div class="info">
                    <div class="name">{{ $goods_detail['name'] or '' }}</div>
                    <div class="price">
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
            </div>
            <div class="qty-box">
            </div>
            <div class="btn-box">
               <a href="javascript:void(0)" class="btn btn-primary btn-block js-buy-confirm">确定</a>
            </div>
        </div>
    </form>
</script> 
<script type="text/javascript" src="{{ Helper::asset_url('/media/scripts/view/gift.js') }}"></script>
@endsection


