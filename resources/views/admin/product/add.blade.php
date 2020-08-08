@extends('admin.template.layout')
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
        height: 100%
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
<div class="well">
	<form name="save-product-form" method="post" class="form-horizontal save-product-form" onsubmit="return false">
		<input type="hidden" name="id" value="{{ $product['id'] or '' }}">
		<div class="form-group">
            <label for="name" class="col-sm-2 control-label">Spu</label>
            <div class="col-sm-10">
                <input class="form-control" type="input" name="spu"  required="required" maxlength="255" />
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">商品名称<span class="text-red">*</span></label>
            <div class="col-sm-10">
                <input class="form-control" type="input" name="name" required="required" maxlength="255" />
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">电话号码<span class="text-red">*</span></label>
            <div class="col-sm-10">
                <input class="form-control" type="input" name="service_phone" required="required" maxlength="255" />
            </div>
        </div>
        <div class="form-group">
            <label for="is_sale" class="col-sm-2 control-label">是否上架</label>
            <div class="col-sm-10">
                <select id="is_sale" class="form-control" name="is_sale">
                    <option value="1">是</option>
                    <option value="0">否</option>
                </select>
            </div>
        </div>
		<div class="form-group">
			<label for="is_sale" class="col-sm-2 control-label">商品分类<span class="text-red">*</span></label>
			<div class="col-sm-10">
				<select name="category_id" class="form-control">
					<option>请选择</option>
					@if(!empty($categorys))
					@foreach($categorys as $ckey => $category)
					<option value="{{ $category['id'] }}" @if(isset($product['category_id']) && $category['id'] == $product['category_id']) selected="selected" @endif>{{ $category['name'] }}</option>
					@endforeach
					@endif
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label">商品视频(格式.mp4)</div>
			<input type="file" class="form-control" name="video" accept="video/mp4" />
		</div>
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
		<div class="form-group">
			<div style="display: block;">
				<div class="form-group-label"><span class="text-red">*</span>规格属性</div>
				<input type="checkbox" checked="checked" class="attributes_checked color_checked" data-value="color" />颜色
				<input type="checkbox" checked="checked" class="attributes_checked size_checked" data-value="size" />规格
				<table>
					<thead>
						<tr>
							<td width="50">图片</td>
							<td class="sku_color_td">颜色</td>
							<td class="sku_size_td">规格</td>
							<td>活动价（真正售价）</td>
							<td>原价</td>
							<td>共享积分</td>
							<td>运费</td>
							<td>库存</td>
							<td>操作</td>
						</<tr>
					</thead>
					<tbody class="sku-list-box">
						<tr class="sku-list-item">
							<td style="border: 1px solid #e2e2e2" class="sku_image"><img src="" width="40" height="40"></td>
							<td class="sku_color_td"><input type="text" class="form-control sku_color" style="padding: 0px 1px" placeholder="颜色" /></td>
							<td class="sku_size_td"><input type="text" class="form-control sku_size" style="padding: 0px 1px" placeholder="规格" /></td>
							<td><input type="text" class="form-control sku_price" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="活动价" /></td>
							<td><input type="text" class="form-control sku_market_price"  onkeyup="clearNoNum(this)"style="padding: 0px 1px" placeholder="市场价" /></td>
							<td><input type="text" class="form-control sku_share_integral"  onkeyup="clearNoNum(this)"style="padding: 0px 1px" placeholder="共享积分" /></td>
							<td><input type="text" class="form-control sku_shipping" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="运费" /></td>
							<td><input type="text" class="form-control sku_stock" onkeyup="clearNoInt(this)" style="padding: 0px 1px" placeholder="库存" /></td>
							<td><a href="javascript:void(0)" class="js-remove-sku-item" style="font-size: 24px;padding: 0px 2px">×</a></td>
						</tr>
					</tbody>
				</table>
				<div class="text-center">
					<a href="javascript:void(0)" class="btn btn-primary add_sku" style="width: 200px">+添加</a>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>店家寄语或商品描述</div>
			<textarea class="form-control" name="description" rows="6">{{ isset($product['description'])  ?  $product['description'] : '' }}</textarea>
			<div>如：我们只卖正品，真诚为您服务</div>
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>描述详情图</div>
			<ul class="image-list js-description-image-list">
		        <li class="image-item image-item-add js-add-description-image">
		       		<span class="add-box">+</span>
		    	</li>
			</ul>
		</div>
		<div class="image-file product-description-image-file" style="display: none"></div>
		<div>
			<input type="submit" class="btn btn-primary btn-block btn-submit" value="保存"  />
		</div>
	</form>
</div>
<form class="upload-form product-image-upload-form" method="post" enctype="multipart/form-data" style="display: none">
    <input @if($type == 'add') name="image[]" @else name="image" @endif accept="image/jpeg,image/jpg,image/png" type="file" class="upload-file product-image-file product-image-upload-file" />
    <input type="hidden" name="product_id" value="{{ $product['id'] or '' }}">
    <input type="hidden" name="type" value="main">
</form>
<form class="upload-form description-image-upload-form" method="post" enctype="multipart/form-data" style="display: none">
    <input  name="description_image[]" accept="image/jpeg,image/jpg,image/png" type="file" class="upload-file description-image-file description-image-upload-file" />
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
    	<div class="form-group-label"><span class="text-red">*</span>主图</div>
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
		<td style="border: 1px solid #e2e2e2" class="sku_image"><img src="" width="40" height="40"></td>
		<td class="sku_color_td"><input type="text" class="form-control sku_color" style="padding: 0px 1px" placeholder="颜色" /></td>
		<td class="sku_size_td"><input type="text" class="form-control sku_size" style="padding: 0px 1px" placeholder="规格" /></td>
		<td><input type="text" class="form-control sku_price" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="活动价" /></td>
		<td><input type="text" class="form-control sku_market_price"  onkeyup="clearNoNum(this)"style="padding: 0px 1px" placeholder="原价" /></td>
		<td><input type="text" class="form-control sku_share_integral"  onkeyup="clearNoNum(this)"style="padding: 0px 1px" placeholder="共享积分" /></td>
		<td><input type="text" class="form-control sku_shipping" onkeyup="clearNoNum(this)" style="padding: 0px 1px" placeholder="运费" /></td>
		<td><input type="text" class="form-control sku_stock" onkeyup="clearNoInt(this)" style="padding: 0px 1px" placeholder="库存" /></td>
		<td><a href="javascript:void(0)" class="js-remove-sku-item" style="font-size: 24px;padding: 0px 2px">×</a></td>
	</tr>
</script>
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
<script src="{{ Helper::asset_url('/media/admin/scripts/plugin/validate.min.js') }}"></script>
<script src="{{ Helper::asset_url('/media/admin/scripts/plugin/mylayer.js') }}"></script>
<script src="{{ Helper::asset_url('/media/admin/scripts/module/product.js') }}"></script>
@endsection

