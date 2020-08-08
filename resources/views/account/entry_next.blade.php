@extends('layouts.app')

@section('header_title') 完善个人信息 @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	        <div class="mobile-header-title">完善个人信息</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="entry_box clearfix">
	<form class="user_info_form" name="user_info_form" method="POST">
	    @if(empty($user['email']))
	    <div class="form-group">
            <div class="form-group-label">邮箱地址</div>
            <input type="text" class="form-control user_email" name="email" placeholder="请补充邮箱地址"  />
        </div>
        <div class="form-group">
            <div class="form-group-label">验证码</div>
            <input type="text" class="form-control verification_code"  placeholder="请补充验证码"  />
        </div>
	    @endif
	    <input type="hidden" id="is_opener" name="is_opener" value="{{ $is_opener or '' }}" />
	    <div class="form-group">
	        <input type="submit" class="btn btn-primary btn-block" value="继续" />
	    </div>
	</form>
</div>
@endsection
@section('scripts')
@endsection

