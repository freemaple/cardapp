@extends('layouts.app')

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">重置交易密码</div>
    </div>
</div>
@endsection
@section('content')
<div class="bg-f pd-10">
	<form class="alert-password-form" name="alert-tpwd-form" data-action="/api/account/changeTransactionPwd" onsubmit="return false">
		<div class="form-group">
	    	<div class="form-group-label">当前绑定手机号码</div>
	    	<input type="text" class="form-control reset_phone" readonly="readonly" value="{{ $user['phone'] }}" />
	    </div>
	    <div class="form-group">
            <div style="padding-right: 110px;position: relative">
                <input type="text" class="form-control" name="code" placeholder="验证码" />
                <a href="javascript:void(0)" class="btn btn-info js-send-phonecode" style="position: absolute;right: 0px;top: 0px">获取验证码</a>
            </div>
            <div class="code_send_tip_info" style="display: none">
                <span class="code_send_tip text-info"></span>
                <span>如若未收到，请<span class="verificate_code_time">60</span>秒后再发送</span>
            </div>
        </div>
	    <div class="form-group">
	       <div class="form-group_label">新密码</div>
	       <input type="password" class="form-control" required="required" maxlength="50" name="password"  />
	    </div>
	    <div class="form-group">
	       <div class="form-group_label">新密码确认</div>
	       <input type="password" class="form-control confirm_new_pwd" name="confirm_password"  />
	       <div class="error_msg confirm_password_error" style="display: none">密码不匹配</div>
	    </div>
	    <div class="form-group">
	    	<span style="color: #f00">每月仅限修改3次，吾皇务必牢记保管好国库钥匙！</span>
	    </div>
	    <div class="btn_group">  
	       <input type="submit" class="btn btn-primary btn-block" value="提交" />
	    </div>
	</form>
</div>
@endsection
@section('footer')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/settings.js') }}"></script>
@endsection

