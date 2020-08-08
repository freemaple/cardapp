@extends('layouts.app')

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_center') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('styles')
<style type="text/css">
	.store-info-box {
		background-color: #03a9f4;
		padding: 15px 10px;
		text-align: center;
		color: #fff;
	}
	.store-info-box a {
		font-size: 0.3rem;
		color: #fff
	}
	.store-box {
		font-size: 0.26rem;
	}
	.c-span {
		padding-left: 5px;
		color: #f00
	}
	.s-btn {
		color: #00c;
		font-size: 16px;
		background-color: #fe5430;
		padding: 2px 4px;
	}
	.s-btn a {
		color: #fff;
	}
	.u-time-box {
		margin-top: 10px
	}
	.s-name-nox {
		position: absolute;
		top: 0%;
		margin-top: 12px;
		left: 0%;
		width: 100%;
		font-size: 0.44rem;
		color: #f4fc1a;
		text-align: center;
		z-index: 1
	}
	.store-info-box .s_r_btn {
		padding-top: 4px;
		color: #0fc;
		font-size: 0.28rem;
		display: inline-block;
	}
	.store-box .s_gift {
		font-size: 0.28rem;
		color: #03a9f4
	}
	.s-order-status-panel {
		margin-top: 10px;
	}
	.order-status-box {
		padding: 20px 10px 0px 10px;
		background-color: #ffffff;
		color: #444;
		margin-top: 0px;
		font-size: 
	}
	.s-panel-header {
		padding: 10px;
		background-color: #fe7589;
		color: #fff;
		width: 100%;
		font-size: 0.26rem;
		position: relative;
	}
	.s-panel-header a {
		color: #fff;
		display: block;
	}
	.s-order-status-panel .order-status-item a {
		color: #03a9f4;
		font-size: 0.24rem;
	}
	.s-panel-header .s-add-prodcut-btn {
		color: #03a9f4;
		font-size: 0.3rem;
	}
</style>
@endsection

@section('content')
<div class="bg-f pd-10">
	<div class="store-info-box">
		@if(!empty($store))
			<a href="{{ Helper::route('account_store_info') }}" style="display: block;">
				@if($store['status'] == '0')
				进入店铺身份信息认证（<span class="text-red">待提交</span>）
				@elseif($store['status'] == '-1')
				店铺身份信息未通过 ，进入修改
				<span class="c-span">>>></span>
				@elseif($store['status'] == '1')
					店铺身份信息审核中
				@elseif($store['status'] == '2')
					店铺身份信息已审核通过
					<span style="margin-left: 5px">
						<a href="{{ Helper::route('account_store_info', ['is_recert' => '1']) }}" class="s_r_btn">重新认证</a>
					</span>
				@endif
			</a>
		@else
			<span><a href="{{ Helper::route('checkout_store') }}" style="padding: 5px 10px">立即开通店铺</a></span>
		@endif
	</div>
	<div class="store-box">
		<div class="store-item">
			<div style="position: relative;">
				<a href="{{ Helper::route('store_view', [$store['id']]) }}">
					<img src="{{ Helper::asset_url('/media/images/bstore.gif') }}">
					<div class="s-name-nox">{{ $store['name'] or '我的店铺' }}</div>
				</a>
			</div>
			<div class="img">
				<a href="{{ Helper::route('store_view', [$store['id']]) }}">
					<img src="@if(empty($store['banner'])) {{ Helper::asset_url('/media/images/default_store_banner.png') }}  @else {{ HelperImage::storagePath($store['banner']) }} @endif" />
				</a>
			</div>
			<div class="name clearfix">
				<div class="pull-right">
					@if(!empty($store))
					<a class="js-store-banner-upload" href="javascript:void(0)" style="color: #03a9f4">编辑迎客封面</a>
					@endif
				</div>
			</div>
			<div class="clearfix" style="margin-top: 5px" style="background: #f5f5f5">
				<span>浏览量 ( <span class="text-info value">{{ isset($store['view_number']) ? $store['view_number'] : 0 }}</span> )</span>
				<span style="padding: 0px 5px">总销量 ( <span class="text-info value">{{ isset($store['sales_number']) ?  $store['sales_number'] : '0' }}</span> )</span>
				<span style="padding: 0px 5px">店铺收藏 ( <span class="text-info value">{{ isset($store['wish_number']) ?  $store['wish_number'] : '0' }}</span> )</span>
				<div style="padding: 10px 0px">
					<span style="">店铺荣誉 ( <span class="text-info value">{{ isset($store['rating_honor']) ?  $store['rating_honor'] : '0' }}</span> )</span>
					<span style="padding: 0px 5px">店铺评分 ( <span class="text-info value">{{ isset($store['rating']) ?  $store['rating'] : '0' }}</span> 分) </span>
				</div>
			</div>
			@if(!empty($store))
			<div class="clearfix">
				<span>店铺级别：<span class="text-info">{{ $level_text[$user['store_level']] or '' }}</span></span>
				<span style="margin-left: 10px">
					<a class="operate-btn" href="{{ Helper::route('help_catalog_doc', ['store']) }}">到商学院学习</a>
				</span>
			</div>
			@endif
			<div class="u-time-box clearfix">
				<div class="clearfix">
					<span class="pull-left" class="text-red">剩余租期{{ $expire_date }}天@if($gift_date) (<span class="s_gift">赠</span>) @endif</span>
					@if(!empty($store) && $store->expire_date != null || $expire_date > 0)
					<span class="pull-right" style=""><a href="{{ Helper::route('checkout_store') }}" class="operate-btn" style="padding: 5px 10px">续约</a></span>
					@else
					<span class="pull-right"><a href="{{ Helper::route('checkout_store') }}" class="operate-btn" style="padding: 5px 10px">开通</a></span>
					@endif
				</div>
				@if(!empty($store->expire_date))
				<div style="margin-top: 5px;font-size: 12px">租期到期时间：{{ $store->expire_date }}</div>
				@endif
			</div>
		</div>
	</div>
</div>
<div class="s-order-status-panel">
	<div class="s-panel-header">
		<a href="{{ Helper::route('account_store_orders') }}">
			我的店铺订单
			<span class="to" style="position: absolute;right: 20px">></span>
		</a>
	</div>
	<div class="order-status-box">
		<ul class="order-status-list store-order-status-list clearfix">
			@foreach($order_status_list as $status_code => $status_code_text)
			<li class="order-status-item">
				<a href="{{ Helper::route('account_store_orders', ['status_code' => $status_code]) }}">
					<div class="order-status-item-box">
						<span class="iconfont icon-order-{{ strtolower($status_code) }}">
							<span class="number order-{{ strtolower($status_code) }}-number order_status_number" style="display: none"></span>
						</span>
					</div>
					<p>
						{{ $status_code_text }}
					</p>
				</a>
			</li>
			@endforeach
			<li class="order-status-item">
				<a href="{{ Helper::route('account_store_order_refundlist') }}">
					<div class="order-status-item-box">
						<span class="iconfont icon-order-refund">
							<span class="number order_status_number order-refund-number" style="display: none;"></span>
						</span>
					</div>
					<div>
						退换单
					</div>
				</a>
			</li>
		</ul>
	</div>
</div>
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
                    	<span class="text-info">@if($product['enable'] == '1') '已上架' @else '已下架' @endif</span>
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
                </div>
            </div>
		</li>
		@endforeach
	</ul>
	<div style="text-align: center;padding: 10px 0px">
		{{ $pager }}
	</div>
	@if(!empty($store) && $expire_date > 0)
	<div style="margin: 10px auto;padding: 0px 10px">
		<a href="{{ Helper::route('account_store_product_add') }}"  class="btn btn-info btn-block">添加产品</a>
	</div>
	@endif
	<div>
        <div style="text-align: center;padding: 40px 0px">
            <p class="self_help_qr" style="display: none;">
                <img src="{{ Helper::asset_url('/media/images/self_weiqin.jpg') }}" width="100" />
            </p>
            <p>
                <a style="color: #00f;" href="javascript:void(0)" class="js-show-help-qr">网店操作部</a>
            </p>
        </div>
    </div>
	<div style="display: none">
		人人有赏仅为商家提供网页设计
		商品责任主体由该商家方负责！
	</div>
</div>
<form class="upload-form store-banner-upload-form" method="post" enctype="multipart/form-data">
    <input name="image" accept="image/*" type="file" class="upload-file store-banner-file" />
</form>
<script type="text/template" id="store-expire-template">
   	<img src="{{ Helper::asset_url('/media/images/store_expire.png') }}" width="250" />
</script>
<style type="text/css">
	.layer-store-expire .layerbox-wrapper {
		box-shadow: unset;
		border: none;
	}
	.layer-store-expire .layerbox-content {
		background-color: unset;
	}
</style>
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

