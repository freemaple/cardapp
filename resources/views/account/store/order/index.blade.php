@extends('layouts.app')

@section('title') {{ $title }}  @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('account_store') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">
           {{ $title }}
        </div>
    </div>
</div>
@endsection
@section('content')
@if(!empty($do_show_status))
<div class="order-status-menu">
    <div class="menu-box">
        <ul class="menu-box-list">
            <li class="menu-box-item @if(empty($form['status_code']) || $form['status_code'] == '') current @endif"> 
                <a href="{{ Helper::route('account_store_orders') }}">所有</a>
            </li>
            @foreach($do_show_status as $status_code => $status_text)
            <li class="menu-box-item @if(!empty($form['status_code']) && $form['status_code'] == $status_code) current @endif"> 
                <a href="{{ Helper::route('account_store_orders', ['status_code' => $status_code]) }}">
                    {{ $status_text }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@if(count($orders) > 0)
<div class="order-list-box js-order-list-box">
    @if(!empty($pager))
    <div style="text-align: right;padding: 10px 0px">
        {{ $pager }}
    </div>
    @endif
    <ul class="clearfix js-order-list">
        @foreach($orders as $pkey => $order_item)
        <li class="order-item js-order-item js-order-item-{{ $order_item['id'] }} clearfix">
            <div class="order-item-header clearfix">
                <span class="order-no">订单号: {{ $order_item['order_no']}}</span>
                <span class="order-status-text">{{ $order_status[$order_item['order_status_code']] }}</span>
            </div>
            <div class="order-item-content clearfix">
                <a href="{{ Helper::route('account_store_order_detail', $order_item['order_no']) }} ">
                    <div class="img lazy">
                        <img src="{{ $order_item['product']['image'] or '' }}" />
                    </div>
                    <div class="info">
                        <div class="info-box">
                            <div class="name">{{ $order_item['product']['product_name'] }}</div>
                            <div class="price-info">
                                ￥{{ $order_item['order_total'] }}
                            </div>
                            @if(!empty($order_item['product']['spec']))
                            <div class="spec">
                               {{ $order_item['product']['spec'] }}
                            </div>
                             <div class="qty">
                                <span>数量：</span><span>{{ $order_item['order_item_qty'] }}</span>
                            </div>
                            @endif
                            @if(!empty($order_item['comment']))
                            <div>
                               买家留言：<span class="text-red">{{ $order_item['comment'] }}</span>
                            </div>
                            @endif
                            @if(!empty($order_item['shipping_info']))
                                @if($order_item['shipping_info']['shipping_method'])
                                    <p style="margin-top: 3px">物流方式: {{ $order_item['shipping_info']['shipping_method'] }}</p>
                                @endif
                                @if($order_item['shipping_info']['tracknumber'])
                                    <p style="margin-top: 3px">物流单号: {{ $order_item['shipping_info']['tracknumber'] }}
                                        <a href="javascript:void(0)" class="text-info js-search-track" data-tracknumber="{{ $order_item['shipping_info']['tracknumber'] }}">查看物流</a>
                                    </p>
                                @endif
                            @endif
                            @if(!empty($order_item['refund']))

                                @if($order_item['refund']['status'] == '0')
                                    <div class="text-red">退换货申请中 <a class="operate-btn" href="{{ Helper::route('account_store_order_refundlist', ['order_id' => $order_item['id']]) }}">去处理</a></div>
                                @endif
                                @if($order_item['refund']['status'] == '1')
                                    <div class="text-red">退款中 <a class="operate-btn" href="{{ Helper::route('account_store_order_refundlist', ['order_id' => $order_item['id']]) }}">去查看</a></div>
                                @endif
                                @if($order_item['refund']['status'] == '2')
                                    <div class="text-red">已退款 <a class="operate-btn" href="{{ Helper::route('account_store_order_refundlist', ['order_id' => $order_item['id']]) }}">去查看</a></div>
                                @endif
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @if($order_item['order_status_code'] != 'cancel')
            <div class="operate-box">
                @if($order_item['order_status_code'] == 'shipping' && $order_item['refund_status'] == '0')
                    <a href="javascript:void(0)" data-id="{{ $order_item['id'] }}" class="operate-btn js-order-confirm-shipped">确认发货</a>
                @endif
                @if($order_item['order_status_code'] == 'finished')
                    @if($order_item['is_review'] == '1')
                    <a href="{{ Helper::route('account_store_order_reviews', $order_item['id']) }}" class="operate-btn">查看评论</a>
                    @endif
                @endif
                <a href="{{ Helper::route('account_store_order_detail', $order_item['order_no']) }}" class="operate-btn">详情</a>
            </div>
            @endif
        </li>
        @endforeach
    </ul>
    @if(!empty($pager))
    <div style="text-align: center;padding: 10px 0px">
        {{ $pager }}
    </div>
    @endif
</div>
@else
    <div class="no-results">
        <div class="result-img">@include('template.rote')</div>
        <div class="result-content">
            <p>没有任何匹配订单！</p>
        </div>
    </div>
@endif
@endsection
@section('scripts')
<script type="text/template" id="shipped-template">
    <form name="order-shipped-form" class="order-shipped-form" method="post">
        <div class="shipped-box" style="padding: 10px;max-width: 95%;width: 360px">
            <div style="color: #fe7589;text-align: center;font-size: 18px">订单发货</div>
            <input type="hidden" name="order_id" class="order_id" value="">
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
                <a href="javascript:void(0)" class="btn btn-primary btn-block js-order-shipped" data-confim="确认发货?">确认发货</a>
            </div>
        </div>
    </form>
</script>
<script src="{{ Helper::asset_url('/media/scripts/view/store/order/index.js') }}"></script>
@endsection