@extends('layouts.app')
@section('header_title') {{ $title }} @endsection
@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection
@section('styles')
<style type="text/css">
	.form-group {
		margin-bottom: 5px;
	}
</style>
@endsection
@section('content')
<div class="bg-f pd-10">
	<form name="payout-apply-form" method="post" class="payout-apply-form" onsubmit="return false">
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>真实姓名</div>
			<input  class="form-control" name="fullname" maxlength="50" value="" />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>金额</div>
			<input  class="form-control" name="amount" maxlength="50" value="" />
			<div class="text-red">
				一天仅限提现一次，单笔限额5000, 每笔提现手续费2元，预计1-3工作日左右到账。
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>支付宝账户</div>
			<input  class="form-control" name="alipay" maxlength="255" value="" />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control" name="transaction_password" type="password" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div class="form-group">
	    	<div class="form-group_label">当前绑定手机号码</div>
	    	<input type="text" class="form-control user_phone" readonly="readonly" value="{{ $user['phone'] }}" />
	    </div>
		<div class="form-group">
	        <div style="padding-right: 110px;position: relative">
	            <input type="text" class="form-control" name="code" placeholder="验证码" />
	            <a href="javascript:void(0)" class="btn btn-success js-send-phonecode" style="position: absolute;right: 0px;top: 0px">获取验证码</a>
	        </div>
	        <div class="code_send_tip_info" style="display: none">
	            <span class="code_send_tip text-info"></span>
	            <span>如若未收到，请<span class="verificate_code_time">60</span>秒后再发送</span>
	        </div>
	    </div>
		<div>
			<input type="submit" class="btn btn-primary btn-block btn-submit" value="提交"  />
		</div>
	</form>
</div>
@endsection
@section('footer')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/payout.js') }}"></script>
@endsection

