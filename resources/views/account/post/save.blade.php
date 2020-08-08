@extends('layouts.app')

@section('header_title') 名片: {{ $post['name'] or '' }} @endsection

@section('styles')
<link href="{{ Helper::asset_url('/media/styles/colpick.css') }}" rel="stylesheet">
<style type="text/css">
	.colpick_full {
		top: 80px !important;
	}
	.wangEditor-menu-container.fixed {
		position: fixed !important;
		top: 45px !important;
		z-index: 100000 !important;
	}
	.wangEditor-txt {
		caret-color: red;
		-webkit-caret-color: red;
	}
	.play .icon-bofang {
		color: #00f !important;
	}
</style>
@endsection

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
			<input  class="form-control" name="name" maxlength="50" value="{{ $post['name'] or ''}}" />
		</div>
		<div class="form-group">
			<div class="form-group-label">该文章要归属为？名片</div>
			@foreach($cards as $ckey => $card)
			<input type="checkbox" value="{{ $card['id'] }}" class="card_id" @if($card['is_select'])) checked="checked" @endif  /> {{ $card['name'] }}
			@endforeach
		</div>
		<div class="form-group">
			<div class="form-group-label">文章分类</div>
			<select name="category_id" class="form-control">
			<option value="">请选择</option>
			@foreach($categorys as $ckey => $category)
				<option value="{{ $category['id'] }}" @if(!empty($post) && $post['category_id'] == $category['id']) selected="selected" @endif>{{ $category['name'] }}</option>
			@endforeach
			</select>
		</div>
		<div class="form-group">
			<div class="form-group-label">
				<span>文章主图(宽高比2:1)</span>
	        </div>
			<div class="image-list-box">
				<div class="post-image-item">
		        	<img class="img" data-name="{{ $post['image'] or '' }}" src="@if(!empty($post['image'])){{ HelperImage::storagePath($post['image']) }}@endif"  />
		        	<a class="iconfont icon-upload js-upload-image">
						<input name="image[]"  accept="image/*" type="file" class="upload-file upload-post-file" />
		        	</a>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>视频(格式.mp4)</div>
			<input type="file" class="form-control" name="video" accept="*mp4" />
		</div>
		<div class="form-group text-red">
			温馨提示：禁止上传 淫秽、色情低俗、迷信、谣言、暴力、血腥恐怖、政治敏感、侵犯他人权益等国家法律禁止的图片、视频、链接，否则将依法关闭权限或账号。并负法律责任。
		</div>
		<div class="form-group post-description-box">
			<div class="form-group-label">文章内容</div>
			<textarea id="post-description" class="form-control" name="description" rows="18">{{ $post['description'] or ''}}</textarea>
		</div>
		<div class="form-group">
			<p class="text-red">
				我王文采妙笔生花！分享出去必受百姓爱戴！如允许转载，审核通过后，将在文库公开展示！
			</p>
			<p style="padding: 5px 0px">
			是否公开转载
				<input type="checkbox" value="1" name="public" @if(isset($post['public']) && $post['public'] == '1') checked="checked" @endif />是
			</p>
		</div>
		<div class="form-group">
			<div class="form-group-label">背景音乐</div>
			<input type="hidden" name="music_id" class="post_music" value="{{ $post['music_id'] or '' }}" />
			<input type="text"  class="post-music-edit form-control" readonly="readonly"  value="{{ $post_music['name'] or '' }}" />
		</div>
		<input type="submit" class="btn btn-primary btn-block btn-submit" value="保存" />
	</div>
	
</form>
<form id="editor_post_form" method="post" enctype="multipart/form-data" action="/common/uploadfile" style="display:none;">
    <input name="editor_upload_file" type="file" accept="image/*" class="editor_upload_post" id="editor_upload_post">
    <input name="name" value="editor_upload_file" id="editor_file_name" type="hidden">
</form>
@endsection
@section('scripts')
<script type="text/template" id="video-link-template">
    <div class="video-box" style="background-color: #f5f5f5;padding: 20px;width: 300px;">
    	<div class="form-group">
    		<div class="form-group-label">视频链接</div>
        	<input type="text" class="form-control video_link" />
        </div>
        <div class="form-group">
        	<input type="button" class="btn btn-primary btn-block js-add-video-link" value="确定" />
        </div>
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
<script type="text/javascript">
	window.onbeforeunload = function()
    { 
    	if(!window.post_save){
    		return "是否离开页面";
    	}
    }
</script>
<script src="{{ Helper::asset_url('/media/scripts/view/account/post.js') }}"></script>
@endsection

