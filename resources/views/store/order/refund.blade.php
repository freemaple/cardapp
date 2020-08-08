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
<div class="order-list-box js-order-list-box" data-action="/api/order/refundlist" data-page="1" @if($order_refunds['last_page'] == 1) data-load-more="-1" @endif>
    <ul class="clearfix js-order-list">
        @foreach($order_refunds as $pkey => $refund_item)
        <li class="order-item js-order-item clearfix">
            <div class="order-item-header clearfix">
                <span class="order-no">订单号: {{ $refund_item['order_no']}}</span>
                <span class="order-status-text">{{ $refund_status[$refund_item['status']] }}</span>
            </div>
            <div class="order-item-content clearfix">
                <div class="img lazy" style="height: 80px;overflow: hidden;min-height: unset;">
                    <img src="{{ $refund_item['product']['image'] or '' }}" class="lazyload" />
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
                    </div>
                </div>
            </div>
            <div>
                <span style="color: #666;font-size: 12px;">申请理由：</span><span class="text-red" style="font-size: 14px">{{ $refund_item['reason'] }}</span>
            </div>
            @if(!empty($refund_item['handel_reason']))
                <div>
                    <span style="color: #666;font-size: 12px;">解决方法：</span><span class="text-red" style="font-size: 14px">{{ $refund_item['handel_reason'] }}</span>
                </div>
            @endif
            @if($refund_item['status'] == '0')
            <div class="operate-box" style="margin-top: 10px;">
                <a href="javascript:void(0)" class="operate-btn js-order-refund-handel" style="background-color: #ff9800;color: #fff" data-id="{{ $refund_item['id'] }}" data-order-id="{{ $refund_item['order_id'] }}">审核</a>
            </div>
            @endif
        </li>
        @endforeach
    </ul>
    <div class="waiting-load-block js-load-block" style="display: none">
        <div class="waiting-loading"></div>
        <div class="text">Loading...</div>
    </div>
</div>
@else
    <div class="no-results">
        <div class="result-img">@include('template.rote')</div>
        <div class="result-content">
            <p>没有</p>
        </div>
    </div>
@endif
@endsection
@section('scripts')
<script type="text/template" id="refund-handel-template">
    <div class="" style="padding: 10px;max-width: 95%;width: 460px">
        <form class="refund-handel-form" name="refund-handel-form">
            <input type="hidden" name="order_id" class="order_id" value="">
            <input type="hidden" name="order_refund_id" class="order_refund_id" value="">
            <div style="color: #FF9800;text-align: center;font-size: 18px">审核</div>
            <div class="form-group">
                <div class="form-group-label"><span class="text-red">*</span>操作</div>
                <select class="form-control handel_type" name="handel_type" required="required">
                    <option value="">请选择</option>
                    <option value="1">已协调处理，无需退款（拒绝）</option>
                    <option value="2">已处理，同意全额退款</option>
                </select>
            </div>
            <div class="form-group refuse_tip" style="display: none;">
                <span class="form-group text-red">拒绝退款后，需待买家确认收货才能完成本交易！</span>
                <div class="form-group-label"><span class="text-red">*</span>解决方法</div>
                <textarea rows="3" class="form-control" name="handel_reason" placeholder="请描述解决方法"></textarea>
            </div>
            <div class="form-group text-red">
                请确保与买家协商同意！已处理解决，再确认以上操作！否则，买家可以投诉，造成责任纠纷由自己负责！
            </div>
            <div>
                <a href="javascript:void(0)" class="btn btn-primary btn-block js-submit-refund-handel">提交</a>
            </div>
        </form>
    </div>
</script>
<script src="{{ Helper::asset_url('/media/scripts/view/store/order/refund.js') }}"></script>
@endsection