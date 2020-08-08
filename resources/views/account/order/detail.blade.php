@extends('layouts.app')

@section('title') 订单详情  @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	        <div class="mobile-header-back">
	            <a href="{{ Helper::route('account_orders') }}"><span class="iconfont icon-back"></span></a>
	        </div>
	        <div class="mobile-header-title">订单号： {{ $order_detail['order_no'] or '' }} </div>
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
				<p>订单状态: <span class="text-red">{{ $order_status[$order_detail['order_status_code']] or '' }}</span></p>
				@if($order_detail['comment'] != '')
				<p>您的留言: <span class="text-red">{{ $order_detail['comment'] or '' }}</span></p>
				@endif
				@if(!empty($order_detail['shipping_info']))
					@if($order_detail['shipping_info']['shipping_method'])
						<p>物流方式: {{ $order_detail['shipping_info']['shipping_method'] }}</p>
					@endif
					@if($order_detail['shipping_info']['tracknumber'])
						<p>
							物流单号: {{ $order_detail['shipping_info']['tracknumber'] }}
							<a href="javascript:void(0)" style="padding-left: 2px" class="text-info js-search-track" data-tracknumber="{{ $order_detail['shipping_info']['tracknumber'] }}">查看物流</a>
						</p>
					@endif
				@endif
				@if(!empty($order_detail['refund']))
                    @if($order_detail['refund']['status'] == '0')
                        <div class="text-red">退换货申请中
                    @endif
                    @if($order_detail['refund']['status'] == '1')
                        <div class="text-red">退款中</div>
                    @endif
                    @if($order_detail['refund']['status'] == '2')
                        <div class="text-red">已退款</div>
                    @endif
                @endif
			</div>
		</div>
		<div class="order-view-panel">
			<div class="order-panel-header clearfix">
				订单金额
			</div>
			<div class="order-panel-content">
				<div class="order-product-list">
					<p>总金额: <span class="text-red">￥{{ $order_detail['order_total'] }}</span></p>
					<p>产品金额: <span class="text-red">￥{{ $order_detail['order_subtotal'] }}</span></p>
					<p>
						运费: 
						@if($order_detail['order_shipping'] == 0)
						<span class="text-red">卖家包邮</span>
						@else
						<span class="text-red">￥{{ $order_detail['order_shipping'] }}</span>
						@endif
					</p>
					@if($order_detail['payment_amount'] > 0)
					<p>现金支付: <span class="text-red">￥{{ $order_detail['payment_amount'] }}</span></p>
					@endif
					@if($order_detail['order_integral'] > 0)
					<p>积分支付: <span class="text-red">￥{{ $order_detail['order_integral'] }}</span></p>
					@endif
					@if(!empty($order_detail['paycode']))
						<p>@lang('view.order.payment_method_text'): {{ ucwords($order_detail['paycode']) }}</p>
					@endif
					@if($order_detail['order_type'] == '1')
                    <div style="margin: 0px 0px 5px 0px">
                        <div class="text-red">订单类型：礼包产品</div>
                    </div>
                    @endif
				</div>
			</div>
		</div>
		<div class="order-view-panel">
			<div class="order-panel-header clearfix">
				产品
				<div class="pull-right">@if($order_detail['is_self'] == '1') <span class="text-red">自营</span>
				@elseif(isset($order['order_store']['name']))
				<span class="text-red">店铺：{{ $order_store['name'] }}</span>
				@endif</div>
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
	                <p class="weight">
	                	<span>{{ $order_detail['user_info']['fullname'] }}</span>
	                </p>
	                <p>
	                	<span>{{ $order_detail['user_info']['province'] }} </span>
	                	<span>,{{ $order_detail['user_info']['city'] }} </span>
	                	<span>,{{ $order_detail['user_info']['district'] }}</span>
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
	@if(!empty($order_detail))
		<div class="mobile-footer clearfix">
			<div class="mobile-footer-operate">
			    <ul class="operate-box">
		            @if($order_detail['order_status_code'] == 'pending')
		            	@if($order_detail['pay_remaining_time'] > 0)
		            	<li>
		            		<a href="{{ Helper::route('account_order_pay', $order_detail['id']) }}" class="btn btn-primary">去付款</a>
		            	</li>
		            	<li>
		            		<a href="javascript:void(0)" class="btn btn-default js-cancel-order" data-id="{{ $order_detail['id'] }}">取消订单</a>
		            	</li>
		            	@endif
		            @endif
		            @if($order_detail['order_status_code'] == 'shipped' && empty($order_detail['refund']))
		           		<li>
		           			<a  href="javascript:void(0)" class="btn btn-primary js-order-confirm-receipted" data-id="{{ $order_detail['id'] }}">确认收货</a>
		           		</li>
		            @endif
		            @if($order_detail['order_status_code'] == 'shipped' || $order_detail['order_status_code'] == 'shipping')
	                    @if(empty($order_detail['refund']) && $order_detail['order_type'] != '1')
	                    <li><a href="{{ Helper::route('account_order_refund', [$order_detail['id']]) }}" data-id="{{ $order_detail['id'] }}" class="btn btn-default">申请退换货</a></li>
	                    @endif
                	@endif
		            @if($order_detail['order_status_code'] == 'finished')
		           		<li>
		           			<a href="{{ Helper::route('account_order_reviews_add', $order_detail['id']) }}" class="btn btn-primary" data-id="{{ $order_detail['id'] }}">评论</a>
		           		</li>
		            @endif
		            @if(!Helper::isApp())
		            <li>
		            	<a href="{{ Helper::route('account_orders') }}" class="btn btn-default">返回订单列表</a>
		            </li>
		            @endif
		        </ul>
	        </div>
		</div>
	@endif
@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/order/index.js') }}"></script>
<script type="text/javascript">
    if (window.history && window.history.pushState) {
        window.addEventListener("popstate", function(){
            var referrer = document.referrer;
            if(referrer.indexOf('/checkout/') != -1){
            	window.location.href = "{{ Helper::route('account_orders') }}";
            }
        });
　　}
</script>
@endsection

