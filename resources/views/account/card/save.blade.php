@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

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
	.resultImgbox {
		position: relative;
		min-height: 200px;
		width: 100%;
		background-color: #eee;
		text-align: center;
		padding: 20px 0px;
	}
	.play .icon-bofang {
		color: #00f !important;
	}
</style>

<form name="card-save-form" method="post" class="card-save-form" data-action="/api/card/saveInfo" @if(!empty($card)) data-custom-link="{{ Helper::route('account_card_custom', $card['card_number']) }}" @endif onsubmit="return false">
	<input type="hidden" name="id" value="{{ $card['id'] or '0' }}" />
	<input type="hidden" name="save_type" value="{{ $save_type  }}" />
	<div class="account-warp">
		<div class="account-setting-content">
			<div class="box-tab">
		        <div class="tab-content">
		      		<div class="tab-content-item account-setting-panel current">
		      			<div class="block-panel">
		      				<div class="block-panel-content"> 
		      					<div class="setting-content-box">
		      						<div class="form-group">
										<div class="form-group-label"><span class="text-red">*</span>设置名片名称</div>
										<input  class="form-control" name="name" maxlength="50" value="{{ $card['name'] or ''}}" />
									</div>
									<div class="form-group">
										<div class="form-group-label">公司</div>
										<input type="text" maxlength="50" class="form-control" name="organization" maxlength="50" value="{{ $card_info['organization'] or ''}}"  />
									</div>
									<div class="form-group-list clearfix">
										<div class="form-group">
											<div class="form-group-label">部门</div>
											<input type="text" maxlength="50" class="form-control" name="department" maxlength="50" value="{{ $card_info['department'] or ''}}" />
										</div>
										<div class="form-group">
											<div class="form-group-label">职位</div>
											<input type="text" maxlength="50" class="form-control" name="position" maxlength="50" value="{{ $card_info['position'] or ''}}" />
										</div>
									</div>
									<div class="form-group">
										<div class="form-group-label"><span class="text-red">*</span>姓名</div>
										<input type="text" maxlength="8" class="form-control" name="fullname" value="{{ $user['fullname'] or ''}}" required="required" />
									</div>
									<div class="form-group">
										<div class="form-group-label">名片背景</div>
										<div class="resultImgbox js-show-select-background">
											<span class="closebtn"><i class="ic-pubfont ic-close"></i></span>
											<img src="@if(!empty($card['background_image'])) {{ HelperImage::storagePath($card['background_image']) }} @endif" class="card-background_img" style="width: 280px" />
										</div>
									</div>
									<div class="form-group">
										<div class="form-group-label">上传微信二维码</div>
										<div class="resultImgbox js-change-qt">
											<span class="closebtn"><i class="ic-pubfont ic-close"></i></span>
											<img src="{{ !empty($user['weixin_qr']) ? HelperImage::storagePath($user['weixin_qr']) : '' }}" class="qr-image" style="width: 280px" />
										</div>
									</div>
									<input type="hidden" name="background_image" class="background_image" value="{{ $card['background_image'] or '' }}" />
									<div class="form-group">
										<div class="form-group-label">导航位置 <a class="text-info js-show-map" href="javascript:void(0)">导航</a></div>
										<div class="form-group">
											<div data-toggle="distpicker" class="distpicker clearfix" id="distpicker1" data-province="{{ $card_info['province'] or ''}}" data-city="{{ $card_info['city'] or ''}}" data-district="{{ $card_info['district'] or ''}}">
												<select class="form-control province" id="province"  name="province"></select>
												<select class="form-control city" id="city" name="city"></select>
												<select class="form-control district" id="district" name="district"></select>
											</div>
										</div>
										<input type="text" maxlength="50" class="form-control address_street" name="address_street" maxlength="50" value="{{ $card_info['address_street'] or ''}}" placeholder="详细地址" />
									</div>
									<div class="form-group">
										<div class="form-group-label">背景音乐</div>
										<input type="hidden" name="music_id" class="card_music" value="{{ $card['music_id'] or '' }}" />
										<input type="text"  class="card-music-edit form-control" readonly="readonly"  value="{{ $card_music['name'] or '' }}" />
									</div>
									
		                        </div>
		      				</div>
		      			</div>
		      		</div>
		         </div>
		   	</div>
		</div>
	</div>
	<input type="submit" class="btn btn-primary btn-block btn-submit" value="保存" style="display: none" />
</form>
<form class="upload-form qr-upload-form" method="post" enctype="multipart/form-data">
    <input name="image" accept="image/*" type="file" class="upload-file qr-upload-file" />
</form>
<script type="text/template" id="background-select-template">
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">选择背景</div>
            <div class="mobile-header-right">
                <a class="btn btn-info js-confirm-icon" style="height: 34px;line-height: 34px">确定</a>
            </div>
        </div>
    </div>
    <div class="background-box">
        <ul class="background-list clearfix">
        @foreach($backgrounds as $bkey => $background)
        <li data-id="{{ $background['id'] }}" class="background-item js-background-item" data-src="{{ HelperImage::storagePath($background['image']) }}" data-image="{{ $background['image'] }}">
            <a><div class="img"><img src="{{ HelperImage::storagePath($background['image']) }}" /></div></a>
        </li>
        @endforeach
        </ul>
    </div>
</script>
@endsection
@section('footer')
	<div class="mobile-footer">
		<div class="btn-group text-center">  
	       <input type="button" class="btn btn-primary btn-block js-save-card" value="保存" />
	    </div>
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
            <div class="mobile-header-right">
                <a class="btn btn-info js-confirm-icon" style="height: 34px;line-height: 34px">确定</a>
            </div>
        </div>
    </div>
    <div id="allmap"></div>
</script>
<script type="text/template" id="map-template">
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">相册</div>
        </div>
    </div>
    <div id="card_album_list">
    	
    </div>
</script>
<script type="text/template" id="edit-music-template">
	<div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">音乐选择</div>
        </div>
    </div>
	<div class="edit-music-box">
		<ul>
			@foreach($musics as $mkey => $mc)
			<li class="list-item music-list-item clearfix" data-id="{{ $mc['id'] }}" data-name="{{ $mc['name'] }}" style="padding: 0px 10px">
				<span class="name">{{ $mc['name'] }}</span>
				<span class="checkedbox" style="float: right;">
            		<a href="javascript:void(0)" data-music="{{ $mc['id'] }}" class="select-music-item"><span class="checkbox">✓</span></a>
            	</span>
				<span style="float: right;margin-right: 20px">
					<a href="javascript:void(0)" class="js-play-music" data-id="{{ $mc['id'] }}" data-music="{{ HelperImage::storagePath($mc['url']) }}"><span class="iconfont icon-bofang" style="color: #ff9800;font-size: 20px"></span>
					<audio id="music-audio-{{ $mc['id'] }}" class="music-audio" data-src="{{ HelperImage::storagePath($mc['url']) }}" controls="controls" hidden preload></audio>
					</a>
				</span>
			</li>
			@endforeach
		</ul>
		<div style="margin-top: 5px">
			<a href="javascript:void(0)" class="btn btn-primary btn-block save-music">确定</a>
		</div>
	</div>
</script>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=9da280703fc98372b55dc44dda8e7fad"></script>
<script src="{{ Helper::asset_url('/media/scripts/view/account/card/card.js') }}"></script>
@endsection

