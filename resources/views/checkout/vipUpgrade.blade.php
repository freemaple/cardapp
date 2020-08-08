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
@if(empty($address))
<div class="bg-f pd-10">
    <form class="shipping-address-form clearfix" name="shipping-address-form" onsubmit="return false">
        <input type="hidden" name="id" value="">
        <div class="form-group-list clearfix">
            <div class="form-group">
                <label class="form-label" for="first_name">姓名<span class="text-red">*</span></label>
                <input type="text" class="form-control" required="required" name="fullname" placeholder="姓名" maxlength="50" />
            </div>
            <div class="form-group">
                <label class="form-label" for="phone">联系电话<span class="text-red">*</span></label>
                <input type="text" class="form-control" required="required" name="phone" placeholder="联系电话" maxlength="50" />
            </div>
        </div>
        <div class="form-group">
            <div class="clearfix js-position-select-box">
                <div class="position-select">
                    <select class="form-control address-select province_select"   name="province_id">
                    <option value="">请选择</option>
                    @foreach($provices as $provice)
                        <option value="{{ $provice['provice_id'] }}">{{ $provice['provice_name'] }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select city_select" name="city_id"></select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select county_select" name="district_id"></select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select town_select"  name="town_id"></select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select village_select"  name="village_id"></select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="address_line">详细地址<span class="text-red">*</span></label>
            <input type="text" class="form-control" required="required" name="address" placeholder="如：168号" maxlength="255" />
             <span>如：168号</span>
        </div>
        <div class="form-group">
            <label class="form-label" for="address_line">邮编<span class="text-red">*</span></label>
            <input type="text" class="form-control" required="required" name="zip" placeholder="邮编" maxlength="255" />
        </div>
        <div class="form-group set-default-box" style="display: none">
            <input type="checkbox" class="check is_default_check" name="is_default" value="1" checked="checked"  />默认地址
        </div>
        <div class="layer-footer-box">
            <div class="button-box"><input type="submit" class="btn btn-primary btn-block add-address js-save-address" value="添加地址" /></div>
        </div>
    </form>
</div>
@else
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
        <div class="checkout-panel-content" style="padding: 10px">
            <input type="checkbox" name="agreement" class="agreement_checkbox"  value="1" /><a href="javascript:void(0)" class="js-show-store-agreement" style="color: #f00;text-decoration: underline;cursor: pointer;">同意店铺合同协议书,请认真阅读</a>
        </div>
    </div>
    <script type="text/template" id="store-agreement-template">
        <div style="padding: 10px;">
            {!! $store_agreement['description'] !!}
        </div>
    </script>
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
@endif
<input type="hidden" id="order_no" value="{{ $order_no }}" />
@if(!empty($product))
<input type="hidden" id="product_id" gift-id="{{ $gift['id'] or '0' }}" value="{{ $product['id'] or '0' }}" />
<input type="hidden" id="product_sku_id" gift-id="{{ $product_sku_id or '0' }}" value="{{ $product_sku_id or '0' }}" />
<input type="hidden" id="address_id" value="{{ !empty($address) ? $address['id'] : '' }}" />
<input type="hidden" id="upgrade_type" value="1" />
@endif
@endsection

@section('copyright')@endsection

@section('footer')
    @if(!empty($address))
    <div class="checkot-footer">
        <ul class="clearfix">
            <li class="order-amount">
                <div class="box">
                    <span>总共</span>
                    <span class="total-amount-info">
                        ￥{{ $gift['price'] }}
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
    @endif
@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/checkout/vipUpgrade.js') }}"></script>
@endsection

