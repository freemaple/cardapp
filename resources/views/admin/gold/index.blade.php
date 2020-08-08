@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">金麦管理</a></li>
    <li class="active">金麦</li>
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
<div class="clearfix"><div style="color: #00b;font-size: 16px;padding: 0px 30px 0px 20px">共金麦总量  {{ $user_gold_numbers }} 支</div></div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $userGolds->total() }} 个用户 {{ $userGolds->firstItem() }}-{{ $userGolds->lastItem() }}
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
                <th>礼包佣金</th>
                <th>麦仓总资产</th>
                <th>金麦数量</th>
                <th>每支/元</th>
                <th>金麦价值</th>
                <th>累计收益</th>
                <th>红利余额</th>
                <th>创建时间</th>
                <th>更新时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($userGolds))
                    @foreach($userGolds as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->user_id }}
                                <a type="button" class="text-info"  href="/admin/customer/{{ $list['user_id'] }}" target="_blank">
                                    详情
                                </a>
                            </td>
                            <td>
                                <p>
                                    <a href="{{ HelperImage::getavatar($list->avatar) }}" target="_blank"><img src="{{ HelperImage::getavatar($list->avatar) }}" width="40"></a>
                                </p>
                                {{ $list->phone }}
                                ({{ $list->fullname ? $list->fullname : $list->nickname }})
                            </td>
                            <td>
                                {{ $list->gift_commission }}麦粒
                            </td>
                            <td>
                                ￥{{ $list->total_amount }}
                            </td>
                            <td>
                                {{ $list->gold_number }}支
                            </td>
                            <td>
                                {{ $gift_unit }}/支
                            </td>
                            <td>
                                ￥{{ $list->gold_total }}
                            </td>
                            <td>
                                ￥{{ $list->income_total }}
                            </td>
                            <td>
                                ￥{{ $list->bonus_amount }}
                            </td>
                            <td>
                                {{ $list->created_at }}
                            </td>
                            <td>
                                {{ $list->updated_at }}
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

