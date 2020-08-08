@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
    <li><a href="{{ route('admin_banner') }}">banner管理</a></li>
    <li class="active">添加banner</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{ route('admin_banner') }}">banner信息</a></li>
        <li role="presentation"  class="active"><a href="{{ route('admin_banner_add') }}">添加banner</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" enctype="multipart/form-data" method="post" id="add_banner_form">
	        <div class="form-group">
	            <input type="hidden" id="" />
                <input type="hidden" name="location" value="home">
	        </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">图片</label>
                <div class="col-sm-9">
                  <input type="file" class="form-control"  name="image" required="required">
                  <p>图片格式为 jpg, png或gif</p>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">链接<span class="red">*</span></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="url" value="{{ $model['url'] or ''}}" maxlength="255">
                    <p>请输入完整链接地址，如：https://www.renrenyoushang.com</p>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">标题</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control"  name="alt" value="{{ $model['alt'] or ''}}" maxlength="255">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">排序</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="sort" value="{{ $model['sort'] or ''}}">
                </div>
            </div>
            <div class="form-group">
                <label for="enable" class="col-sm-2 control-label">启用</label>
                <div class="col-sm-9">
                  <select class="form-control" name="enable">
                    <option value="1" @if(isset($model['enable']) && $model['enable'] == '1') selected="selected" @endif>是</option>
                    <option value="0" @if(isset($model['enable']) && $model['enable'] == '1') selected="selected" @endif>否</option>
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
@endsection

