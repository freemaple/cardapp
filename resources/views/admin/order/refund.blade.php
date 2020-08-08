@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_orders') }}">订单管理</a></li>
    <li class="active">订单管理</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation" class="@if(empty($form['order_status_code']))active @endif">
            <a href="{{ route('admin_orders', ['order_status_code' => '', 'is_self' => '1']) }}">全部</a>
        </li>
        @foreach($order_status as $o_code => $o_status_text)
        <li role="presentation" class="@if(!empty($form['order_status_code']) && $form['order_status_code'] == $o_code )active @endif">
            <a href="{{ route('admin_orders', ['order_status_code' => $o_code, 'is_self' => '1']) }}">{{$o_status_text}}</a>
        </li>
       @endforeach
    </ul>
</div>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">订单：</label>
            <input type="text" class="form-control" name="order_no" value="{{ $form['order_no'] or '' }}" placeholder="请输入姓名">
        </div>
        <input type="hidden" name="order_status_code" value="{{ $form['order_status_code'] or '' }}">
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $order_refunds->total() }} 个退款申请 {{ $order_refunds->firstItem() }}-{{ $order_refunds->lastItem() }}
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
                <th>店铺</th>
                <th>金额</th>
                <th>会员</th>
                <th>下单时间</th>
                <th>订单状态</th>
                <th>支付时间</th>
                <th>发货时间</th>
                <th>退款编号</th>
                <th>退款状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($order_refunds))
                    @foreach($order_refunds as $key=>$order_refund)
                        <tr>
                            <td>{{ $order_refund['order_id'] }}</td>
                            <td>
                                <a target="_blank"  href="/admin/order/detail/{{ $order_refund['order_id'] }}">{{ $order_refund['order_no'] }}</a>
                                <a type="button" style="color: #00f" target="_blank"  href="/admin/order/detail/{{ $order_refund['order_id'] }}">
                                    详情
                                </a>
                            </td>
                            <td>{{ $order_refund['store_info']['name'] or '' }}</td>
                            <td>{{ $order_refund['order_total'] }}</td>
                            <td>{{ $order_refund['userinfo']['fullname'] }}({{ $order_refund['userinfo']['phone'] }})</td>
                            <td>{{ $order_refund['order_info']['created_at'] }}</td>
                            <td>
                                {{ $order_status[$order_refund['order_info']['order_status_code']] }}
                            </td>
                            <td>{{ $order_refund['order_info']['paid_at'] or '' }}</td>
                            <td>{{ $order_refund['order_info']['shipped_at'] }}</td>
                            <td>{{ $order_refund['refundsn'] }}</td>
                            <td>{{ $refund_status[$order_refund['status']] }}</td>
                            <td></td>
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
<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="order_shipped_modal">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 10px">
            <form class="form-horizontal order-shipped-form" role="form" action="/admin/shipOrder" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        订单发货
                    </h4>
                </div>
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="order_id" class="shipped_order_id" value="" />
                    <div class="form-group">
                        <input type="radio" name="checked_type" class="checked_type" value="1" checked="checked">系统选择
                        <input type="radio" name="checked_type" class="checked_type" value="2">自定义
                    </div>
                   
                    <div>
                        <a href="javascript:void(0)" class="btn btn-primary btn-block js_order_shipped_submit" data-confim="确认发货?">确认发货</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
        };
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection
