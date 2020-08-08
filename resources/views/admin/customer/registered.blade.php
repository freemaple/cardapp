@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">用户管理</a></li>
    <li class="active">用户信息</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">用户名：</label>
            <input type="text" class="form-control" id="user_name" name="nickname" value="{{ $username or '' }}" placeholder="请输入昵称">
        </div>
        <div class="form-group">
            <label class="control-label">开始时间</label>
            <input type="text" class="start_date form-control laydate-icon" name="login_start_date" value="{{ $login_start_date or '' }}"  />
        </div>
        <div class="form-group">
           <label class="control-label">结束时间</label>
            <input type="text" class="end_date form-control laydate-icon" name="login_end_date" value="{{ $login_end_date or '' }}" />
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $userlist->total() }} 个用户 {{ $userlist->firstItem() }}-{{ $userlist->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>编号</th>
                <th>姓名</th>
                <th>手机号码</th>
                <th>推荐人</th>
                <th>源推荐人</th>
                <th>注册时间</th>
                <th>注册ip</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($registered_records))
                    @foreach($registered_records as $key=>$list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->fullname }}</td>
                            <td>{{ $list->phone }}</td>
                            <td>{{ $list->user_type == '2' ? '机构' : '学员'}}</td>
                            <td>{{ $list->created_at }}</td>
                            <td>{{ $list->signup_ip }}</td>
                            <td>{{ $list->enable == "1" ? "正常" : "黑名单" }}</td>
                            <td>
                                <a type="button" class="btn btn-primary update_item"  href="">
                                    详情
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
