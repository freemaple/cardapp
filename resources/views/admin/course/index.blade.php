@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/course">课程管理</a></li>
    <li class="active">课程列表</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation" class="active"><a href="/admin/course">自营课程</a></li>
      <li role="presentation"><a href="/admin/course/add">添加自营课程</a></li>
    </ul>
</div>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="name">课程名</label>
            <input type="text" class="form-control"  name="name" value="{{ $name }}" placeholder="请输入课程名">
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $course_list->total() }} 个课程 {{ $course_list->firstItem() }}-{{ $course_list->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th>编号</th>
                <th>课程编号</th>
                <th>课程名</th>
                <th>图片</th>
                <th>是否禁用</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
	            @if (!empty($course_list))
	                @foreach($course_list as $key => $list)
		                <tr>
		                    <td>{{ $list['id'] }}</td>
                            <td>{{ $list['course_number'] }}</td>
		                    <td>{{ $list['name'] }}</td>
                            <td><img src="{{ HelperImage::storagePath($list['banner']) }}" width="100" /></td>
		                    <td>{{ $list['enable'] == "1" ? "启用" : "禁用" }}</td>
		                    <td>{{ $list['created_at'] }}</td>
		                    <td>
                              <a type="button" class="btn btn-primary" href="{{ route('admin_course_edit', $list['id'] ) }}">编辑</a>
                              <a type="button" class="btn btn-primary" href="{{ route('admin_course_detail', $list['id'] ) }}">详情</a>
		                    </td>
		                </tr>
	                @endforeach
	            @endif
            </tbody>
        </table>
        @if(!empty($pager))
        <div class="panel-body">
            <div class="text-center">{{ $pager }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
