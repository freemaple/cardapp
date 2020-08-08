@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/user">用户管理</a></li>
    <li class="active">编辑用户</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation"><a href="/admin/user">用户信息</a></li>
      <li role="presentation"><a href="/admin/user/add">添加用户</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" id="edit_user_form" action="/admin/user/save" method="post">
        	<div class="form-group">
	            <input id="user_id" name="id" type="hidden" value="{{ $user->id }}" />
	        </div>
            <div class="form-group">
                 {!! csrf_field() !!}
                <label for="username" class="col-sm-2 control-label">用户名</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="username" value="{{ $user->username }}" name="username" value="" autocomplete="off" placeholder="username" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">密码</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="" id="userpwd" name="userpwd"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">启用</label>
                <div class="col-sm-9">
                  <select class="form-control" value="{{ $user->status }}" name="status">
                    <option value="1" @if($user->status == "1") selected="selected" @endif>是</option>
                    <option value="0" @if($user->status != "1") selected="selected" @endif>否</option>
                  </select>
                </div>
            </div>
            @if(!empty($roles))
            <div class="form-group">
                <label for="userrole" class="col-sm-2 control-label">角色</label>
                <div class="col-sm-9">
                    <ul class="nav nav-pills">
                    <li style="display: none">
                        <input class="role_check" value="1" name="user_role[0]" type="checkbox" checked="checked" />
                    </li>
                    @foreach ($roles as $role)
                    <li>
                    <a href="javascript:void(0)"><input class="role_check" value="1" name="user_role[{{ $role->id }}]" type="checkbox" @if(in_array($role->id, $roleid)) checked="checked" @endif  />{{ $role->name }}</a>
                    </li>
                    @endforeach
                </div>
                </div>
            </div>
            @endif
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

