@extends('layouts.app')

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">微信设置</div>
    </div>
</div>
@endsection
@section('content')
<div class="account-setting-content">
	<form class="weixin-info-form" name="weixin-info-form" method="post" onsubmit="return false">
		<div style="background-color: #fff;padding: 10px;margin-top: 10px">
		    <div class="form-group">
				<div class="form-group-label">微信</div>
				<input type="text" class="form-control" name="weixin"  value="{{ $user['weixin'] }}" placeholder="请填写微信号">
				<a href="//wdy.soqi.cn/m/MTk0MjM5MzI=" data-i18n="[prepend]showHowGetCode"><loc-i18n>查看如何获得二维码</loc-i18n><i class="ic-pubfont ic-pub-question-solid"></i></a>
			</div>
			<div class="form-group">
				<div class="form-group-label">上传微信二维码</div>
				<div class="resultImgbox js-change-qt" style="position: relative;min-height: 200px;width: 100%;background-color: #eee;text-align: center;padding: 20px 0px">
					<span class="closebtn"><i class="ic-pubfont ic-close"></i></span>
					<img src="{{ HelperImage::storagePath($user['weixin_qr']) }}" class="qr-image" style="width: 280px" />
				</div>
			</div>
		</div>
		<div style="background-color: #fff;padding: 10px;margin-top: 10px">
			<div class="form-group">
				<div class="form-group-label">自定义名称</div>
				<input type="text" class="form-control" name="signature_title"  value="{{ $user['signature_title'] }}" placeholder="请填写自定义名称">
			</div>
			<div class="form-group">
				<div class="form-group-label">自定义内容</div>
				<input type="text" class="form-control" name="signature_content"  value="{{ $user['signature_content'] }}" placeholder="请填写自定义内容">
			</div>
			<div class="form-group">
				<div class="resultImgbox" style="position: relative;min-height: 200px;width: 100%;;text-align: center;">
					<span class="closebtn"><i class="ic-pubfont ic-close"></i></span>
					<img src="{{ $card_qrcode }}" />
					<div style="position: absolute;top: 50%;max-height: 60px;margin-top: -15px;z-index: 2;width: 60px;margin: -30px auto 0px -30px;left: 50%"><img src="{{ HelperImage::getavatar($user->avatar) }}" style="width: 60px" /></div>
				</div>
			</div>
		</div>
		<div class="btn-group text-center">  
            <input type="submit" class="btn btn-primary btn-block" value="保存" />
        </div>
	</form>
</div>
<form class="upload-form qr-upload-form" method="post" enctype="multipart/form-data">
    <input name="image" accept="image/*" type="file" class="upload-file qr-upload-file" />
</form>
@endsection
@section('footer')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/settings.js') }}"></script>
@endsection

