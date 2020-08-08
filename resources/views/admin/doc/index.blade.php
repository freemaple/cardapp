@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/doc">文档管理</a></li>
    <li class="active">帮助文档</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation" class="active"><a href="/admin/doc">文档信息</a></li>
      <li role="presentation"><a href="/admin/doc/add">添加文档</a></li>
    </ul>
</div>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="name">名称</label>
            <input type="text" class="form-control"  name="name" value="{{ $form['name'] or '' }}" placeholder="请输入文档">
        </div>
        <button type="submit" class="btn btn-info"> 查询</button>
    </form>
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th>编号</th>
                <th>名称</th>
                <th>目录</th>
                <th>标题</th>
                <th>是否禁用</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
	            @if (!empty($doc_list))
	                @foreach($doc_list as $key => $list)
		                <tr>
		                    <td>{{ $list['id'] }}</td>
                            <td>{{ $list['name'] }}</td>
                            <td>{{ $list['catalog_name'] }}</td>
                            <td>{{ $list['meta_title'] }}</td>
		                    <td>{{ $list['enable'] == "1" ? "启用" : "禁用" }}</td>
		                    <td>{{ $list['created_at'] }}</td>
		                    <td>
		                        <a type="button" class="btn btn-primary"  href="/admin/doc/edit/{{ $list['id'] }}">
		                            编辑
		                        </a>
		              		</td>
		                </tr>
	                @endforeach
	            @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
