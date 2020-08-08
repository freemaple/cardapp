@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="{{ Helper::route('account_center') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('styles')
<style type="text/css">
	.pd-small {
		padding-bottom: 0px;
	}
    .bg-btn.hover {
    	background-color: #00f !important
    }
</style>
@endsection

@section('styles')
@section('content')
<div class="bg-f screen-box" style="background-color: #ff8800;">
	<form name="card-save-form" method="post" class="card-save-form" data-action="/api/card/saveInfo" onsubmit="return false">
		<div style="position: relative;">
			<a href="javascript:void(0)" class="bg-btn js-bg-left" style="position: absolute;background-color: #222;height: 34px;line-height: 34px;z-index: 2;top: 50%; left: 10px;width: 34px;text-align: center;"><span class="iconfont icon-left" style="color: #fff"></span></a>
			<div style="position: relative;height: 548px;text-align: center;margin: 0px auto;width: 260px;overflow: hidden;">
				
				<img src="{{ HelperImage::storagePath($backgrounds[0]['image']) }}" data-image="{{ $backgrounds[0]['image'] }}" class="card-background-image" width="260" />
				<div style="position: absolute;bottom: 120px;left: 160px;" class="product_front_design">
					<img src="{{ $card_qrcode }}" width="80" />
				</div>
			</div>
			<a href="javascript:void(0)" class="bg-btn js-bg-right" style="position: absolute;background-color: #222;height: 34px;line-height: 34px;z-index: 3;right: 0px;top:0px;top: 50%; right: 10px;width: 34px;text-align: center;"><span class="iconfont icon-right" style="color: #fff"></span></a>
		</div>
		<div class="text-center" style="padding: 5px 0px;font-size: 14px;color: #fff">二维码移动到自己喜欢的位置</div>
		<ul style="overflow: auto;height: 80px;width: 100%;position: fixed;bottom: 50px;display: none">
			@foreach($backgrounds as $bkey=> $bg)
			<li style="display: inline-block;" class="card-background-item" data-image='{{ $bg['image'] }}' data-src="{{ HelperImage::storagePath($bg['image']) }}">
				
			</li>
			@endforeach
		</ul>
		<input type="submit" class="btn btn-primary btn-block btn-submit" value="确定合成" style="display: none" />
	</form>
</div>
<div class="success-box" style="width: 100%;position: relative;display: none;min-height: 100%">
	<div style="width: 100%;background-color: #ff8800;color: #fff;padding: 35px 10px 10px 10px;">
		<div style="width: 260px;margin: auto">
			<img src='' class="screen-img" style="width: 100%;margin: auto"  />
		</div>
		<div style="width: 100%;text-align: center;padding: 10px 0px">长按图片保存到相册,进入相册设置图片成壁纸</div>
	</div>
	
</div>
@endsection
@section('footer')
	<div class="mobile-footer screen-box">
		<div class="btn-group text-center">  
	       <input type="button" class="btn btn-primary btn-block js-save-screen" value="确定合成" />
	    </div>
    </div>
@endsection
@section('copyright')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/screen.js') }}"></script>
@endsection




