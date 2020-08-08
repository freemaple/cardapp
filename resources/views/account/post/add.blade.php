@extends('layouts.app')

@section('header_title') 添加名片 @endsection

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
	<input type="hidden" name="save_type" value="0" />
	<div class="post-warp">
		<div class="form-group">
			<div class="form-group-label">标题</div>
			<input  class="form-control" name="name" maxlength="50" required="required" value="" />
		</div>
		<div class="form-group">
			<div class="form-group-label">标题背景色</div>
			<input type="text" value="" class="form-control background_color" style="background-color: {{ $post['background_color'] or '#ffffff'}}" readonly="readonly" />
			<input type="hidden" value="" class="form-control select_color_code" name="background_color" value="">
			<a href="javascript:void(0)" id="picker" style="display: none;">Show Color Picker</a>
		</div>
		<div class="form-group">
			<div class="form-group-label">所属名片</div>
			@foreach($cards as $ckey => $card)
			<input type="checkbox" value="$card['id']" @if($card['is_select'])) checked="checked" @endif name="card_id" /> {{ $card['name'] }}
			@endforeach
		</div>
		<div class="form-group">
			<div class="form-group-label">分类</div>
			<select name="category_id" class="form-control" required="required">
			<option value="">请选择</option>
			@foreach($categorys as $ckey => $category)
				<option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
			@endforeach
			</select>
		</div>
		<div class="form-group">
			<div class="form-group-label">
				<span>图片</span>
	        </div>
			<div class="image-list-box">
				<div class="post-image-item">
		        	<img class="img" data-name="{{ $post['image'] or '' }}" />
		        	<a class="iconfont icon-upload js-upload-image">
						<input name="image[]"  accept="image/*" type="file" class="upload-file upload-post-file" />
		        	</a>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label">文章内容</div>
			<textarea type="text" id="post-description" maxlength="50" class="form-control" name="description"  required="required" autocomplete="off" rows="20" ></textarea>
		</div>
		<div class="form-group">
			<p class="text-red">
				我王文采妙笔生花！分享出去必受百姓爱戴！
			</p>
			<p style="padding: 5px 0px">
				是否公开转载
				<input type="checkbox" value="1" name="public" checked="checked" />是
			</p>
		</div>
		<div class="form-group" style="display: none">
			<a class="post-music-edit" href="javascript:void(0)" data-music="{{ $post['music'] or ''}}">设置音乐</a>
			<input class="post_music" type="hidden" name="music" maxlength="50" value="{{ $post['music'] or ''}}" />
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

