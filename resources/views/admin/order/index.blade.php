@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="">订单管理</a></li>
    <li class="active">订单管理</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation" class="@if(empty($form['order_status_code']))active @endif">
            <a href="{{ route('admin_orders', ['order_status_code' => '', 'is_self' => $form['is_self'] ? $form['is_self'] : 0]) }}">全部</a>
        </li>
        @foreach($order_status as $o_code => $o_status_text)
        <li role="presentation" class="@if(!empty($form['order_status_code']) && $form['order_status_code'] == $o_code )active @endif">
            <a href="{{ route('admin_orders', ['order_status_code' => $o_code, 'is_self' => $form['is_self'] ? $form['is_self'] : 0]) }}">{{$o_status_text}}</a>
        </li>
       @endforeach
    </ul>
</div>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
           <label class="control-label">订单类型</label>
            <select class="form-control"  name="order_type">
                <option value="" @if(!isset($form['order_type']) || $form['order_type'] === '') selected="selected" @endif>全部</option>
                <option value="0" @if(isset($form['order_type']) && $form['order_type'] === '0') selected="selected" @endif>普通</option>
                <option value="1" @if(isset($form['order_type']) && $form['order_type'] == '1') selected="selected" @endif>礼包</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">订单号：</label>
            <input type="text" class="form-control" name="order_no" value="{{ $form['order_no'] or '' }}" placeholder="请输入订单号">
        </div>
        <div class="form-group">
            <label class="control-label">开始时间</label>
            <input type="text" class="start_date form-control laydate-icon" name="start_date" value="{{ $form['start_date'] or '' }}"  />
        </div>
        <div class="form-group">
           <label class="control-label">结束时间</label>
            <input type="text" class="end_date form-control laydate-icon" name="end_date" value="{{ $form['end_date'] or '' }}" />
        </div>
        <input type="hidden" name="order_status_code" value="{{ $form['order_status_code'] or '' }}">
        <input type="hidden" name="is_self" value="{{ $form['is_self'] or '' }}">
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        当前查询共 销售额￥{{ $orders_statistics['order_total'] }}，积分支付 ￥{{ $orders_statistics['orders_integral'] }}，付款金额 ￥{{ $orders_statistics['payment_amount'] }}，共 {{ $orders->total() }} 个订单   当前页：{{ $orders->firstItem() }}-{{ $orders->lastItem() }}
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
                <th>订单类型</th>
                <th>店铺</th>
                <th>金额</th>
                <th>积分支付</th>
                <th>付款金额</th>
                <th>会员</th>
                <th>下单时间</th>
                <th>订单状态</th>
                <th>支付时间</th>
                <th>发货时间</th>
                <th>完成时间</th>
                <th>买家留言</th>
                <th>核算状态</th>
                <th>已核算商家收益</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($orders))
                    @foreach($orders as $key=>$order)
                        <tr>
                            <td>{{ $order['id'] }}</td>
                            <td style="max-width: 80px">
                                <a target="_blank"  href="/admin/order/detail/{{ $order['id'] }}">{{ $order['order_no'] }}</a>
                                <a type="button" style="color: #00f" target="_blank"  href="/admin/order/detail/{{ $order['id'] }}">
                                    详情
                                </a>
                                <a href="{{ $order['image'] }}" target="_blank"t><img  width="60" height="60" src='{{ $order['image'] }}' /></a>
                                <div  class="address_data_{{ $order['id'] }}">
                                收件人：{{ $order['shipinfo']['fullname'] }} 联系电话：{{ $order['shipinfo']['phone'] }} 收货地址： {{ $order['shipinfo']['province'] }} {{ $order['shipinfo']['city'] }} {{ $order['shipinfo']['district'] }} {{ $order['shipinfo']['town'] }} {{ $order['shipinfo']['village'] }} {{ $order['shipinfo']['address'] }} 邮编：{{ $order['shipinfo']['zip'] }}
                                </div>
                            </td>
                            <td>{{ $order['order_type'] == '1' ? '礼包' : '普通' }}</td>
                            <td style="width: 40px;">@if($order['is_self']) 自营 @endif {{  $order['store_info']['name'] or '' }}</td>
                            <td>￥{{ $order['order_total'] }}</td>
                            <td>￥{{ $order['order_integral'] }}</td>
                            <td>￥{{ $order['payment_amount'] }}</td>
                            <td style="width: 80px">{{ $order['userinfo']['fullname'] or '' }}({{ $order['userinfo']['phone'] or '' }})</td>
                            <td style="width: 80px">{{ $order['created_at'] }}</td>
                            <td>
                                @if($order['order_status_code'] == 'shipping')
                                <span style="color: #f00">{{ $order_status[$order['order_status_code']] }}</span>
                                @else
                                {{ $order_status[$order['order_status_code']] }}
                                @endif
                            </td>
                            <td style="width: 60px">
                                {{ $order['paid_at'] }}
                                @if(!empty($order['faild_at']))
                                    支付失败时间：{{ $order['faild_at'] }}
                                @endif
                            </td>
                            <td style="width: 60px">{{ $order['shipped_at'] }}</td>
                            <td style="width: 60px">{{ $order['done_at'] }}</td>
                            <td style="color: #f00">{{ $order['comment'] }}</td>
                            <td>{{ $order['is_account'] == "1" ? "已核算" : "未核算" }}</td>
                            <td style="width: 120px">
                                @if(!empty($order['account_record']))
                                    <p>总共：￥{{ $order['account_record']['order_profit_total'] }}</p>
                                    <P>含积分：￥{{ $order['account_record']['order_profit_integral'] }}</P>
                                    <p>含现金：￥{{ $order['account_record']['order_profit_amount'] }}</p>
                                @endif
                                
                            </td>
                            <td>
                                @if($order['order_status_code'] == 'shipping' && $order['is_self'] == '1')
                                <a type="button" class="btn btn-danger js_order_shipped" data-id="{{ $order['id'] }}"  href="javascript:void(0)">
                                    订单发货
                                </a>
                                @endif
                                <a type="button" class="btn btn-danger copy_address" data-id="{{ $order['id'] }}"  href="javascript:void(0)" style="display: none">
                                    发货地址
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
                    <div class="form-group shipping_method_select_box">
                        <div class="form-group-label"><span class="text-red">*</span>选择物流方式</div>
                        <select class="js-shipping-select form-control">
                            <option value="">请选择</option>
                            @foreach($shipping_method as $skey => $sm)
                            <option value="{{ $sm['name'] }}">{{ $sm['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group shipping_method_input_box" style="display: none">
                        物流方式
                        <input type="text" class="form-control shipping_method shipping_method_input" required="required" name="shipping_method" />
                    </div>
                    <div class="form-group">
                        <div class="form-group-label"><span class="text-red">*</span>物流跟踪号</div>
                        <input class="form-control tracknumber" name="tracknumber" required="required" maxlength="50" value="" />
                    </div>
                    <div>
                        <a href="javascript:void(0)" class="btn btn-primary btn-block js_order_shipped_submit" data-confim="确认发货?">确认发货</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<input type="input" name="copy_input" id="copy_input" style="visibility: hidden;">
@endsection
@section('scripts')
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
            $(document).on("click", ".js_order_shipped", function(){
                var order_id = $(this).attr('data-id');
                $(".shipped_order_id").val(order_id);
                $("#order_shipped_modal").modal();
            });
            $(document).on("click", ".js_order_shipped_submit", function(){
                var confim_tip = '确认已发货？';
                var form = $(".order-shipped-form");
                var data = form.serializeObject();
                $.showConfirm(confim_tip, function(){
                    var layer = $.showLoad(true);
                    $.post('/admin/order/shipOrder', data, function(result){
                        $.hideLoad();
                        if(result.code == '200'){
                            $.showMessage(result.message, function(){
                                window.location.reload();
                            });
                        } else {
                            $.showMessage(result.message);
                        }
                    });
                })
            });
            //订单运输方式
            $(document).on("change", ".js-shipping-select", function(){
                var shipping_method = $(this).val();
                $(".shipping_method_input").val(shipping_method);
            });

            //订单运输方式
            $(document).on("click", ".checked_type", function(){
                if($(this).is(":checked")){
                    var type = $(this).val();
                    if(type == '2'){
                        $(".shipping_method_input_box").show();
                        $(".shipping_method_select_box").hide();
                    } else {
                        $(".shipping_method_select_box").show();
                        $(".shipping_method_input_box").hide();
                    }
                }
                var checked_type = $(".checked_type:checked")
                $(".shipping_method_input").val(shipping_method);
            });
            //订单运输方式
            $(document).on("click", ".copy_address", function(){
                var order_id = $(this).attr('data-id');
                var text = $(".address_data_" + order_id).text();
                self.copy(text);
            });
        };
        app.copy = function(text){
            var oInput = document.createElement('input');
            oInput.value = text;
            document.body.appendChild(oInput);
            oInput.select(); // 选择对象
            document.execCommand("Copy"); // 执行浏览器复制命令
            oInput.className = 'oInput';
            oInput.style.display='none';
            document.body.removeChild(oInput);
        }
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection
