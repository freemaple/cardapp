@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/user">用户管理</a></li>
    <li class="active">用户信息</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation" class="active"><a href="/admin/user">用户信息</a></li>
      <li role="presentation"><a href="/admin/user/add">添加用户</a></li>
    </ul>
</div>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="name">用户名</label>
            <input type="text" class="form-control" id="username" name="username" value="{{ $username }}" placeholder="请输入用户名">
        </div>
        <button type="submit" class="btn btn-info"> 查询</button>
    </form>
    <div class="pagination">
       <button type="button" class="btn btn-primary" id="remove_items" data-action="/admin/user/remove">
            删除
        </button>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" class="rows_check" /> </th>
                <th>编号</th>
                <th>用户名</th>
                <th>是否禁用</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
	            @if (!empty($userlist))
	                @foreach($userlist as $key=>$list)
		                <tr>
		                    <td><input type="checkbox" class="row_check" data-id="{{ $list->id }}" /></td>
		                    <td>{{ $list->id }}</td>
		                    <td>{{ $list->username }}</td>
		                    <td>{{ $list->status == "1" ? "启用" : "禁用" }}</td>
		                    <td>{{ $list->created_at }}</td>
		                    <td>
		                        <a type="button" class="btn btn-primary update_item"  href="/admin/user/edit/{{ $list->id }}">
		                            编辑
		                        </a>
		                        <button type="button" class="btn btn-primary remove_item"  data-id="{{ $list->id }}" data-action="/admin/user/remove">
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
@if(!empty($pager))
<div class="panel-body">
    <div class="text-center">{{ $pager }}</div>
</div>
@endif
@endsection
