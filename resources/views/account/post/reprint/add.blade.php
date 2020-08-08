@extends('layouts.app')

@section('header_title') 名片: {{ $post['name'] or '' }} @endsection

<link href="{{ Helper::asset_url('/media/styles/colpick.css') }}" rel="stylesheet">

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="{{ Helper::route('account_post_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title or ''}}</div>
	    </div>
	</div>
@endsection

@section('content')

<form name="post-save-form" method="post" class="post-save-form" data-action="/api/post/saveInfo" onsubmit="return false">
	<input type="hidden" name="id" value="{{ $post['id'] or '0' }}" />
	<input type="hidden" name="save_type" value="{{ $save_type  }}" />
	<div class="post-warp">
		<div class="form-group">
			<div class="form-group-label">标题</div>
			<input  class="form-control" name="name" maxlength="50" required="required" value="{{ $post['name'] or ''}}" />
		</div>
		<input type="submit" class="btn btn-primary btn-block btn-submit" value="保存" />
	</div>
	
</form>
@endsection
@section('scripts')
<script type="text/template" id="edit-music-template">
	<div class="edit-music-box">
	    <div class="form-group">
			<div class="form-group-label">音乐地址</div>
			<input class="form-control music_link" name="music"  />
		</div>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block save-music">保存</a>
		</div>
	</div>
</script>
<script src="{{ Helper::asset_url('/media/scripts/view/account/post.js') }}"></script>
@endsection

