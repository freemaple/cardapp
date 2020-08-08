@extends('layouts.app')

@section('header_title') 支付成功 @endsection

@section('content')
	@if(!empty($order))
	<div class="checkout-box">
		<div class="checkout_success clearfix">
			<div class="checkout-panel">
				<div class="checkout-success-box">
					<p class="icon-box">
			           <span class="iconfont icon-success"></span>
			        </p>
					<p class="order-no">
						<span class="text">订单号:</span>
						<a href="{{ Helper::route('account_order_detail', [$order['order_no']]) }}" class="u-link text-info">{{ $order['order_no'] }}</a>
					</p>
					<p class="total-info">
						<span class="text">金额:</span>
			           <span class="value">
			           		￥{{ $order['order_total'] }}
		        		</span>
			        </p>
			        <div class="control-group">
                    	<a class="btn btn-primary" href="javascript:void(0)" onclick="linkOrder()" data-href="{{ Helper::route('account_order_detail', [$order['order_no']]) }}">查看订单</a>
                	</div>
				</div>
			</div>
		</div>
	</div>
	@endif
@endsection

@section('scripts')
<script type="text/javascript">
	function linkOrder(){
		var  link = "{{ Helper::route('account_order_detail', [$order['order_no']]) }}";
		window.location.replace(link);
		return false;
	}
</script>
@endsection

@section('footer')@endsection
