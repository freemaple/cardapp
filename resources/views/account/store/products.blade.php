@extends('layouts.app')

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_store') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('styles')
<style type="text/css">
	.mobile-header-box {
		border-bottom: 0px;
	}
	.s-panel-header {
		padding: 10px;
		background-color: #ffffff;
		color: #444;
		width: 100%;
		font-size: 0.26rem;
		position: relative;
	}
	.s-panel-header a {
		color: #444;
		display: block;
	}
	.s-order-status-panel .order-status-item a {
		color: #444444;
		font-size: 0.24rem;
	}
	.s-panel-header .s-add-prodcut-btn {
		color: #00f;
		font-size: 0.28rem;
	}
</style>
@endsection

@section('content')
<div style="margin-top: 10px">
	<div class="s-panel-header">
		<span>
			我的产品
		</span>
		<span class="to" style="position: absolute;right: 20px;top: 10px;">
			<a href="{{ Helper::route('account_store_product_add') }}" class="s-add-prodcut-btn">添加产品</a>
		</span>
	</div>
	<div class="account-box" style="margin-top: 0px;display: none">
		<a style="padding: 0px 5px" href=""><span>所有产品 </span><span class="reward_amount"></span></a>
		<a style="padding: 0px 5px" href="">下架产品</a>
		<a style="padding: 0px 5px" href="javascript:void(0)">缺货产品</a>
	</div>
</div>
<div class="store-product-box">
	<ul>
		@foreach($products as $pkey => $product)
		<li class="store-product-item clearfix">
			<div class="image">
				<a href="{{ Helper::route('product_view', [$product['id']]) }}">
					<img src="{{ $product['image'] }}" width="120">
				</a>
			</div>
			<div class="info">
                <div class="info-box">
                    <div class="name"><a href="{{ Helper::route('product_view', [$product['id']]) }}">{{ $product['name'] }}</a></div>
                    <div class="price-info">
                        活动价<span class="price"> ￥{{ $product['sku']['price'] }}</span>
                        @if($product['sku']['market_price'] > 0)
                        市场价<span class="market_price"> ￥{{ $product['sku']['market_price'] }}</span>
                        @endif
                    </div>
                    <div class="info-box">
                        <span>浏览量 <span class="view_number" style="padding: 0px 5px">{{ $product['view_number'] }}</span></span>
                        <span>收藏量 <span class="view_number" style="padding: 0px 5px">{{ $product['wish_number'] }}</span></span>
                        <span>销量 <span class="view_number" style="padding: 0px 5px">{{ $product['sales_numbers'] }}</span></span>
                    </div>
                    @if($product['stock'] == 0)
                   	<span style="background-color: #f00;display: inline-block;padding: 3px 15px;color: #fff;margin-top: 2px"><span>库存不足</span></span>
                    @endif
                    <div style="margin-top: 5px;">
                    	<span class="text-red">@if($product['enable'] == '1') <span class="text-red">'已上架' </span>@else <span class="text-info">'已下架'</span> @endif
                    </div>
                    <div>
                    	<span style="margin-top: 10px;display: inline-block;">
                            <a class="share-icon js-product-codeimage"  data-id="{{ $product['id'] }}"><span class="operate-btn" style="border: 1px solid #ff9800;">分享扫码购</span></a>
                        </span>
                    </div>
                </div>
                <div class="info-button">
                	@if($product['enable'] == '1')<a href="javascript:void(0)" data-id='{{ $product['id'] }}' data-enable='0' data-confirm='确认下架此产品？' class="js-product-enable">下架</a>@else
                	<a href="javascript:void(0)" data-id='{{ $product['id'] }}' data-enable='1' data-confirm='确认上架此产品？' class="js-product-enable">上架</a>
                	@endif
                	<a href="javascript:void(0)" data-id='{{ $product['id'] }}' class="js-product-delete" data-confirm='确认删除此产品？'>删除</a>
                	<a href="{{ Helper::route('account_store_product_edit', [$product['id']]) }}">编辑</a>
                	<a href="javascript:void(0)" data-id='{{ $product['id'] }}' class="js-product-toShared" data-confirm='确认申请进入共享专区？' >申请进入我的共享店铺</a>
                </div>
            </div>
		</li>
		@endforeach
	</ul>
	<div style="text-align: center;padding: 10px 0px">
		{{ $pager }}
	</div>
</div>
<script type="text/template" id="code-image-template">
    <div style="text-align: center;">
        <img src="" class="codeImage" style="width: 400px;margin:10px auto;max-width: 95%" />
        <p style="padding: 10px 0px">长按保存</p>
    </div>
</script> 
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
@if($store_expire_tip)
	<script type="text/javascript">
		var store_expire_tip = 1;
	</script>
@endif
<script src="{{ Helper::asset_url('/media/scripts/view/store/store.js') }}"></script>
@endsection

