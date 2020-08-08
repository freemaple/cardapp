@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/role">角色管理</a></li>
    <li class="active">用户信息</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation" class="active"><a href="/admin/role">角色信息</a></li>
      <li role="presentation"><a href="/admin/role/add">添加角色</a></li>
    </ul>
</div>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="name">名称</label>
            <input type="text" class="form-control"  name="name" value="{{ $form['name'] or '' }}" placeholder="请输入名称">
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th>编号</th>
                <th>名称</th>
                <th>显示名称</th>
                <th>描述</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
	            @if (!empty($rolelist))
	                @foreach($rolelist as $key => $list)
		                <tr>
		                    <td>{{ $list->id }}</td>
		                    <td>{{ $list->name }}</td>
                            <td>{{ $list->display_name }}</td>
                            <td>{{ $list->description }}</td>
		                    <td>{{ $list->created_at }}</td>
		                    <td>
		                        <a type="button" class="btn btn-primary"  href="{{ route('admin_role_edit', $list->id) }}">
		                            编辑
		                        </a>
		                        <button type="button" class="btn btn-primary">
		                            删除
		                        </button>
		                    </td>
		                </tr>
	                @endforeach
	            @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
