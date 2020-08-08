@extends('admin.template.layout')
@section('content')
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation" class="active"><a href="{{ route('admin_course_category') }}">一级分类</a></li>
        @if(empty($fatherCategory))
        <li role="presentation"><a href="{{ route('admin_course_category_add') }}">添加分类</a></li>
        @endif
    </ul>
</div>
<div class="well">
    <form class="form-inline" role="form">
        @if(!empty($fatherCategory))
        <div class="form-group">
            <label class="control-label" for="nickname">父级分类：</label>
            <input type="text" class="form-control" value="{{ $fatherCategory->name or '' }}" readonly="readonly" />
        </div>
        @endif
        <div class="form-group">
            <label class="control-label" for="nickname">名称：</label>
            <input type="text" class="form-control"  name="name" value="{{ $form['name'] or '' }}" placeholder="请输入名称">
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $category_list->total() }} 个分类 {{ $category_list->firstItem() }}-{{ $category_list->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th>编号</th>
                <th>名称</th>
                <th>描述</th>
                <th>创建时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($category_list))
                    @foreach($category_list as $key=>$list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td><a href="{{ route('admin_course_category', $list->id) }}">{{ $list->name }}</a></td>
                            <td>{{ $list->description }}</td>
                            <td>{{ $list->created_at }}</td>
                            <td>{{ $list->enable == "1" ? "启用" : "禁用" }}</td>
                            <td>
                                <a type="button" class="btn btn-primary"  href="{{ route('admin_course_category_add', ['pid' => $list->id]) }}">
                                    添加子分类
                                </a>
                                <a type="button" class="btn btn-primary"  href="{{ route('admin_course_category', $list->id) }}">
                                    子分类
                                </a>
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
