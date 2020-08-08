@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/">系统管理</a></li>
    <li class="active">修改密码</li>
</ul>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal"  method="post" id="alert_form">
             <div class="form-group">
                <label for="old_pwd" class="col-sm-2 control-label">当前密码</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="old_pwd" name="old_pwd" placeholder="请输入密码">
                </div>
            </div>
            <div class="form-group">
                <label for="new_pwd" class="col-sm-2 control-label">新密码</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="new_pwd" name="new_pwd" maxlength="50" placeholder="请输入密码">
                </div>
            </div>
            <div class="form-group">
                <label for="new_pwd1" class="col-sm-2 control-label">确认密码</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="new_pwd1" name="new_pwd1" placeholder="请输入密码">
                </div>
            </div>
            <div class="form-group">
                <div class="text-center">
                  <button type="button" class="btn btn-primary" id="btn_alertPwd">保存</button>
                </div>
            </div>
        </form>
    </fieldset>
</div>
@endsection
@section('scripts')
{!! App\Assets\Admin::script('admin/scripts/module/admin.js') !!}
@endsection