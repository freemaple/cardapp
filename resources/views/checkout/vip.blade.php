@extends('layouts.app')

@section('styles')
<style type="text/css">
    .download-app-box {
        display: none;
    }
</style>
@endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">支付</div>
    </div>
</div>
@endsection

@section('content')
<div class="checkout-box">
    <div class="checkout-panel">
        <div class="checkout-panel-header">收获地址</div>
        @if(!empty($address))
        <div class="checkout-panel-content">
            
            <div class="address-box">
                <a href="{{ Helper::route('account_address', ['is_redirect_back' => '1']) }}">
                    <div class="current-address-content">
                        @if(!empty($address))
                        <p class="weight"><span>{{ $address['fullname'] }} （ {{ $address['phone'] }}）</span></p>
                        <p>
                            <span>{{ $address['province'] }} </span>
                            <span>,{{ $address['city'] }} </span>
                            <span>,{{ $address['district'] }}</span>
                            @if($address['town'] != '')
                            <span>,{{ $address['town'] }}</span>
                            @endif
                            @if($address['village'] != '')
                            <span>,{{ $address['village'] }}</span>
                            @endif
                        </p>
                        <p>{{ $address['address'] }}</p>
                        <p>
                            <span>邮编：{{ $address['zip'] }}</span>
                        </p>
                        @endif
                    </div>
                    <span class="to">></span>
                </a>
            </div>
        </div>
        @else
            <div style="padding: 10px 0px;text-align: center;color: #f00">
                <a class="text-red" href="{{ Helper::route('account_address', ['is_redirect_back' => '1']) }}">
                    请先选择/添加地址>>
                </a>
            </div>
        @endif
    </div>
    @if(!empty($product))
    <div class="checkout-panel">
        <div class="checkout-panel-header">赠送礼品</div>
        <div class="">
            <ul class="checkout-product-info">
                <li class="checkout-product-item clearfix">
                    <div class="img" style="width: 100%">
                        <img src="{{ $product['sku']['image'] }}">
                    </div>
                </li>
            </ul>
        </div>
    </div>
    @endif
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
<input type="hidden" id="order_no" value="{{ $order_no }}" />
@if(!empty($product))
<input type="hidden" id="product_id" gift-id="{{ $gift['id'] or '0' }}" value="{{ $product['id'] or '0' }}" />
<input type="hidden" id="address_id" value="{{ !empty($address) ? $address['id'] : '' }}" />
@endif
@endsection

@section('copyright')@endsection

@section('footer')
    <div class="checkot-footer">
        <ul class="clearfix">
            <li class="order-amount">
                <div class="box">
                    <span>总共</span>
                    <span class="total-amount-info">
                        ￥{{ $vippackage['amount'] }}
                    </span>
                </div>
            </li>
            <li class="pay-btn-block checkout-pay-submit">
                <div class="box">
                    <a href="javascript:void(0)" class="js-vip-pay">
                       支付
                    </a>
                </div>
            </li>
        </ul>
    </div>
@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/checkout/vip.js') }}"></script>
@endsection

