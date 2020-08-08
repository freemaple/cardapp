@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
    <li><a href="{{ route('admin_banner_location') }}">banner位置</a></li>
    <li class="active">添加banner位置</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{ route('admin_banner_location') }}">banner位置</a></li>
        <li role="presentation"  class="active"><a href="{{ route('admin_banner_location_add') }}">添加banner位置</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" enctype="multipart/form-data" method="post" id="add_banner_form">
	        <div class="form-group">
	            <input type="hidden" id="" />
                {!! csrf_field() !!}
	        </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">位置名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control"  name="location" required="required" value="{{ $model['location'] or ''}}" maxlength="255">
                </div>
            </div>
            <div class="form-group">
                <label for="description" class="col-sm-2 control-label">描述</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control"  name="description"  value="{{ $model['description'] or ''}}" maxlength="255">
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

