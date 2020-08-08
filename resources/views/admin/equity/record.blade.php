@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">股权管理</a></li>
    <li class="active">股权记录</li>
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
        共 {{ $equity_record->total() }} 个记录 {{ $equity_record->firstItem() }}-{{ $equity_record->lastItem() }}
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
                <th>手机号码</th>
                <th>姓名/昵称</th>
                <th>头像</th>
                <th>赠送股权值</th>
                <th>描述</th>
                <th>赠送时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($equity_record))
                    @foreach($equity_record as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->user_id }}
                                <a type="button" class="text-info"  href="/admin/customer/{{ $list['user_id'] }}" target="_blank">
                                    详情
                                </a>
                            </td>
                            <td>
                                {{ $list->phone }}
                            </td>
                            <td>{{ $list->fullname ? $list->fullname : $list->nickname }}</td>
                            <td><a href="{{ HelperImage::getavatar($list->avatar) }}" target="_blank"><img src="{{ HelperImage::getavatar($list->avatar) }}" width="40"></a></td>
                            </td>
                            <td>
                                {{ $list->equity_value }}
                            </td>
                            <td>
                                {{ $list->content }}
                            </td>
                            <td>
                                {{ $list->created_at }}
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

