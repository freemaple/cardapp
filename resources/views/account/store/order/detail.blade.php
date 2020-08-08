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
				<p>
                    买家留言：<span class="text-red" style="font-size: 20px">{{ $order_detail['comment'] }}</span>
                </p>
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
						<p>
							
						</p>
					@endif
				@endif
				 @if(!empty($order_detail['refund']))
                    @if($order_detail['refund']['status'] == '0')
                        <div class="text-red">退换货申请中 <a class="operate-btn" href="{{ Helper::route('account_store_order_refundlist', ['order_id' => $order_detail['id']]) }}">去处理</a></div>
                    @endif
                    @if($order_detail['refund']['status'] == '1')
                        <div class="text-red">退款中 <a class="operate-btn" href="{{ Helper::route('account_store_order_refundlist', ['order_id' => $order_detail['id']]) }}">去查看</a></div>
                    @endif
                    @if($order_detail['refund']['status'] == '2')
                        <div class="text-red">已退款 <a class="operate-btn" href="{{ Helper::route('account_store_order_refundlist', ['order_id' => $order_detail['id']]) }}">去查看</a></div>
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
					<p>产品金额:  <span class="text-red">￥{{ $order_detail['order_subtotal'] }}</span></p>
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
					@if(!empty($order_detail['order_share_amount']))
                    <p>共享积分: <span class="text-red">￥{{ $order_detail['order_share_amount'] }}</span></p>
                    @endif
					@if(!empty($order_detail['paycode']))
						<p>@lang('view.order.payment_method_text'): <span class="text-red">{{ ucwords($order_detail['paycode']) }}</span></p>
					@endif
				</div>
			</div>
		</div>
		@if(!empty($order_detail['account_record']))
		<div class="order-view-panel">
			<div class="order-panel-header clearfix">
				<span class="text-blue">订单结算明细(已结算)</span>
			</div>
			<div class="order-panel-content">
				<div class="order-product-list">
					<p><span class="text-blue">结算总额:</span> <span class="text-red">￥{{ $order_detail['account_record']['order_profit_total'] }}</span></p>
					<p><span class="text-blue">现金结算:</span>  <span class="text-red">￥{{ $order_detail['account_record']['order_profit_amount'] }}</span></p>
					<p><span class="text-blue">积分结算:</span>   <span class="text-red">￥{{ $order_detail['account_record']['order_profit_integral'] }}</span></p>
				</div>
			</div>
		</div>
		@endif
		<div class="order-view-panel">
			<div class="order-panel-header clearfix">
				产品
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
		            @if($order_detail['order_status_code'] == 'shipping' && $order_detail['refund_status'] == '0')
		           		<li>
		           			<a  href="javascript:void(0)" class="btn btn-primary js-order-confirm-shipped" data-id="{{ $order_detail['id'] }}">确认发货</a>
		           		</li>
		            @endif
		            @if($order_detail['order_status_code'] == 'finished')
		            	@if($order_detail['is_review'] == '1')
                    	<a href="{{ Helper::route('account_store_order_reviews', $order_detail['id']) }}" class="operate-btn">查看评论</a>
                    	@endif
		            @endif
		            <li>
		            	<a href="{{ Helper::route('account_store_orders') }}" class="btn btn-default">返回订单列表</a>
		            </li>
		        </ul>
	        </div>
		</div>
	@endif
@endsection

@section('scripts')
<script type="text/template" id="shipped-template">
    <form name="order-shipped-form" class="order-shipped-form" method="post">
        <div class="shipped-box" style="padding: 10px;max-width: 95%;width: 360px">
            <div style="color: #fe7589;text-align: center;font-size: 18px">订单发货</div>
            <input type="hidden" name="order_id" class="order_id" value="{{ $order_detail['id'] }}">
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

