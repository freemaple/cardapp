@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/user">课程分类</a></li>
    <li class="active">添加分类</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation"><a href="/admin/user">课程分类</a></li>
      <li role="presentation"  class="active"><a href="/admin/user/add">添加分类</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" method="post">
            <div class="form-group">
                {!! csrf_field() !!}
                @if(!empty($fatherCategory))
                <label for="name" class="col-sm-2 control-label">父级名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" disabled="disabled" value="{{ $fatherCategory->name }}" />
                </div>
                @endif
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">名称</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="name" maxlength="50"  autocomplete="off" placeholder="名称" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">描述</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="description" autocomplete="off" placeholder="描述" required="required"></textarea>
                </div>
            </div>
             <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">启用</label>
                <div class="col-sm-9">
                  <select class="form-control" name="enable">
                    <option value="1">是</option>
                    <option value="0">否</option>
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
@section('scripts')
{!! App\Assets\Admin::script('admin/scripts/module/admin.js') !!}
@endsection
