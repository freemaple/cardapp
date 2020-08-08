@extends('layouts.app')
@section('header_title') {{ $title }} @endsection
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
    .s-product-image-item {
        display: inline-block;
        width: 20%;
        font-size: 0px;
        position: relative;
        min-height: 100px;
        background-color: #ff9800;
        overflow: hidden;
        border: 1px solid #e2e2e2;
        margin-bottom: 20px;
        font-size: 0px;
        margin-right: -4px;
    }
    .image-item-add {
        height: 150px;
    }
    .image-item-add .add-box {
        position: absolute;
        top: 50%;
        margin-top: -30px;
        text-align: center;
        color: #fff;
        font-size: 40px;
        left: 0px;
        width: 100%
    }
    
    .image-item-add a {
        color: #fff
    }
    .s-product-image-item img {
        display: block;
        margin: auto;
        width: auto;
        height: 150px;
    }
    .remove-item {
        position: absolute;
        right: 0px;
        top: 0px;
        border: 1px solid #e2e2e2;
        color: #fff;
        font-size: 12px;
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        border-radius: 50%
    }
</style>
@endsection
@section('content')
<div class="bg-f pd-10">
	<form name="store-product-form" method="post" class="store-product-form" onsubmit="return false">
		<div>
			<ul class="image-list js-product-image-list">
				@if(!empty($product_images))
			        @foreach($product_images as $pkey => $p_img)
					<li class="image-item s-product-image-item" data-id="{{ $p_img['id'] }}">
						<div class="image">
							<img src="{{ HelperImage::storagePath($p_img['image']) }}"  />
						</div>
			            <a class="remove-item js-remove-card-album" data-id="{{ $p_img['id'] }}" data-id="{{ $p_img['id'] }}" data-product-id="{{ $product['id'] }}" title="删除">删除</a>
					</li>
			        @endforeach
		        @endif
		        <li class="image-item image-item-add js-add-product-image">
		       		<span class="add-box">+</span>
		    	</li>
			</ul>
			<div>
				温馨提示：图片比例1:1,
				禁止上传 淫秽、色情低俗、迷信、谣言、暴力、血腥恐怖、政治敏感、侵犯他人权益等国家法律禁止的图片、视频、链接，否则将依法关闭权限或账号。并负法律责任。
			</div>
		</div>
		<div class="image-file">
			
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>商品名称</div>
			<input  class="form-control" name="name" maxlength="50" required="required" value="{{ isset($product['name']) ? $product['name'] : '' }}" />
		</div>
		<div class="form-group-list clearfix">
			<div class="form-group">
				<div class="form-group-label"><span class="text-red">*</span>市场价</div>
				<input  class="form-control" name="market_price" required="required"  value="{{ isset($product['market_price'])  ?  $product['market_price'] : '' }}" />
			</div>
			<div class="form-group">
				<div class="form-group-label"><span class="text-red">*</span>活动价</div>
				<input  class="form-control" name="price" required="required" value="{{ isset($product['price'])  ?  $product['price'] : '' }}" />
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>店家寄语或商品描述</div>
			<textarea class="form-control" name="description">{{ isset($product['description'])  ?  $product['description'] : '' }}</textarea>
			<div>注：我们只卖正品，真诚为您服务</div>
		</div>
		<div class="form-group">
			<div class="form-group-label">您的网店地址</div>
			<input  class="form-control" name="shop_link"  maxlength="1000" value="{{ isset($product['shop_link'])  ?  $product['shop_link'] : '' }}" />
		</div>
		<div class="form-group-list clearfix">
			<div class="form-group">
				<div class="form-group-label">客服</div>
				<input  class="form-control" readonly="readonly" value="{{ $store['contact_user_name'] }}" />
			</div>
			<div class="form-group">
				<div class="form-group-label">电话</div>
				<input  class="form-control" readonly="readonly" value="{{ $store['contact_phone'] }}" />
			</div>
		</div>
		<div>
			<input type="submit" class="btn btn-primary btn-block btn-submit" value="提交"  />
		</div>
	</form>
</div>
@endsection
@section('footer')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/store/product.js') }}"></script>
@endsection

