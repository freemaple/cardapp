@extends('layouts.app')

@section('title') {{ $title }}  @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">
           {{ $title }}
        </div>
    </div>
</div>
@endsection
@section('content')
@if(!empty($order_status))
<div class="order-status-menu">
    <div class="menu-box">
        <ul class="menu-box-list">
            <li class="menu-box-item @if(empty($form['status_code']) || $form['status_code'] == '') current @endif"> 
                <a href="{{ Helper::route('account_orders') }}">所有</a>
            </li>
            @foreach($do_show_status as $status_code => $status_text)
            <li class="menu-box-item @if(!empty($form['status_code']) && $form['status_code'] == $status_code) current @endif"> 
                <a href="{{ Helper::route('account_orders', ['status_code' => $status_code]) }}">
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
    <div style="text-align: right;padding: 10px 0px;">
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
                <a href="@if($order_item['order_status_code'] == 'pending') {{ Helper::route('account_order_pay', $order_item['order_no']) }} @else {{ Helper::route('account_order_detail', $order_item['order_no']) }} @endif">
                    <div class="img lazy">
                        <img data-img="{{ $order_item['product']['image'] or '' }}" class="lazyload" />
                    </div>
                    <div class="info">
                        <div class="info-box">
                            <div class="name">{{ $order_item['product']['product_name'] }}</div>
                            <div class="price-info">
                                ￥{{ $order_item['order_total'] }}
                            </div>
                            <div class="qty">
                                <span>数量：</span><span>{{ $order_item['order_item_qty'] }}</span>
                            </div>
                            @if(!empty($order_item['product']['spec']))
                            <div class="spec">
                               {{ $order_item['product']['spec'] }}
                            </div>
                            @endif
                            <div style="margin: 0px 0px 5px 0px">
                                <span style="color: #666;font-size: 12px;">下单时间：</span>
                                <span style="font-size: 12px">{{ $order_item['created_at'] }}</span>
                            </div>
                            @if($order_item['order_type'] == '1')
                            <div style="margin: 0px 0px 5px 0px">
                                <div class="text-red">礼包产品</div>
                            </div>
                            @endif
                            @if($order_item['order_status_code'] == 'pending')
                                @if(!empty($order_item['pay_remaining_time']) && $order_item['pay_remaining_time'] > 0)
                                <div class="expires-info order-expires-timer" style="display: none" data-remaining-time="{{  $order_item['pay_remaining_time'] }}">
                                    <span style="background-color: #ff5430;color: #fff;padding: 2px">本优惠订单</span>
                                    <span>剩</span>
                                    <span class="expires-time expires-timer">
                                        <span class="hour"></span>:
                                        <span class="minute"></span>:
                                        <span class="second"></span>
                                    </span>
                                    <span style="color: #444">自动取消</span>
                                </div>
                                @endif
                            @endif
                            @if(!empty($order_item['shipping_info']))
                                @if($order_item['shipping_info']['shipping_method'])
                                    <p>物流方式: {{ $order_item['shipping_info']['shipping_method'] }}</p>
                                @endif
                                @if($order_item['shipping_info']['tracknumber'])
                                    <p>
                                        物流单号: {{ $order_item['shipping_info']['tracknumber'] }}
                                        <a href="javascript:void(0)" class="text-info js-search-track" data-tracknumber="{{ $order_item['shipping_info']['tracknumber'] }}">查看物流</a>
                                    </p>
                                    
                                @endif
                            @endif
                            @if(!empty($order_item['refund']))

                                @if($order_item['refund']['status'] == '0')
                                    <div class="text-red">退换货申请中
                                @endif
                                @if($order_item['refund']['status'] == '1')
                                    <div class="text-red">退款中</div>
                                @endif
                                @if($order_item['refund']['status'] == '2')
                                    <div class="text-red">已退款</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @if($order_item['order_status_code'] != 'cancel')
            <div class="operate-box">
                @if($order_item['order_status_code'] == 'pending')
                    <a href="javascript:void(0)" class="operate-btn js-cancel-order" data-id="{{ $order_item['id'] }}">取消</a>
                    @if($order_item['pay_remaining_time'] > 0)
                    <a href="{{ Helper::route('account_order_pay', [$order_item['order_no']]) }}" class="operate-btn">去付款</a>
                    @endif
                @endif
                @if($order_item['order_status_code'] == 'shipped' || $order_item['order_status_code'] == 'shipping')
                    @if(empty($order_item['refund']) && $order_item['order_type'] != '1')
                    <a href="{{ Helper::route('account_order_refund', [$order_item['id']]) }}" data-id="{{ $order_item['id'] }}" class="operate-btn">申请退换货</a>
                    @endif
                @endif
                @if($order_item['order_status_code'] == 'shipped' && $order_item['refund_status'] != '2')
                    @if(empty($order_item['refund']))
                    <a href="javascript:void(0)" data-id="{{ $order_item['id'] }}" class="operate-btn js-order-confirm-receipted">确认收货</a>
                    @endif
                @endif
                @if($order_item['order_status_code'] == 'finished')
                    @if($order_item['is_review'] == '1')
                    <a href="{{ Helper::route('account_order_reviews', $order_item['id']) }}" class="operate-btn">查看评论</a>
                    @else
                    <a href="{{ Helper::route('account_order_reviews_add', $order_item['id']) }}" class="operate-btn">去评论</a>
                    @endif
                @endif
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
            <p>
                没有任何订单！
            </p>
        </div>
        <div class="control-group">
            <a class="btn btn-primary" href="{{ Helper::route('home') }}">到别处逛逛</a>
        </div>
    </div>
@endif
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/order/index.js') }}"></script>
@endsection