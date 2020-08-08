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
                    	<a class="btn btn-primary" href="{{ Helper::route('account_store') }}">查看我的店铺</a>
                	</div>
                	<div style="margin: 40px 0px 0px;text-align: center;">
			        	<img src="{{ Helper::asset_url('/media/images/service/weixin_qr.jpg') }}" width="80"  class="wx_qr_image" />
			        	<p>扫描并关注人人有赏公众号</p>
			    	</div>
				</div>
			</div>
		</div>
	</div>
	@endif
@endsection

@section('scripts')

@endsection

@section('footer')@endsection

