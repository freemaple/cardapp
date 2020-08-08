@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">用户管理</a></li>
    <li class="active">用户信息</li>
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
        <label class="control-label" for="nickname">会员等级</label>
        <select>
            <option>全部</option>
            <option>vip</option>
            <option>vip金</option>
            <option>钻石vip</option>
        </select>
        <label class="control-label" for="nickname">排序</label>
        <select>
            <option>时间</option>
            <option>业绩</option>
        </select>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $userlist->total() }} 个用户 {{ $userlist->firstItem() }}-{{ $userlist->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
    <span>有赏积分共： 10000</span>
    <span>赏金共： 10000</span>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>会员id</th>
                <th>手机号码</th>
                 <th>会员等级</th>
                <th>直推人数</th>
                <th>总业绩</th>
                <th>有赏积分</th>
                <th>赏金</th>
                <th>爱心值</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($userlist))
                    @foreach($userlist as $key=>$list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>
                                {{ $list->phone }}
                                <p>{{ $list->fullname ? $list->fullname : $list->nickname }}</p>
                            </td>
                             <td>{{ $level_status[$list->level_status] or '' }} (总经理)</td>
                            <td>100/40</td>
                            <td>300</td>
                            <td>300</td>
                            <td>300</td>
                            <td>200</td>
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
