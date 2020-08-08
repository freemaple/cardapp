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
        <label class="control-label" for="nickname">年份</label>
        <select name="year">
            <option>全部</option>
            @for($y=2018; $y<=2100; $y++)
            <option value="{{$y}}" @if(isset($form['year']) && $form['year'] == $y) selected="selected" @endif>{{$y}}</option>
            @endfor
        </select>
        <label class="control-label" for="nickname">月份</label>
        <select name="month">
            <option>全部</option>
            @foreach($months as $m)
            <option value="{{$m}}" @if(isset($form['month']) && $form['month'] == $m) selected="selected" @endif>{{$m}}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $userlist->total() }} 个用户 {{ $userlist->firstItem() }}-{{ $userlist->lastItem() }}
    </div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>会员id</th>
                <th>手机号码</th>
                <th>头像</th>
                <th>会员等级</th>
                <th>会员级别</th>
                <th>剩余有赏积分</th>
                <th>剩余赏金</th>
                <th>推荐人</th>
                <th>总直推人数</th>
                <th>伞下vip开通量</th>
                <th>伞下vip续费量</th>
                <th>伞下店铺付费量</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($userlist))
                    @foreach($userlist as $key=>$list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>
                                {{ $list->fullname ? $list->fullname : $list->nickname }}
                                <p>{{ $list->phone }}</p>
                            </td>
                            <td><a href="{{ HelperImage::getavatar($list->avatar) }}" target="_blank"><img src="{{ HelperImage::getavatar($list->avatar) }}" width="40"></a></td>
                            </td>
                            <td>{{ $level_status[$list->level_status] or '' }}</td>
                            <td><span class="text-red">{{ $user_type[$list->user_type]  }}</span></td>
                            <td>{{ $list->integral_amount  }}</td>
                            <td>{{ $list->reward_amount  }}</td>
                            <td>{{ $list->referrer_user['fullname'] or '' }}</td>
                            <td>{{ $list->referrer_user_count  }}</td>
                            <td>{{ isset($list->user_statistics['vip_open_number']) ? $list->user_statistics['vip_open_number'] : 0  }}</td>
                            <td>{{ isset($list->user_statistics['vip_renewal_number']) ? $list->user_statistics['vip_renewal_number'] : 0   }}</td>
                            <td>{{ isset($list->user_statistics['store_number']) ? $list->user_statistics['store_number'] : 0   }}</td>
                            <td>
                                <a type="button" class="btn btn-primary" target="_blank"  href="/admin/customer/{{$list->id}}">
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
