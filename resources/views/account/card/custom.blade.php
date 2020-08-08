@extends('layouts.app')

@section('header_title') 名片: {{$card['name']}} @endsection

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

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="{{ Helper::route('account_card_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $card['name'] }}</div>
	    </div>
	</div>
@endsection

@section('content')
<form method="post" name="card-custom-form" class="card-custom-form">
	<div class="card-warp">
		@include($theme)
	</div>
	<input type="hidden" name="id" id="card_id" value="{{ $card['id'] }}" />
</form>
<div class="card-post-box">
    <div class="panel-title">文章</div>
    <div class="js-post-list-box" data-action="/api/card/post/{{ $card['id'] }}" data-page="1">
        <ul class="clearfix js-card-post-list">
        </ul>
        <div class="waiting-load-block js-load-block"  style="display: none">
            <div class="waiting-loading"></div>
            <div class="text">Loading...</div>
        </div>
    </div>
</div>
<form class="upload-form avatar-upload-form" method="post" enctype="multipart/form-data" action="/api/user/changeavatar">
	<input name="image" accept="image/*" type="file" class="upload-file avatar-upload-file" />
	<input type="hidden" name="_token" class="art_upload_form_token" value="{{ csrf_token() }}" />
</form>
<audio id="card-music-audio" src="@if(!empty($card_music['url'])){{ HelperImage::storagePath($card_music['url']) }}@endif" controls="controls" hidden preload></audio>
<input type="hidden" id="card_address" data-province="{{ $card['card_info']['province'] }}" data-city="{{ $card['card_info']['city'] }}" data-district="{{ $card['card_info']['district'] }}" data-address-street="{{ $card['card_info']['address_street'] }}">
@endsection
@section('footer')
	<div class="mobile-footer">
		<ul class="card-nav-info clearfix">
			<li><a href="javascript:void(0)" class="js-show-qr"><span class="iconfont icon-qrcode"></span><div style="padding-top: 2px"><span class="text">名片</span></div></a></li>
			<li><a href="javascript:void(0)" class="js-show-map-view"><span class="iconfont icon-daohang"></span><div style="padding-top: 2px"><span class="text">导航</span></div></a></li>
			<li style="display: none"><a href="javascript:void(0)"><span class="text">主题</span></a></li>
			<li><a href="{{ Helper::route('card_view', $card['card_number']) }}" class="btn btn-primary">预览</a></li>
		</ul>
	</div>
@endsection
@section('scripts')

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
                        @if($user['weixin'] != '')
                        <div style="text-align: center;padding: 10px">
                            微信号：{{ $user['weixin'] }}
                        </div>
                        @else
                        <div style="text-align: center;padding: 40px 10px">
                            用户未设置微信
                        </div>
                        @endif
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
                <img src="{{ HelperImage::storagePath('weixin/' .  $user['weixin_qr']) }}" />
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
<script src="{{ Helper::asset_url('/media/scripts/view/account/card/custom.js') }}"></script>
@endsection

