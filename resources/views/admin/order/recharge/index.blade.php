@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_order_recharge') }}">充值管理</a></li>
    <li class="active">充值记录</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">订单：</label>
            <input type="text" class="form-control" name="order_no" value="{{ $form['order_no'] or '' }}" placeholder="请输入姓名">
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">充值类型</label>
            <select name="order_type">
                <option value="" @if((!isset($form['order_type']) || $form['order_type'] == '')) selected="selected" @endif>全部</option>
                @foreach($recharge_type as $recharge_type_value => $recharge_type_text)
                <option value="{{ $recharge_type_value }}" @if(isset($form['order_type']) && $form['order_type'] == $recharge_type_value &&  $form['order_type'] !== '')selected="selected" @endif>{{ $recharge_type_text }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">核算状态</label>
            <select name="is_account">
                <option value="" @if((!isset($form['is_account']) || $form['is_account'] === '')) selected="selected" @endif>全部</option>
                <option value="1" @if(isset($form['is_account']) &&  $form['is_account'] == '1')) selected="selected" @endif>已核算</option>
                <option value="0" @if(isset($form['is_account']) && $form['is_account'] === '0')) selected="selected" @endif>未核算</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">开始时间</label>
            <input type="text" class="start_date form-control laydate-icon" name="start_date" value="{{ $form['start_date'] or '' }}"  />
        </div>
        <div class="form-group">
           <label class="control-label">结束时间</label>
            <input type="text" class="end_date form-control laydate-icon" name="end_date" value="{{ $form['end_date'] or '' }}" />
        </div>
        <input type="hidden" name="status" value="{{ $form['status'] or '' }}" />
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $orders->total() }} 个订单 {{ $orders->firstItem() }}-{{ $orders->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>订单id</th>
                <th>订单号</th>
                <th>金额</th>
                <th>会员</th>
                <th>创建时间</th>
                <th>备注</th>
                <th>订单状态</th>
                <th>支付时间</th>
                @if($admin_user['is_admin'] == '1')
                <th>支付失败时间</th>
                @endif
                <th>状态</th>
                @if($admin_user['is_admin'] == '1')
                <th>股权状态</th>
                @endif
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($orders))
                    @foreach($orders as $key=>$order)
                        <tr>
                            <td>{{ $order['id'] }}</td>
                            <td>{{ $order['order_no'] }}</td>
                            <td>{{ $order['amount'] }}</td>
                            <td><a href="{{ route('admin_customer_info', $order['userinfo']['id']) }}" target="_blank">{{ $order['userinfo']['fullname'] }}({{ $order['userinfo']['phone'] }})</a></td>
                            <td>{{ $order['created_at'] }}</td>
                            <td>{{ $order['remarks'] }}</td>
                            <td>{{ $order['status'] == "2" ? "已付款" : "未付款" }}</td>
                            <td>{{ $order['paid_at'] }}</td>
                            @if($admin_user['is_admin'] == '1')
                            <td>{{ $order['faild_at'] }}</td>
                            @endif
                            <td>{{ $order['is_account'] == "1" ? "已核算" : "未核算" }}</td>
                            @if($admin_user['is_admin'] == '1')
                            <td>
                                {{ $order['equity_account'] == "1" ? "已核算" : "未核算" }}
                            </td>
                            @endif
                            @if($admin_user['is_admin'] == '1' && $order['is_account'] != "1")
                            <td>
                                <a type="button" class="btn btn-primary" target="_blank"  href="/admin/order/recharge/pay/{{ $order['id'] }}">
                                    核算
                                </a>
                            </td>
                            @endif
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
