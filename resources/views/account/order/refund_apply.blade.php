@extends('layouts.app')

@section('title') 申请退换货  @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	        <div class="mobile-header-back">
	            <a href="{{ Helper::route('account_orders') }}"><span class="iconfont icon-back"></span></a>
	        </div>
	        <div class="mobile-header-title">订单号： {{ $order['order_no'] or '' }} </div>
	    </div>
	</div>
@endsection

@section('content')
	<div class="bg-f pd-10">
		<form name="refund-form" method="post" class="refund-form" onsubmit="return false">
		<input type="hidden" name="order_id" value="{{ $order['id'] or '0' }}" />
		<div style="background-color: #f00;padding: 10px;color: #fff;margin: 10px 0px;text-align: center;">
			取消订单 退货/换货
		</div>
		<div class="form-group">
			<div class="text-red">温馨提示：</div>
			<p>
				如果不是商品质量和快递破损问题，以及商家无表示免费退换货等情况。
			<p>
			<p>
				申请退换货，来回运费由买家负责，或由商家决定。
			</p>
			<p style="margin-top: 10px">操作步骤：</p>
			<p>
				1、联系商家商讨，获取退回商品地址。
			</p>
			<p>
				2、将商品安全打包好，按商家指定地址寄回，待商家收货验收。
			</p>
			<p>
				3、商家验收无误，退还应有货款或换货寄出，完成退换操作。
			</p>
		</div>
		<div class="form-group">
			<div class="form-group-label">申请理由</div>
			<textarea type="text" class="form-control" name="reason" maxlength="255" required="required" ></textarea>
		</div>
	</div>
	<div class="form-group" style="margin-top: 10px">
		<a href="{{ Helper::route('account_order_detail', $order['id']) }}" class="btn btn-default" style="color: #444;display: inline-block;width: 39%;margin-right: 1%">取消</a>
		<input type="submit" class="btn btn-primary btn-submit" style="color: #fff;display: inline-block;width: 60%;margin-right: -5px" value="提交" />
	</div>
</form>
@endsection

@section('footer')
@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/order/refund.js') }}"></script>
@endsection

