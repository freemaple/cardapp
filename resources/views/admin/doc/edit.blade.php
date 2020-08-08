@extends('admin.template.layout')
@section('styles')
{!! App\Assets\Admin::style('admin/wangEditor/css/wangEditor.min.css') !!}
@endsection
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/doc">文档管理</a></li>
    <li class="active">添加文档</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation"><a href="/admin/doc">文档信息</a></li>
      <li role="presentation"  class="active"><a href="/admin/doc/add">添加文档</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal"  enctype="multipart/form-data" method="post">
            <div class="form-group">
                {!! csrf_field() !!}
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" maxlength="50"  autocomplete="off" placeholder="名称" required="required" value="{{ $form['name'] or '' }}">
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">描述</label>
                <div class="col-sm-9">
                    <textarea  class="form-control" id="post-description" name="description" rows="10"  placeholder="描述">{{ $form['description'] or '' }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">目录</label>
                <div class="col-sm-9">
                   <select name="catalog_id" class="form-control">
                       <option value="">请选择</option>
                       @foreach($doc_catalog as $dkey => $catalog)
                       <option value="{{ $catalog['id'] }}" @if(!empty($form['catalog_id']) && $form['catalog_id'] == $catalog['id']) selected="selected" @endif>{{ $catalog['name'] }}</option>
                       @endforeach
                   </select>
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">修改上传视频(格式.mp4)</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" name="video" accept="*mp4" />
                </div>
            </div>
            @if(!empty($form['video']))
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">当前视频</label>
                <div class="col-sm-9">
                    <video
                        id="my-player"
                        class="video-js"
                        height=200
                        controls
                        preload="auto"
                        poster="{{ Helper::asset_url('/media/images/poster.png') }}" 
                        style="width: 100%"
                        data-setup='{}'>
                            <source src="{{ HelperImage::storagePath($form['video']) }}" type="video/mp4"></source>
                            <p class="vjs-no-js">
                                To view this video please enable JavaScript, and consider upgrading to a
                                web browser that
                                <a href="http://videojs.com/html5-video-support/" target="_blank">
                                  supports HTML5 video
                                </a>
                            </p>
                    </video>
                </div>
            </div>
            @endif
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">网页标题</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="meta_title"  autocomplete="off" placeholder="网页标题"  value="{{ $form['meta_title'] or '' }}">
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">网页描述</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="meta_description" maxlength="255"  autocomplete="off" placeholder="网页描述"  value="{{ $form['meta_description'] or '' }}">
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">网页keyword</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="meta_keyword"  autocomplete="off" placeholder="网页keyword"  value="{{ $form['meta_keyword'] or '' }}">
                </div>
            </div>
             <div class="form-group">
                <label for="name" class="col-sm-2 control-label">url</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="url"  autocomplete="off" placeholder="url"  value="{{ $form['url'] or '' }}">
                </div>
            </div>
            <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">启用</label>
                <div class="col-sm-9">
                  <select class="form-control" name="enable">
                    <option value="1" @if(isset($form['enable']) && $form['enable'] == "1") selected="selected" @endif>是</option>
                    <option value="0" @if(isset($form['enable']) && $form['enable'] != "1") selected="selected" @endif>否</option>
                  </select>
                </div>
            </div>
            <div class="form-group">
                <div class="text-center">
                  <input type="submit" class="btn btn-primary" value="保存" />
                </div>
            </div>
        </form>
    </fieldset>
</div>
<form id="editor_post_form" method="post" enctype="multipart/form-data" action="/common/uploadfile" style="display:none;">
    <input name="editor_upload_file" type="file" accept="image/*" class="editor_upload_post" id="editor_upload_post">
    <input name="name" value="editor_upload_file" id="editor_file_name" type="hidden">
</form>
@endsection
@section('scripts')
{!! App\Assets\Admin::script('admin/wangEditor/js/wangEditor.min.js') !!}
{!! App\Assets\Admin::script('admin/scripts/module/admin.js') !!}
{!! App\Assets\Admin::script('admin/scripts/module/post.js') !!}
@endsection
