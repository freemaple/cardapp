
@extends('admin.template.layout')
@section('content')
<div class="content">
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">订单详情</h3>
        </div>
        <div class="panel-body">
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>订单类型</th>
                        <th>订单总额</th>
                        <th>订单运费</th>
                        <th>订单付款金额</th>
                        <th>订单积分支付</th>
                        <th>订单下单时间</th>
                        <th>订单状态</th>
                        <th>支付时间</th>
                        <th>支付失败时间</th>
                        <th>发货时间</th>
                        <th>完成时间</th>
                        <th>买家留言</th>
                        <th>核算状态</th>
                        <th>卖家核算金额</th>
                    </tr>
                </thead>
               <tbody>
                  <tr>
                    <td>{{ $order_info['order_no'] }}</td>
                    <td>{{ $order_info['order_type'] == '1' ? '礼包' : '普通' }}</td>
                    <td>￥{{$order_info['order_total'] }}</td>
                    <td>￥{{ $order_info['order_shipping'] }}</td>
                    <td>￥{{ $order_info['order_integral'] }}</td>
                    <td>￥{{ $order_info['payment_amount'] }}</td>
                    <td>{{ $order_info['created_at'] }}</td>
                    <td>
                      {{ $order_status[$order_info['order_status_code']] }}
                      @if($order_info['order_status_code'] == 'cancel')
                      {{ $order_info['cancel_at'] }}
                      @endif
                    </td>
                    <td>{{ $order_info['paid_at'] }}</td>
                    <td>{{ $order_info['faild_at'] }}</td>
                    <td>{{ $order_info['shipped_at'] }}</td>
                    <td>{{ $order_info['done_at'] }}</td>
                    <td style="color: #f00">{{ $order_info['comment'] }}</td>
                    <td>{{ $order_info['is_account'] == "1" ? "已核算" : "未核算" }}</td>
                    <td style="width: 120px">
                      @if(!empty($order_info['account_record']))
                          <p>总共：￥{{ $order_info['account_record']['order_profit_total'] }}</p>
                          <P>含积分：￥{{ $order_info['account_record']['order_profit_integral'] }}</P>
                          <p>含现金：￥{{ $order_info['account_record']['order_profit_amount'] }}</p>
                      @endif
                    </td>
                  </tr>
               </tbody>
            </table>
        </div>
    </div>
    @if(!empty($order_info['shipping_info']))
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">发货信息</h3>
        </div>
        <div class="panel-body">
            <table class="table table-condensed table-striped">
               <thead>
                  <tr>
                    <th>运输方式</th>
                    <th>跟踪号</th>
                    <th>创建时间</th>
                  </tr>
               </thead>
               <tbody>
                    <tr>
                        <td>{{ $order_info['shipping_info']['shipping_method'] }}</td>
                        <td>{{ $order_info['shipping_info']['tracknumber'] }}</td>
                        <td>{{ $order_info['shipping_info']['created_at'] }}</td>
                    </tr>
               </tbody>
            </table>
        </div>
    </div>
    @endif
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">地址</h3>
        </div>
        <div class="panel-body">
            <p>
                <span>姓名:</span>
                <span>{{ $order_info['user_info']['fullname'] }}</span>
            </p>
            <p>
                <span>联系电话：:</span>
                <span>{{ $order_info['user_info']['phone'] }}</span>
            </p>
            <p>地址：<span>{{ $order_info['user_info']['province'] }}, </span><span>{{ $order_info['user_info']['city'] }}, </span><span>{{ $order_info['user_info']['district'] }}，</span><span>{{ $order_info['user_info']['town'] }}，</span><span>{{ $order_info['user_info']['village'] }}, </span><span>{{ $order_info['user_info']['address'] }}</span></p>
            <p>邮编：{{ $order_info['user_info']['zip'] }}</p>
        </div>
    </div>
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">订单产品</h3>
        </div>
        <div class="panel-body">
            <table class="table table-condensed table-striped">
               <thead>
                  <tr>
                     <th>产品名称</th>
                     <th>图片</th>
                     <th>价格</th>
                     <th>数量</th>
                     <th>属性</th>
                  </tr>
               </thead>
               <tbody>
                    @if(!empty($order_info['order_products']))
                    @foreach ($order_info['order_products'] as $ikey => $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td><img  width="100" height="100" src='{{ $item['image'] }}' /></td>
                            <td>{{ $item['price'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ $item['spec'] }}</td>
                        </tr>
                    @endforeach
                    @endif
               </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
