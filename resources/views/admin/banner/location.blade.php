@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_banner') }}">banner管理</a></li>
    <li class="active">banner位置信息</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation" class="active"><a href="{{ route('admin_banner_location') }}">banner位置</a></li>
        <li role="presentation"><a href="{{ route('admin_banner_location_add') }}">添加banner位置</a></li>
    </ul>
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th>编号</th>
                <th>位置</th>
                <th>描述</th>
                <th>添加时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($banner_location))
                    @foreach($banner_location as $key => $list)
                        <tr>
                            <td>{{ $list['id'] }}</td>
                            <td>{{ $list['location'] }}</td>
                            <td>{{ $list['description'] }}</td>
                            <td>{{ $list['created_at'] }}</td>
                            <td>
                                <a type="button" class="btn btn-primary update_item"  href="{{ route('admin_banner_location_edit', ['id' => $list['id']]) }}">
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
@if(!empty($pager))
<div class="panel-body">
    <div class="text-center">{{ $pager }}</div>
</div>
@endif
@endsection
