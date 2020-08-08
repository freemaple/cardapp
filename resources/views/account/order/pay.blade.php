@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('account_orders') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">订单号:{{ $order_detail['order_no'] }}</div>
    </div>
</div>
@endsection
@section('content')
	@if(!empty($order_detail))
	<div class="order-view-warp">
		<div class="order-view-panel">
			<div class="order-panel-header clearfix">
				订单概括
				<div class="pull-right"><span class="order-status-text">{{ $order_status[$order_detail['order_status_code']] or '' }}</span></div>
			</div>
			<div class="order-panel-content">
				<p>订单号: {{ $order_detail['order_no'] }}</p>
				<p>下单时间: {{ $order_detail['created_at'] }}</p>
				<p>总金额: <span class="text-red">￥{{ $order_detail['order_total'] }}</span></p>
				<p>产品金额: <span class="text-red">￥{{ $order_detail['order_subtotal'] }}</span></p>
				@if($order_detail['order_integral'] > 0)
					<p>积分支付: ￥{{ $order_detail['order_integral'] }}</p>
				@endif
				<p>运费: 
					<span class="text-red">
						@if($order_detail['order_shipping'] > 0)￥{{ $order_detail['order_shipping'] }}
						@else
						卖家包邮
						@endif
					</span>
				</p>
				@if($order_detail['comment'] != '')
				<p>您的留言: {{ $order_detail['comment'] or '' }}</p>
				@endif
			</div>
		</div>
		<div class="order-view-panel">
			<div class="order-panel-header clearfix">
				产品
				<div class="pull-right">@if($order_detail['is_self']) <span class="text-red">自营</span> @endif</div>
			</div>
			<div class="order-panel-content">
				<div class="order-product-list">
					@if(!empty($order_detail['order_products']))
					@foreach($order_detail['order_products'] as $opkey => $o_p)
					<div class="order-product-item clearfix">
						<div class="img">
							<a href="{{ Helper::route('product_view', [$o_p['product_id']]) }}"><img src="{{ $o_p['image'] or '' }}" /></a>
						</div>
						<div class="info">
							<div class="info-box">
								<div><a href="{{ Helper::route('product_view', [$o_p['product_id']]) }}">{{ $o_p['product_name'] }}"</a></div>
								<div class="price-info">
									<span class="price-text"> ￥{{ $o_p['price'] }}</span> × {{ $o_p['quantity'] }}
	                        	</div>
	                        	@if(!empty($o_p['spec']))
	                            <div class="spec">
	                               {{ $o_p['spec'] }}
	                            </div>
	                            @endif
							</div>
						</div>
					</div>
					@endforeach
					@endif
				</div>
			</div>
		</div>
		<div class="order-view-panel">
			<div class="order-panel-header clearfix">
				收获地址
			</div>
			<div class="order-panel-content">
				<div class="address-value">
	                <p class="weight"><span>{{ $order_detail['user_info']['fullname'] }}</span></p>
	                <p>
	                	<span>{{ $order_detail['user_info']['province'] }} </span>
	                	<span>,{{ $order_detail['user_info']['city'] }} </span>
	                	<span>,{{ $order_detail['user_info']['district'] }}</span>
	                	<span>,{{ $order_detail['user_info']['town'] }}</span>
		                @if($order_detail['user_info']['town'] != '')
			                <span>,{{ $order_detail['user_info']['town'] }}</span>
			            @endif
		                @if($order_detail['user_info']['village'] != '')
			                <span>,{{ $order_detail['user_info']['village'] }}</span>
			            @endif
	                </p>
	                <p>{{ $order_detail['user_info']['address'] }}</p>
	                <p><span>联系电话：{{ $order_detail['user_info']['phone'] }}</span></p>
	                <p>邮编：{{ $order_detail['user_info']['zip'] }}</p>
	            </div>
			</div>
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
	@else
	    <div class="no-results">
	        <div class="result-img">@include('template.rote')</div>
	        <div class="result-content">
	            <p>{{ trans('view.order.no_order_tip') }}</p>
	        </div>
	    </div>
	@endif
@endsection
@section('footer')
@if($order_detail['order_status_code'] == 'pending')
<div class="checkot-footer">
    <ul class="clearfix">
        <li class="order-amount">
        	<div class="box">
        		<span>需支付:</span>
        		<span class="total-amount-info">
        			￥{{ $payment_amount }}
        		</span>
        	</div>
        </li>
        <li class="pay-btn-block">
        	<div class="box">
	            <a href="javascript:void(0)" class="order-pay-submit">
	               支付
	            </a>
            </div>
        </li>
    </ul>
</div>
@endif
<input type="hidden" name="order_id" id="order_id" value="{{ $order_detail['id'] }}" data-order-no="{{ $order_detail['order_no'] }}" />
<input type="hidden" id="is_auto_pay" value="{{ $is_auto_pay }}">
@endsection
@section('scripts')
<script src="{{  Helper::asset_url('/media/scripts/view/account/order/pay.js') }}"></script>
@endsection

