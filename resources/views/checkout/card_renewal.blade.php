@extends('layouts.app')

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">{{ $title }}</div>
    </div>
</div>
@endsection

@section('content')
<div class="checkout-box">
    <div class="checkout-box">
        <div class="checkout-panel">
            <div class="checkout-panel-content clearfix">
                <span>{{ $title }}（<span class="text-red">6</span>个名片！）</span>
                <span class="pull-right">需支付 <span class="text-red">￥ {{ $total_amount }}</span></span>
            </div>
        </div>
        <div class="checkout-panel">
            <div class="checkout-panel-header">支付方式</div>
            <div class="checkout-panel-content">
                <div class="payment-list">
                    <div class="payment-item selected" data-code="weixin">
                        <span class="weixin-logo">
                            <span class="iconfont icon-weixin-zf"></span>
                            <span>微信支付</span>
                        </span>
                        <span class="checkbox">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="order_no" value="{{ $order_no or '' }}" />
@endsection

@section('copyright')@endsection

@section('footer')
    <div class="checkot-footer">
        <ul class="clearfix">
            <li class="order-amount">
                <div class="box">
                    <span>总共:</span>
                    <span class="total-amount-info">
                        ￥{{ $total_amount }}
                    </span>
                </div>
            </li>
            <li class="pay-btn-block checkout-pay-submit">
                <div class="box">
                    <a href="javascript:void(0)" class="js-pay-card-order">
                       支付
                    </a>
                </div>
            </li>
        </ul>
    </div>
@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/checkout/card.js') }}"></script>
@endsection

