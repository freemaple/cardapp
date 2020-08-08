@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/user">用户管理</a></li>
    <li class="active">添加用户</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation"><a href="/admin/user">用户信息</a></li>
      <li role="presentation"  class="active"><a href="/admin/user/add">添加用户</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" action="/admin/user/save" method="post" id="add_user_form">
	        <div class="form-group">
	            <input type="hidden" id="" />
	        </div>
            <div class="form-group">
                 {!! csrf_field() !!}
                <label for="username" class="col-sm-2 control-label">用户名</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="username" name="username" maxlength="50"  autocomplete="off" placeholder="username" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">密码</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" maxlength="50" id="userpwd" name="userpwd" maxlength="50"  autocomplete="off" placeholder="password" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">启用</label>
                <div class="col-sm-9">
                  <select class="form-control" value="" name="status">
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
