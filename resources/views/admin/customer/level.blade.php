@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">会员管理</a></li>
    <li class="active">会员信息</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">姓名：</label>
            <input type="text" class="form-control" name="fullname" value="{{ $form['fullname'] or '' }}" placeholder="请输入姓名">
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">手机号码：</label>
            <input type="text" class="form-control" name="phone" value="{{ $form['phone'] or '' }}" placeholder="请输入手机号码">
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
                <th>会员id</th>
                <th>会员名</th>
                <th>父级id</th>
                <th>上级Id</th>
                <th>总监id</th>
                <th>总经理Id</th>
                <th>创建时间</th>
                <th>更新时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($userlist))
                    @foreach($userlist as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->user_id }}
                                <a type="button" class="text-info"  href="/admin/customer/{{ $list['user_id'] }}" target="_blank">
                                    详情
                                </a>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ $list->user_name }}</span>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ !empty($list->parent_user) ? $list->parent_user['user_name'] : '' }}</span>
                                <span>({{ $list->parent_id }})</span>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ $list->parent_ids }}</span>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ $list->manager_id }}</span>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ $list->director_id }}</span>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ $list->created_at }}</span>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ $list->updated_at }}</span>
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
