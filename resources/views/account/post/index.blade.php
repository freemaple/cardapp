@extends('layouts.app')

@section('header_title') 文章管理 @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_center') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">文章管理</div>
	        <div class="mobile-header-right">
                <a class="btn btn-info js-confirm-icon" href="{{ Helper::route('account_post_add') }}" style="height: 34px;line-height: 34px">我要原创</a>
            </div>
	    </div>
	</div>
@endsection

@section('styles')
<style type="text/css">
	.form-box {
		width: 50%;
		display: inline-block;
		vertical-align: middle;
		margin-right: -4px;
	}
	.form-box select {
		width: 100%! important
	}
</style>
@endsection

@section('content')
<div class="pd-10" style="background-color: #ff9800">
	<form method="get" class="search-post-form">
		<div class="form-box" style="padding-right: 5px">
			<span>所属名片</span>
			<select class="search-select" name="card_id" style="display: inline-block;width: 80px">
				<option value="">全部</option>
				@foreach($cards as $key => $card)
					<option value="{{ $card['id'] }}" @if($form['card_id'] == $card['id']) selected="selected" @endif>{{ $card['name'] }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-box">
			<span>文章类型</span>
			<select class="search-select" name="type" style="display: inline-block;width: 80px">
				<option value="1" @if($form['type'] == '1') selected="selected" @endif>原创</option>
				<option value="2" @if($form['type'] == '2') selected="selected" @endif>转载</option>
			</select>
		</div>
		<div style="padding-right: 70px;position: relative;">
			<div style="padding: 15px 0px 0px 0px;display: inline-block;width: 100%">
				<input type="text" class="search-input" name="name" value="{{ $form['name'] }}" placeholder="请输入文章标题" style="width: 100%" />
			</div>
			<input type="submit" class="btn btn-default" value="搜索" style="height: 34px;line-height: 34px;position: absolute;right: 0px;top: 12px" />
		</div>
	</form>
</div>
<div class="account-warp">
	<div style="text-align: right;padding: 10px 0px">
		{{ $pager }}
	</div>
	@if(!empty($posts) && count($posts) > 0)
	<div class="js-post-list-box" data-page="1">
		<ul class="clearfix post-list js-post-list">
	        @include('account.post.block.list', ['posts' => $posts])
    	</ul>
    	<div class="waiting-load-block js-load-block" style="display: none">
    		<div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
		</div>
	</div>
	@else
		<div class="no-results">
		    <div class="result-img">@include('template.rote')</div>
		    <div class="result-content">
		        <p>此宝地空空如也，还待我王来挖掘！</p>
		    </div>
		</div>
	@endif
	<div style="text-align: center;padding: 10px 0px">
		{{ $pager }}
	</div>
</div>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/post.js') }}"></script>
@endsection

