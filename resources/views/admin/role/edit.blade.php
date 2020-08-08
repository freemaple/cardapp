@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/user">角色管理</a></li>
    <li class="active">编辑角色</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation"><a href="/admin/role">角色信息</a></li>
      <li role="presentation"><a href="/admin/role/add">添加角色</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" method="post">
        	<div class="form-group">
                {!! csrf_field() !!}
                <label for="username" class="col-sm-2 control-label">名称</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  name="name" maxlength="50"  autocomplete="off" placeholder="名称" required="required" value="{{ $form['name'] or ''  }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">显示名称</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" maxlength="50" name="display_name" maxlength="50"  autocomplete="off" placeholder="显示名称" required="required" value="{{ $form['display_name'] or ''  }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">描述</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" maxlength="50" name="description" maxlength="50"  autocomplete="off" placeholder="描述" required="required" value="{{ $form['description'] or ''  }}">
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
{!! App\Assets\Admin::script('scripts/module/admin.js', '') !!}
@endsection



