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
@if(count($order_refunds) > 0)
<div class="order-list-box js-order-list-box">
    @if(!empty($pager))
    <div style="text-align: right;padding: 10px 0px">
        {{ $pager }}
    </div>
    @endif
    <ul class="clearfix js-order-list">
        @foreach($order_refunds as $pkey => $refund_item)
        <li class="order-item js-order-item clearfix">
            <div class="order-item-header clearfix">
                <span class="order-no">订单号: {{ $refund_item['order_no']}}</span>
                <span class="order-status-text">{{ $refund_status[$refund_item['status']] }}</span>
            </div>
            <div class="order-item-content clearfix">
                <div class="img lazy" style="height: 80px;overflow: hidden;min-height: unset;">
                    <img data-img="{{ $refund_item['product']['image'] or '' }}" class="lazyload" />
                </div>
                <div class="info">
                    <div class="info-box">
                        <div class="name">{{ $refund_item['product']['product_name'] }}</div>
                        <div class="qty">
                            <span>数量：</span><span>{{ $refund_item['order_item_qty'] }}</span>
                        </div>
                        @if(!empty($refund_item['product']['spec']))
                        <div class="spec">
                           {{ $refund_item['product']['spec'] }}
                        </div>
                        @endif
                        <div class="price-info">
                            ￥{{ $refund_item['order_total'] }}
                        </div>
                        <span style="color: #666;font-size: 12px;">申请时间：</span><span style="font-size: 12px">{{ $refund_item['created_at'] }}</span>
                    </div>
                </div>
            </div>
            <div >
                <span style="color: #666;font-size: 12px;">申请理由：</span><span class="text-red" style="font-size: 14px">{{ $refund_item['reason'] }}</span>
            </div>
            @if(!empty($refund_item['handel_reason']))
                <div>
                    <span style="color: #666;font-size: 12px;">解决方法：</span><span class="text-red" style="font-size: 14px">{{ $refund_item['handel_reason'] }}</span>
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
            <p>没有任何退款订单！</p>
        </div>
    </div>
@endif
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/order/index.js') }}"></script>
@endsection