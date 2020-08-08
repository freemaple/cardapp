@extends('layouts.app')

@section('title') {{ $doc['name'] }} @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">{{ $doc['name'] }}</div>
        <div class="mobile-header-right share-header-right">
            @if(!empty($doc))
            <span class="share-icon js-share-link"><span class="iconfont icon-share1"></span><div style="color: #0942fa">分享</div></span>
            @endif
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="help-box">
    <div>
        {!! $doc['description'] or '' !!}
    </div>
    @if(!empty($doc['video']))
    <div style="margin: 20px auto">
        <video
        height='600'
        id="my-player"
        class="video-js"
        controls
        preload="auto"
        poster="{{ Helper::asset_url('/media/images/poster.png') }}" 
        style="width: 100%;"
        data-setup='{}'>
            <source src="{{ HelperImage::storagePath($doc['video']) }}" type="video/mp4"></source>
            <p class="vjs-no-js">
                对不起，您的浏览器不支持播放
            </p>
        </video>
    </div>
    @endif
</div>
@endsection
