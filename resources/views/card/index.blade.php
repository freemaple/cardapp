@extends('layouts.app')

@section('styles')
<style type="text/css">
    #allmap {
        width: 768px;
        max-width: 100%;
        overflow-x: hidden;
        overflow: auto !important;
        min-height: 768px;
        background-color: #fff;
        z-index: 1000
    }
    .pop-bt-codebox {
        min-height: 200px;
    }
    .pop-bt-codebox-nav ul li.current {
        background-color: #fe7589;
        color: #fff;
    }
    .pop-bt-codebox-nav ul li {
        font-size: 14px;
        color: #1f8ff3;
        text-align: center;
        height: 34px;
        line-height: 34px;
        border: 1px solid #eeeeee;
        width: 50%;
        float: left;
    }
    .qr-box img {
        width: 100%
    }
    .qr-tab-content {
        padding: 20px 0px;
        width: 280px;
        margin: 0px auto;
        min-height: 280px
    }
</style>
@endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ isset($card['name']) ? $card['name'] : '' }}</div>
            <div class="mobile-header-right share-header-right">
                <span class="share-icon js-share-link"><span class="iconfont icon-share1"></span><div style="color: #0942fa">分享</div></span>
            </div>
	    </div>
	</div>
@endsection

@section('content')
<div class="card-warp" style="position: relative;">
	@include($theme)
</div>
<div style="display: none" class="card-post-box">
    <div class="panel-title" style="">文章</div>
    <div class="js-post-list-box" data-action="/api/card/post/{{ $card['id'] }}" data-page="1">
        <ul class="clearfix js-card-post-list">
        </ul>
        <div class="waiting-load-block js-load-block"  style="display: none">
            <div class="waiting-loading"></div>
            <div class="text">Loading...</div>
        </div>
    </div>
</div>
<audio id="card-music-audio" src="{{ !empty($card['card_music']['url']) ? HelperImage::storagePath($card['card_music']['url']) : '' }}" loop="loop" controls="controls" hidden preload autoplay="autoplay"></audio>
<input type="hidden" id="card_id" value="{{ $card['id'] }}">
<input type="hidden" id="card_address" data-province="{{ $card['card_info']['province'] }}" data-city="{{ $card['card_info']['city'] }}" data-district="{{ $card['card_info']['district'] }}" data-address-street="{{ $card['card_info']['address_street'] }}">
@endsection

@section('footer')
    <div class="mobile-footer site_footer">
        <ul class="card-nav-info clearfix">
            <li><a href="javascript:void(0)" class="js-show-qr"><span class="iconfont icon-qrcode"></span><div style="padding-top: 2px"><span class="text">名片</span></div></a></li>
            <li><a href="javascript:void(0)" class="js-show-map"><span class="iconfont icon-daohang"></span><div style="padding-top: 2px"><span class="text">导航</span></div></a></li>
            @if(!empty($session_user) && $card['user_id'] == $session_user->id )
            <li><a href="{{ Helper::route('account_card_edit', [$card['card_number']]) }}"><span class="iconfont icon-edit"></span><div style="padding-top: 2px"><span class="text">编辑</span></div></a></li>
            @else
                @if(!empty($session_user))
                <li><a href="{{ Helper::route('account_card_index') }}"><span class="iconfont icon-add"></span><div style="padding-top: 2px"><span class="text">我也要创建</span></div></a></li>
                @else
                <li><a href="{{ Helper::route('auth_login', ['register', 'rid' => $rid]) }}"><span class="iconfont icon-add"></span><div style="padding-top: 2px"><span class="text">我也要创建</span></div></a></li>
                @endif
            @endif
        </ul>
    </div>
@endsection

@section('scripts')
<script type="text/template" id="map-template">
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">地图</div>
        </div>
    </div>
    <div id="allmap"></div>
</script>
<script type="text/template" id="qr-box-template">
    <div class="qr-box pop-bt-codebox">
        <div class="box-tab">
            <div class="pop-bt-codebox-nav">
                <ul class="clearfix">
                    <li class="tab-item current">存名片</li> 
                    <li class="tab-item">加微信</li>
                </ul>
            </div>
            <div class="tab-content qr-tab-content">
                <div class="current qr-box">
                    <img src="{{ $card_qrcode }}" />
                </div>
                <div>
                    @if($user['weixin_qr'] != '')
                    <img src="{{ HelperImage::storagePath($user['weixin_qr']) }}" />
                    @else
                        <div style="text-align: center;padding: 40px 10px">
                            用户未设置微信
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/template" id="weixin-box-template">
    <div class="qr-box pop-bt-codebox">
       <div>
            @if($user['weixin_qr'] != '')
                <img src="{{ HelperImage::storagePath($user['weixin_qr']) }}" />
            @else
                @if($user['weixin'] != '')
                <div style="text-align: center;padding: 40px 10px">
                    微信号：{{ $user['weixin']  }}
                </div>
                @endif
            @endif
        </div>
    </div>
</script>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=9da280703fc98372b55dc44dda8e7fad"></script>
<script src="{{ Helper::asset_url('/media/scripts/view/card.js') }}"></script>
@endsection

