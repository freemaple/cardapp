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
            <a href="javascript:void(0)" class="js-checkout-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">{{ $title }}</div>
    </div>
</div>
@endsection
@section('content')
@if(empty($data['address_default']))
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
	@if(!empty($data['products']))
	<div class="checkout-panel">
		<div class="checkout-panel-header">产品</div>
		<div class="checkout-panel-content">
			<ul class="checkout-product-info">
				@foreach($data['products'] as $product)
				<li class="checkout-product-item clearfix">
					<div class="img">
						<img src="{{ $product['image'] }}">
					</div>
					<div class="info">
						<div class="info-box">
							<div class="name">{{ $product['name'] }}</div>
							<div class="price-info">
								<span>￥</span>
								<span>{{ $product['goods_sku_data']['price'] }}</span>
							</div>
							@if(!empty($product['goods_sku_data']['spec']))
								<div class="sku-attr-info">
									{{ $product['goods_sku_data']['spec'] }}
								</div>
							@endif
							<div class="sku-qty">
								<span class="qty-label">数量: </span>
								<span class="qty-text">
									<input class="form-control qty-input sku-qty-value" type="number" name="qty" value="{{ $product['qty'] }}" min="1" size="12">
								</span>
							</div>
                            @if(!empty($sku_message))
                            <div class="errormsg">{{ $sku_message or ''}}</div>
                            @endif
						</div>
					</div>
				</li>
				@endforeach
			</ul>
		</div>
	</div>
	@endif
	<div class="checkout-panel">
		<div class="checkout-panel-header">收获地址</div>
		<div class="checkout-panel-content">
			<div class="address-box">
				<a href="{{ Helper::route('account_address', ['is_redirect_back' => '1']) }}">
					<div class="current-address-content">
						@if(!empty($data['address_default']))
						<p class="weight"><span>{{ $data['address_default']['fullname'] }} （ {{ $data['address_default']['phone'] }}）</span></p>
						<p>
                            <span>{{ $data['address_default']['province'] }} </span>
                            <span>,{{ $data['address_default']['city'] }} </span>
                            <span>,{{ $data['address_default']['district'] }}</span>
                            @if($data['address_default']['town'] != '')
                            <span>,{{ $data['address_default']['town'] }}</span>
                            @endif
                            @if($data['address_default']['village'] != '')
                            <span>,{{ $data['address_default']['village'] }}</span>
                            @endif
                        </p>
						<p>{{ $data['address_default']['address'] }}</p>
						<p>
                            <span>邮编：{{ $data['address_default']['zip'] }}</span>
						</p>
						@endif
					</div>
					<span class="to">></span>
				</a>
			</div>
		</div>
	</div>
	<div class="checkout-panel">
		<div class="checkout-panel-header">给卖家留言</div>
		<div class="checkout-panel-content">
			<textarea rows="1" class="form-control order-comment-value" maxlength="255" name="comment"></textarea>
		</div>
	</div>
    <div class="checkout-panel">
        <div class="checkout-panel-header">订单金额</div>
        <div class="checkout-panel-content">
            <div class="checkout-amount-detail">
               <div class="checkout-amount-item amount_product">
                    <span class="text">产品金额:</span>
                    <span class="value amount_product_value">￥ {{ $data['subtotal_amount'] }}</span>
                </div>
                <div class="checkout-amount-item amount_product">
                    <span class="text">运费:</span>
                    <span class="value amount_product_value">@if($data['shipping_amount'] > 0)￥ {{ $data['shipping_amount'] }} @else 卖家包邮 @endif</span>
                </div>
            </div>
        </div>
    </div>
	@if($is_integral_pay && $can_integral_amount > 0)
	<div class="checkout-panel">
        <div class="checkout-panel-content">
            <div class="payment-list">
                <div class="payment-item">
                    <span class="text-blue" style="font-size: 18px;">
                        <input type="checkbox"  class="use_integral_checked" @if($is_use_integral == '1') checked="checked" @endif name="" style="width: 24px;height: 24px;" /><span>积分可支付 <span>￥{{ $can_integral_amount }}</span></span>
                    </span>
                </div>
            </div>
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
<form class="order_form_data">
	@foreach ($data['products'] as $product)
	<input type="hidden" name="sku_id[]" value="{{ $product['goods_sku_id'] }}" />
	@endforeach
	<input type="hidden" name="address_id" value="{{ !empty($data['address_default']['id']) ? $data['address_default']['id'] : 0 }}">
    <input type="hidden" name="basket_code" value="{{ $basket_code }}">
    <input type="hidden" name="sid" value="{{ $sid }}">
	<input type="hidden" name="use_integral" value="{{ !empty($is_use_integral) ? $is_use_integral : 0 }}">
</form>
@endif
@endsection
@section('footer')
@if(!empty($data['address_default']))
<div class="checkot-footer">
    <ul class="clearfix">
        <li class="order-amount">
        	<div class="box">
        		<span>总共:</span>
        		<span class="total-amount-value">$ {{ $data['total_amount'] }}</span>
        	</div>
        </li>
        <li class="pay-btn-block">
        	<div class="box">
	            <a href="javascript:void(0)" class="checkout-pay-submit">
	               支付
	            </a>
            </div>
        </li>
    </ul>
</div>
@endif
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/checkout.js') }}"></script>
@endsection
