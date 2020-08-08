@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_banner') }}">banner管理</a></li>
    <li class="active">banner信息</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation" class="active"><a href="{{ route('admin_banner') }}">banner信息</a></li>
        <li role="presentation"><a href="{{ route('admin_banner_add') }}">添加banner</a></li>
    </ul>
</div>
<div class="well">

</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th>编号</th>
                <th>链接</th>
                <th>文字描述</th>
                <th>状态</th>
                <th>有效期开始</th>
                <th>有效期结束</th>
                <th>添加时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($bannerlist))
                    @foreach($bannerlist as $key => $list)
                        <tr>
                            <td>{{ $list['id'] }}</td>
                            <td><a href="{{ HelperImage::storagePath($list['image']) }}" target="_blank">
                                <img src="{{ HelperImage::storagePath($list['image']) }}" width="50" height="50"></a></td>
                            <td>{{ $list['url'] }}</td>
                            <td>{{ $list['alt'] }}</td>
                            <td>{{ $list['enable'] == "1" ? "启用" : "禁用" }}</td>
                            <td>{{ !empty($list['valid_from']) ?date("Y-m-d H:i:s ", $list['valid_from']) :"" }}</td>
                            <td>{{ !empty($list['valid_to']) ? date("Y-m-d H:i:s ", $list['valid_to']) : '' }}</td>
                            <td>{{ $list['created_at'] }}</td>
                            <td>
                                <a type="button" class="btn btn-primary"  href="{{ route('admin_banner_edit', ['id' => $list['id']]) }}">
                                    编辑
                                </a>
                                <a type="button" class="btn btn-primary"  href="{{ route('admin_banner_remove', ['id' => $list['id']]) }}">
                                    删除
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
