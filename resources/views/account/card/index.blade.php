@extends('layouts.app')

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

@section('content')
<div class="account-warp">
	<div style="padding: 0px;">
		<div class="account-box-header clearfix" style="margin-top: 10px">
			我的个人名片
			<span style="position: absolute;right: 10px;top:10px"><a class="btn btn-default" href="{{ Helper::route('account_card_add') }}" style="height: 34px;line-height: 34px;background: #fe5430">创建新名片</a></span>
		</div>
		<ul class="clearfix card-list js-card-list">
	        @foreach($cards as $ckey => $card)
		    <li class="card-item @if($card['is_default']) selected @endif js-card-item js-card-item-{{ $card['id'] }} @if($card['is_default']) selected @endif clearfix" style="margin-top: 0px;border-bottom: 1px solid #eee">
	            <div class="card-item-content clearfix">
	            	<span class="checkedbox">
	            		<a href="javascript:void(0)" data-id="{{ $card['id'] }}" class="js-card-setdefault" data-confirm="确定设置此名片为默认名片?"><span class="checkbox">✓</span></a>
	            	</span>
	                <div class="infobox">
	                	<span class="name"><a href="{{ Helper::route('card_view', $card['card_number']) }}">{{ $card['name'] }}</a></span>
	                	<div class="info-block">
	                		<span>发布于 </span><span class="value">{{ $card['created_at'] }}</span>
	                		<div style="margin-top: 5px">
	                			<span>浏览数 </span> <span class="value" style="margin-right: 10px">{{ $card['view_number'] }}</span>
	                			<span class="text-info">{{ $card['enable'] == '1' ? '已启用' : '已禁用' }}</span>
	                		</div>
	                	</div>
	                	@if(!empty($card['syn_card']))
	                	<div style="margin-top: 5px;margin-bottom: 10px">当前同步名片：<span class="text-info">{{ $card['syn_card']['name'] }}</span></div>
	                	@endif
	                </div>
	                <div style="position: absolute; right: 0px;display: inline-block;vertical-align: middle;margin-right: 10px;top: 2px">
		                <img src="{{ $card['qr'] or '' }}" width="60" />
	                </div>
	            </div>
	            <div class="infobox-btn clearfix" style="text-align: right;margin: top: 5px;padding: 10px 0px 5px 0px;">
	            	<a href="{{ Helper::route('card_view', $card['card_number']) }}" class="operate-btn">浏览</a>
	            	<a class="js-card-enable operate-btn" data-id="{{ $card['id'] }}" data-enable="{{ $card['enable'] }}" data-confirm="@if($card['enable'] == '1')确定禁用此名片?@else确定启用此名片?@endif">@if($card['enable'] == '1')禁用@else启用@endif</a>
            		<a href="{{ Helper::route('account_card_custom', $card['card_number']) }}" class="operate-btn">编辑</a>
            		@if($card['syn_card_id'] > 0)
            		<a data-id="{{ $card['id'] }}" data-confirm="确定取消同步?" class="operate-btn js-syn-card-cancel">取消同步</a>
            		@endif
            		<a href="{{ Helper::route('account_post_index', ['card_id' => $card['id']]) }}" class="operate-btn">文章</a>
            	</div>
		    </li>
	        @endforeach
    	</ul>
	</div>
	<div style="margin-top: 40px;background: #fff">
		<div class="header" style="background: #03A9F4;padding: 10px;color: #fff;margin-top: 10px;position: relative;">
			可同步的名片列表
			<span style="position: absolute;right: 10px;top: 10px"><a class="btn btn-default js-card-contribute" style="height: 34px;line-height: 34px;background-color: #66e6f7">原创名片投稿</a></span>
		</div>
		<ul class="clearfix card-list js-card-list">
	        @foreach($sys_cards as $ckey => $s_card)
		    <li class="card-item @if($card['is_default']) selected @endif js-card-item js-card-item-{{ $card['id'] }} @if($card['is_default']) selected @endif clearfix" style="margin-top: 0px;border-bottom: 1px solid #eee">
	            <div class="card-item-content clearfix">
	                <div class="infobox">
	                	<span class="name">{{ $s_card['name'] }}</span>
	                	<div class="info-block">
	                		<span>发布于：</span><span class="value">{{ $s_card['created_at'] }}</span>
	                	</div>
	                </div>
	                <div style="position: absolute; right: 0px;display: inline-block;vertical-align: middle;margin-right: 10px;top: 2px">
		                <img src="{{ $card['qr'] or '' }}" width="80" />
	                </div>
	            </div>
	            <div class="infobox-btn clearfix">
	               <a href="{{ Helper::route('card_view', $s_card['card_number']) }}" class="operate-btn">浏览</a>
	               <a href="javascript:void(0)" data-id="{{ $s_card['id'] }}" class="operate-btn js-show-card-syn">同步</a>
	            </div>
		    </li>
	        @endforeach
    	</ul>
	</div>
	<div>
        <div style="text-align: center;padding: 40px 0px">
            <p class="self_help_qr" style="display: none;">
                <img src="{{ Helper::asset_url('/media/images/self_weiqin.jpg') }}" width="100" />
            </p>
            <p>
                <a style="color: #00f;" href="javascript:void(0)" class="js-show-help-qr">名片服务部</a>
            </p>
        </div>
    </div>
</div>
<script type="text/template" id="syn-card-template">
	<form class="syn_card_from">
		<div class="syn-box" style="padding: 10px;max-width: 95%;width: 360px">
		    <div class="form-group">
				<div class="form-group-label"><span class="text-red">*</span>选择您要同步的名片</div>
				<select name="card_id" class="form-control card_id">
					@foreach($cards as $ckey => $card)
					<option value="{{ $card['id'] }}">{{ $card['name'] }}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group">
				<span class="text-red">注：将同步该公司相关链接和相关文章</span>
			</div>
			<input type="hidden" class="syn_card_id" name="syn_card_id" value="">
			<div>
				<a href="javascript:void(0)" class="btn btn-primary btn-block js-syn-card-submit">确认同步</a>
			</div>
		</div>
	</form>
</script>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/card/list.js') }}"></script>
@endsection

