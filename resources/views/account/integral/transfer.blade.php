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
@section('content')
<div class="bg-f pd-10">
	<form name="integral-transfer-form" method="post" class="payout-apply-form" onsubmit="return false">
		@if(!empty($payer))
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>收款人姓名</div>
			<input  class="form-control" name="fullname" readonly="readonly" maxlength="50" value="{{ Helper::hideStar($payer['fullname'] . '(' . $payer['phone'] .')')  }}" />
		</div>
		<input type="hidden" name="payer" value="{{ $payer['id'] or '0' }}" />
		@else
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>收款人账户代码</div>
			<input  class="form-control payer_input" name="payer" value="" />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>收款人姓名(请核对)</div>
			<input  class="form-control payer_user" name="payer_user" readonly="readonly" maxlength="50" value="" />
		</div>
		@endif
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>转账积分（剩余 <span class="text-red">￥{{ $integral['point'] }}</span>）</div>
			<input  class="form-control" name="amount" maxlength="50" value="{{ $card['name'] or ''}}" />
			<p></p>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control" name="transaction_password" type="password" maxlength="50" value="" />
			<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		</div>
		<div>
			<input type="submit" class="btn btn-primary btn-block btn-submit" value="提交"  />
		</div>
	</form>
</div>
@endsection
@section('footer')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/integral.js') }}"></script>
@endsection

