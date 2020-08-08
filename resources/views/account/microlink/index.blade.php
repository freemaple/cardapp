@extends('layouts.app')

@section('header_title') 微链接 @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="{{ Helper::route('account_card_custom', $card['card_number']) }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">微链接</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="account-warp">
	<ul class="microlink-list">
        @foreach($microlinks as $ikey => $microlink)
		<li class="microlink-item js-edit-microlink" data-id="{{ $microlink['id'] }}" data-name="{{ $microlink['name'] }}" data-icon-id="{{ $microlink['icon_id'] }}">
			<div class="icon">
				{!! $microlink['svg'] !!}
			</div>
			<div class="name">{{ $microlink['name'] }}</div>
		</li>
        @endforeach
	</ul>
</div>
<script type="text/template" id="microlink-form-template">
        <div class="mobile-header clearfix">
            <div class="mobile-header-box clearfix">
                <div class="mobile-header-back">
                    <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
                </div>
                <div class="mobile-header-title">微链接</div>
            </div>
        </div>
        <div class="microlink-form-box">
            <form class="microlink-form clearfix" name="microlink-form" onsubmit="return false">
                <input type="hidden" class="form-control" name="id" value="" />
                <input type="hidden" class="form-control icon_id" name="icon_id"  value="" />
                <input type="hidden" name="card_id" class="card_id" value="{{ $card['id'] }}" />
                <div class="selectIcon">
                	<div class="selectIconbox js-show-select-icon">
                        <div class="current-icon-box"></div>   
                    </div>
                	<p class="text">选择图标</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="name">名称<span class="text-red">*</span></label>
                    <input type="text" class="form-control" required="required" name="name" placeholder="名称" maxlength="255" />
                </div>
                <div class="form-group">
                    <label class="form-label" for="name">链接<span class="text-red">*</span></label>
                    <input type="text" class="form-control" required="required" name="link" placeholder="链接" maxlength="255" />
                </div>
                <div class="layer-footer-box">
                    <div class="button-box"><input type="submit" class="btn btn-primary btn-block js-save-link" value="保存" /></div>
                </div>
            </form>
        </div>
</script>
<script type="text/template" id="icon-select-template">
        <div class="mobile-header clearfix">
            <div class="mobile-header-box clearfix">
                <div class="mobile-header-back">
                    <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
                </div>
                <div class="mobile-header-title">选择图标</div>
                <div class="mobile-header-right">
                    <a class="btn btn-info js-confirm-icon" style="height: 34px;line-height: 34px">确定</a>
                </div>
            </div>
        </div>
        <div class="microlink-icon-box">
            <ul class="icon-list">
            @foreach($icons as $ikey => $icon)
            <li data-id="{{ $icon['id'] }}" class="icon-item js-select-icon">
                <div class="icon-item-box">{!! $icon['svg'] !!}</div>
            </li>
            @endforeach
            </ul>
        </div>
</script>
@endsection

@section('footer')
<div class="mobile-footer">
	<div class="btn-group text-center">  
       <input type="button" class="btn btn-primary btn-block js-add-microlink" value="创建" />
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/microlink.js') }}"></script>
@endsection

