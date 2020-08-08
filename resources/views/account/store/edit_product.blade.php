@extends('layouts.app')
@section('header_title') {{ $title }} @endsection
@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="{{ Helper::route('account_store_products') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection
@section('styles')
<style type="text/css">
    .image-item {
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
        height: 0px;
   		padding-bottom: 20%;
   		overflow: hidden;
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
   	.image-item .image {
   		
   	}
    .image-item img {
        display: block;
        width: 100%;
        position: absolute;
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
        border-radius: 50%;
        background: #222;
    }
</style>
@endsection
@section('content')
<div class="bg-f pd-10">
	<form name="save-store-product-form" method="post" class="save-store-product-form" onsubmit="return false">
		<input type="hidden" name="id" value="{{ $product['id'] or '' }}">
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>商品名称</div>
			<input  class="form-control" name="name" maxlength="100" value="{{ isset($product['name']) ? $product['name'] : '' }}" />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>商品分类</div>
			<select name="category_id" class="form-control">
				<option value="">请选择</option>
				@if(!empty($categorys))
				@foreach($categorys as $ckey => $category)
				<option value="{{ $category['id'] }}" @if(isset($product['category_id']) && $category['id'] == $product['category_id']) selected="selected" @endif>{{ $category['name'] }}</option>
				@endforeach
				@endif
			</select>
		</div>
		<div class="form-group">
			<div class="form-group-label">商品视频(格式.mp4), 20M以内</div>
			<input type="file" class="form-control" name="video" accept="video/mp4" />
		</div>
		@if(!empty($product['video']))
		<video
		    id="my-player"
		    class="video-js"
		    height=200
		    controls
		    preload="auto"
		    poster="{{ Helper::asset_url('/media/images/poster.png') }}" 
		    style="width: 100%"
		    data-setup='{}'>
				<source src="{{ HelperImage::storagePath($product['video']) }}" type="video/mp4"></source>
				<p class="vjs-no-js">
					To view this video please enable JavaScript, and consider upgrading to a
					web browser that
					<a href="http://videojs.com/html5-video-support/" target="_blank">
					  supports HTML5 video
					</a>
				</p>
		</video>
		@endif
		<div style="display: none">
			<div class="form-group-label"><span class="text-red">*</span>主图</div>
			<ul class="image-list js-product-image-list">
				@if(!empty($product_images))
			        @foreach($product_images as $pkey => $p_img)
			        @if($p_img['type'] == 'main')
					<li class="image-item product-image-item" data-id="{{ $p_img['id'] }}" data-image-path="{{ $p_img['image'] }}">
						<div class="image">
							<img src="{{ HelperImage::storagePath($p_img['image']) }}"  />
						</div>
			            <a class="remove-item js-remove-product-image" data-id="{{ $p_img['id'] }}" data-product-id="{{ $product['id'] }}" title="删除">删除</a>
					</li>
					@endif
			        @endforeach
		        @endif
		        <li class="image-item image-item-add js-add-product-image">
		       		<span class="add-box">+</span>
		    	</li>
			</ul>
			<div class="form-group text-red">
				温馨提示：禁止上传 淫秽、色情低俗、迷信、谣言、暴力、血腥恐怖、政治敏感、侵犯他人权益等国家法律禁止的图片、视频、链接，否则将依法关闭权限或账号。并负法律责任。
			</div>
		</div>
		<div class="image-file main-image-file" style="display: none"></div>
		<div style="display: block;">
			<div class="form-group-label"><span class="text-red">*</span>规格属性</div>
			<div @if($type == 'edit') style="display: none"@endif>
				<input type="checkbox" checked="checked" class="attributes_checked color_checked" data-value="color" />颜色
				<input type="checkbox" checked="checked" class="attributes_checked size_checked" data-value="size" />规格
			</div>

			<div style="overflow-x:scroll;width: 100%">
				<div>注：设置共享积分促进分享，提供销量</div>
				<table style="margin: 10px 0px;width: 600px">
					<thead>
						<tr>
							<td width="50">图片</td>
							@if($type == 'add' || in_array('color', $attribute_option))
							<td class="sku_color_td">颜色</td>
							@endif
							@if($type == 'add' || in_array('size', $attribute_option))
							<td class="sku_size_td">规格</td>
							@endif
							<td>原价</td>
							<td>活动价</td>
							<td style="color: #f00">共享积分</td>
							<td>运费</td>
							<td>库存</td>
							<td>操作</td>
						</<tr>
					</thead>
					<tbody class="sku-list-box">
						@if(!empty($product_skus))
						@foreach($product_skus as $skey => $sku)
						<tr class="sku-list-item">
							<input type="hidden" class="sku_id" value="{{ $sku['id'] }}">
							<td style="border: 1px solid #e2e2e2" class="sku_image" data-image-path="{{ $sku['image'] }}">
								<img src="{{ $sku['image_link'] or '' }}" width="40" height="40" style="background-color: #f5f5f5" />
								<p><a href="javascript:void(0)" style="text-decoration: underline;color: #f00">上传</a></p>
							</td>
							@if($type == 'add' || in_array('color', $attribute_option))
							<td class="sku_color_td"><input type="text" class="form-control sku_color" style="padding: 0px 1px" placeholder="颜色" value="{{ $sku['attribute']['color']['option_value'] or '' }}" /></td>
							@endif
							@if($type == 'add' || in_array('size', $attribute_option))
							<td class="sku_size_td"><input type="text" class="form-control sku_size" style="padding: 0px 1px" placeholder="规格" value="{{ $sku['attribute']['size']['option_value'] or '' }}" /></td>
							@endif
							<td><input type="text" class="form-control sku_market_price" style="padding: 0px 1px" placeholder="原价" value="{{ $sku['market_price'] }}" onkeyup="clearNoNum(this)" /></td>
							<td><input type="text" class="form-control sku_price" style="padding: 0px 1px" placeholder="活动价" value="{{ $sku['price'] }}" onkeyup="clearNoNum(this)" /></td>
							
							<td><input type="text" class="form-control sku_share_integral" style="padding: 0px 1px;color: #f00" placeholder="共享积分" value="{{ $sku['share_integral'] }}" onkeyup="clearNoNum(this)" /></td>
							<td><input type="text" class="form-control sku_shipping" style="padding: 0px 1px" placeholder="运费" value="{{ $sku['shipping'] }}" onkeyup="clearNoNum(this)" /></td>
							<td><input type="number" min="0" class="form-control sku_stock" onkeyup="clearNoInt(this)" style="padding: 0px 1px" placeholder="库存" value="{{ $sku['stock'] }}" /></td>
							<td><a href="javascript:void(0)" class="js-remove-sku-item" data-product-id="{{ $product['id'] }}" style="font-size: 24px;padding: 0px 2px">×</a></td>
						</tr>
						@endforeach
						@else
						<tr class="sku-list-item">
							<td style="border: 1px solid #e2e2e2" class="sku_image">
								<img src="" width="40" height="40">
								<p><a href="javascript:void(0)" style="text-decoration: underline;color: #f00">上传</a></p>
							</td>
							@if($type == 'add' || in_array('color', $attribute_option))
							<td class="sku_color_td"><input type="text" class="form-control sku_color" style="padding: 0px 1px" placeholder="颜色" /></td>
							@endif
							@if($type == 'add' || in_array('size', $attribute_option))
							<td class="sku_size_td"><input type="text" class="form-control sku_size" style="padding: 0px 1px" placeholder="规格" /></td>
							@endif
							<td><input type="text" class="form-control sku_market_price"  onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="原价" /></td>
							<td><input type="text" class="form-control sku_price" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="活动价" /></td>
							<td><input type="text" class="form-control sku_share_integral"  onkeyup="clearNoNum(this)" style="padding: 0px 1px;color: #f00" placeholder="共享积分" /></td>
							<td><input type="text" class="form-control sku_shipping" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="运费" /></td>
							<td><input type="text" class="form-control sku_stock" onkeyup="clearNoInt(this)" style="padding: 0px 1px" placeholder="库存" /></td>
							<td><a href="javascript:void(0)" class="js-remove-sku-item" style="font-size: 24px;padding: 0px 2px">×</a></td>
						</tr>
						@endif
					</tbody>
				</table>
				<div class="text-red">
					温馨提醒：平台有安装价格设置和调整幅度限制系统，请慎重填写第一次的价格！
				</div>
			</tr>
			<div class="text-center" style="padding: 10px 0px">
				<a href="javascript:void(0)" class="btn btn-primary add_sku" style="width: 200px">+添加规格</a>
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>商品描述</div>
			<textarea class="form-control" name="description" rows="6">{{ isset($product['description'])  ?  $product['description'] : '' }}</textarea>
			<div>如：我们只卖正品，真诚为您服务！</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>描述详情图</div>
			<ul class="image-list js-description-image-list">
				@if(!empty($product_images))
			        @foreach($product_images as $pkey => $p_img)
			        @if($p_img['type'] == 'description')
					<li class="image-item product-image-item" data-id="{{ $p_img['id'] }}">
						<div class="image">
							<img src="{{ HelperImage::storagePath($p_img['image']) }}"  />
						</div>
			            <a class="remove-item js-remove-product-image" data-id="{{ $p_img['id'] }}" data-product-id="{{ $product['id'] }}" title="删除">删除</a>
					</li>
					@endif
			        @endforeach
		        @endif
		        <li class="image-item image-item-add js-add-description-image">
		       		<span class="add-box">+</span>
		    	</li>
			</ul>
		</div>
		<div class="image-file product-description-image-file" style="display: none"></div>
		<div class="form-group-list clearfix" style="display: none">
			<div class="form-group">
				<div class="form-group-label">客服</div>
				<input  class="form-control" readonly="readonly" value="{{ $store['contact_user_name'] }}" />
			</div>
			<div class="form-group">
				<div class="form-group-label">电话</div>
				<input  class="form-control" readonly="readonly" value="{{ $store['contact_phone'] }}" />
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>是否接受有赏积分支付</div>
			<select  name="integral_pay" class="form-control">
				<option value="">请选择</option>
				<option value="1" @if(!empty($product) && $product['integral_pay'] == '1') selected="selected" @endif>是</option>
				<option value="0" @if(!empty($product) && $product['integral_pay'] == '0') selected="selected" @endif>否</option>
			</select>
			<p style="font-size: 12px;line-height: 20px;margin-top: 5px;color: #666666"> 
				有赏积分，可用于自营商城和支持有赏积分支付的店铺购物
				关闭后，客户不能使用积分付款<br />
			    开启后,客户可以使用积分付款，结算方式根据客户付款情况:<br />
				如:商品价格是100，客户用20元有赏积分 + 80元现金支付<br />
				完成交易到账 = 20有赏积分 + 78赏金，平台服务点数从现金部分优先扣取，点数为商品交易额的 <span class="text-red" style="font-size: 14px">2%</span>
		    </p>
		</div>
		<div>
			<input type="submit" class="btn btn-primary btn-block btn-submit" value="保存"  />
		</div>
	</form>
</div>
<form class="upload-form product-image-upload-form" method="post" enctype="multipart/form-data" style="display: none">
    <input @if($type == 'add') name="image[]" @else name="image" @endif accept="image/*" type="file" class="upload-file product-image-file product-image-upload-file" />
    <input type="hidden" name="product_id" value="{{ $product['id'] or '' }}">
    <input type="hidden" name="type" value="main">
</form>
<form class="upload-form description-image-upload-form" method="post" enctype="multipart/form-data" style="display: none">
    <input  name="description_image[]" accept="image/*" type="file" class="upload-file description-image-file description-image-upload-file" />
    <input type="hidden" name="product_id" value="{{ $product['id'] or '' }}">
</form>
<form class="upload-form video-upload-form" method="post" enctype="multipart/form-data" style="display: none">
    <input  name="video[]" accept="image/*" type="file" class="upload-file video-file video-upload-file" />
    <input type="hidden" name="product_id" value="{{ $product['id'] or '' }}">
</form>
<script type="text/template" id="product-image-template">
    <li class="image-item product-image-item" data-file-id="{file_id}" data-image-path="{image_path}">
		<div class="image">
			<img src="{image}" />
		</div>
        <a class="remove-item js-remove-product-image" title="删除">删除</a>
	</li>
</script>
<script type="text/template" id="product-image-template">
    <li class="image-item product-description-image-item" data-id="{$image_id}" data-file-id="{file_id}">
		<div class="image">
			<img src="{image}" />
		</div>
        <a class="remove-item js-remove-product-image" title="删除">删除</a>
	</li>
</script>
<script type="text/template" id="product-attributes-template">
    <li class="product-attributes-item" style="border: 1px solid #eeeeee;padding: 10px;margin-bottom: 5px">
		<input type="text"  class="form-control attributes_input" placeholder="属性" style="width: 120px;display: inline-block;" />
		<input type="text"  class="form-control attributes_value_input" placeholder="属性值" style="width: 120px;display: inline-block;" />
		<ul class="attributes_value_list" style="padding: 10px">
			
		</ul>
	</li>
</script>
<script type="text/template" id="product-image-list-template">
	<div style="padding: 10px;">
    	<div class="form-group-label"><span class="text-red">*</span>主图(<span class="text-red">请上传1：1正方形的产品主图</span>)</div>
	    <div class="product-image-list-box">
			<ul class="image-list product-image-list">
		        
			</ul>
		</div>
		<div class="form-group text-red">
			温馨提示：禁止上传 淫秽、色情低俗、迷信、谣言、暴力、血腥恐怖、政治敏感、侵犯他人权益等国家法律禁止的图片、视频、链接，否则将依法关闭权限或账号。并负法律责任。
		</div>
	</div>
</script>
<script type="text/template" id="sku-item-template">
	<tr class="sku-list-item">
		<td style="border: 1px solid #e2e2e2" class="sku_image">
			<img src="" width="40" height="40">
			<p><a href="javascript:void(0)" style="text-decoration: underline;color: #f00">上传</a></p>
		</td>
		@if($type == 'add' || in_array('color', $attribute_option))
		<td class="sku_color_td"><input type="text" class="form-control sku_color" style="padding: 0px 1px" placeholder="颜色" /></td>
		@endif
		@if($type == 'add' || in_array('size', $attribute_option))
		<td class="sku_size_td"><input type="text" class="form-control sku_size" style="padding: 0px 1px" placeholder="规格" /></td>
		@endif
		<td><input type="text" class="form-control sku_market_price"  onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="原价" /></td>
		<td><input type="text" class="form-control sku_price" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="活动价" /></td>
		<td><input type="text" class="form-control sku_share_integral"  onkeyup="clearNoNum(this)" style="padding: 0px 1px;color: #f00" placeholder="共享积分" /></td>
		<td><input type="text" class="form-control sku_shipping" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="运费" /></td>
		<td><input type="text" class="form-control sku_stock" onkeyup="clearNoInt(this)" style="padding: 0px 1px" placeholder="库存" /></td>
		<td><a href="javascript:void(0)" class="js-remove-sku-item" style="font-size: 24px;padding: 0px 2px">×</a></td>
	</tr>
</script>
<div style="display: none" class="file_input">
	
</div>
@endsection
@section('footer')@endsection
@section('scripts')
<script type="text/javascript">
	function clearNoNum(obj){
		 obj.value = obj.value.replace(/[^\d.]/g,"");  //清除“数字”和“.”以外的字符  
	}
	function clearNoInt(obj){
		 obj.value = obj.value.replace(/[^\d]/g,"");  //清除“数字”和“.”以外的字符  
	}
</script>
@if($type == 'add')
<script type="text/javascript">
	var save_type = 'add';
</script>
@else
<script type="text/javascript">
	var save_type = 'edit';
</script>
@endif
<script src="{{ Helper::asset_url('/media/scripts/view/store/edit_product.js') }}"></script>
@endsection

