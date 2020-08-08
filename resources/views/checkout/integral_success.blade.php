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
					<p class="total-info">
						<span class="text">金额:</span>
			           <span class="value">
			           		￥{{ $order['amount'] }}
		        		</span>
			        </p>
			        <div class="control-group" style="padding-top: 10px">
                    	<a class="btn btn-primary" href="{{ Helper::route('account_index') }}">管理中心</a>
                	</div>
				</div>
			</div>
		</div>
	</div>
	@endif
@endsection

@section('footer')@endsection


@section('scripts')

@endsection