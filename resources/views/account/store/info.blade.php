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
    .image-item {
        display: inline-block;
        width: 25%;
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
    .image-item img {
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
        width: 34px;
        height: 34px;
        line-height: 34px;
        text-align: center;
        border-radius: 50%
    }
</style>
@endsection
@section('content')
@if(!empty($store) && $store['denial_reason'] != '' && $store['status'] == '-1')
	<div style="background-color: #f00;padding: 20px 10px;text-align: center;color: #fff">
		{{ $store['denial_reason'] }}
	</div>
@endif
<div class="bg-f pd-10">
	<form name="store-info-form" method="post" class="store-info-form" onsubmit="return false">
	<div class="form-group">
		<div class="form-group-label"><span class="text-red">*</span>店铺名称</div>
		<input  class="form-control" name="name" maxlength="10" value="{{ $store['name'] }}" />
	</div>
	<div class="form-group">
		<div class="form-group-label"><span class="text-red">*</span>身份证号码</div>
		<input  class="form-control" name="id_card" maxlength="20" value="{{ isset($store['id_card'])  ?  $store['id_card'] : $store['id_card'] }}" />
	</div>
	<div class="form-group-list clearfix">
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>店长姓名</div>
			<input  class="form-control" name="contact_user_name" maxlength="8" value="{{ isset($store['contact_user_name']) &&  $store['contact_user_name'] != '' ?  $store['contact_user_name'] : $user['fullname'] }}" />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>店长电话</div>
			<input  class="form-control" name="contact_phone" maxlength="20" readonly="readonly"  value="{{ isset($store['contact_phone']) &&  $store['contact_phone'] != '' ?  $store['contact_phone'] : $user['phone'] }}" />
		</div>
	</div>
    <div class="form-group">
        <div class="clearfix js-position-select-box">
            <div class="position-select">
                <select class="form-control address-select province_select" name="provice_id">
                <option value="">请选择</option>
                @foreach($provices as $provice)
                    <option value="{{ $provice['provice_id'] }}" @if(!empty($store['provice_id']) && $provice['provice_id'] == $store['provice_id']) selected="selected" @endif>{{ $provice['provice_name'] }}</option>
                @endforeach
                </select>
            </div>
            <div class="position-select">
                <select class="form-control address-select city_select" name="city_id">
                	<option value="">请选择</option>
                	@if(!empty($citys))
	                @foreach($citys as $city)
	                    <option value="{{ $city['city_id'] }}" @if(!empty($store['city_id']) && $city['city_id'] == $store['city_id']) selected="selected" @endif>{{ $city['city_name'] }}</option>
	                @endforeach
	                @endif
                </select>
            </div>
            <div class="position-select">
                <select class="form-control address-select county_select" name="district_id">
                	<option value="">请选择</option>
                	@if(!empty($countys))
	                @foreach($countys as $county)
	                    <option value="{{ $county['county_id'] }}" @if(!empty($store['district_id']) && $county['county_id'] == $store['district_id']) selected="selected" @endif>{{ $county['county_name'] }}</option>
	                @endforeach
	                @endif
                </select>
            </div>
            <div class="position-select">
                <select class="form-control address-select town_select"  name="town_id">
                	<option value="">请选择</option>
                	@if(!empty($towns))
	                @foreach($towns as $town)
	                    <option value="{{ $town['town_id'] }}" @if(!empty($store['town_id']) && $town['town_id'] == $store['town_id']) selected="selected" @endif>{{ $town['town_name'] }}</option>
	                @endforeach
	                @endif
                </select>
            </div>
            <div class="position-select">
                <select class="form-control address-select village_select"  name="village_id">
                	<option value="">请选择</option>
                	@if(!empty($villages))
	                @foreach($villages as $village)
	                    <option value="{{ $village['village_id'] }}" @if(!empty($store['village_id']) && $village['village_id'] == $store['village_id']) selected="selected" @endif>{{ $village['village_name'] }}</option>
	                @endforeach
	                @endif
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label" for="address_line">详细地址<span class="text-red">*</span></label>
        <input type="text" class="form-control" name="address" value="{{ $store['address'] }}"  maxlength="255" />
        <span>如：168号</span>
    </div>
    <div class="form-group">
		<div class="form-group-label"><span class="text-red">*</span>主营主体</div>
		<div>
			<input  class="form-control" name="business_entity_name" maxlength="6" value="{{ $store['business_entity_name'] or '' }}" />
		</div>
		<div>主营主体最多6个字, 如: 某某美发店</div>
	</div>
	<div class="form-group-list clearfix">
		<div class="form-group" style="width: 33.33%;padding-right: 10px">
			<div class="form-group-label"><span class="text-red">*</span>上传营业执照</div>
			@if(($store_edit &&  $store['status'] != '2' && $store['is_history_approval'] != '1') || $store['status'] == '-1' || $is_recert == '1')
			<input type="file" class="form-control" name="business_license_front" accept="image/*"  />
			@endif
			@if(!empty($store['business_license_front']))
			<img src="{{ HelperImage::storagePath($store['business_license_front']) }}" width="80"  />
			@endif
		</div>
		<div class="form-group" style="width: 33.33%;padding-right: 10px">
			<div class="form-group-label"><span class="text-red">*</span>身份证正面照</div>
			@if(($store_edit && $store['status'] != '2' && $store['is_history_approval'] != '1') || $store['status'] == '-1' || $is_recert == '1')
			<input type="file" class="form-control" name="id_card_front" accept="image/*"  />
			@endif
			@if(!empty($store['id_card_front']))
			<img src="{{ HelperImage::storagePath($store['id_card_front']) }}" width="80" />
			@endif
		</div>
		<div class="form-group" style="width: 33.33%">
			<div class="form-group-label"><span class="text-red">*</span>身份证反面照</div>
			@if(($store_edit &&  $store['status'] != '2' && $store['is_history_approval'] != '1')  || $store['status'] == '-1' || $is_recert == '1')
			<input type="file" class="form-control" name="id_card_back" accept="image/*"  />
			@endif
			@if(!empty($store['id_card_back']))
			<img src="{{ HelperImage::storagePath($store['id_card_back']) }}" width="80" />
			@endif
		</div>
	</div>
	<div class="form-group" style="">
		<div class="form-group-label">其他证件信息<span style="color: #f00;font-size: 14px;text-decoration: underline;" class="js-show-store-cert">(所需证件参考)</span></div>
		<ul class="image-list">
			@if(!empty($certificate_image))
	        @foreach($certificate_image as $ikey => $c_i)
			<li class="image-item certificate-image-item" data-id="{{ $c_i['id'] }}">
				<div class="image">
					<img src="{{ HelperImage::storagePath($c_i['image']) }}"  />
				</div>
				@if($store_edit)
	            <a class="remove-item js-remove-certificate-image" data-id="{{ $c_i['id'] }}" data-card-id="{{ $c_i['id'] }}" title="删除">删除</a>
	            @endif
			</li>
	        @endforeach
	        @endif
	        @if($store_edit || $is_recert == '1')
	        <li class="image-item image-item-add js-add-certificate-image">
	            <span class="add-box">+</span>
	        </li>
	        @endif
		</ul>
		<div class="image-file" style="display: none"></div>
	</div>
	<div class="form-group">
		<div class="form-group-label"><span class="text-red">*</span>预售商品描述</div>
		<textarea class="form-control" name="description" maxlength="255">{{ $store['description'] or '' }}</textarea>
	</div>
	@if($store_edit)
		<div class="form-group">
			<input type="checkbox" name="agreement" value="1" /><a href="javascript:void(0)" class="js-show-store-agreement" style="color: #f00;text-decoration: underline;cursor: pointer;">同意店铺合同协议书,请认真阅读</a>
		</div>
	@endif
	@if($store_edit)
		<div>
			<input type="submit" class="btn btn-primary btn-block btn-submit" value="提交"  />
		</div>
	@endif
	<input type="hidden" name="is_recert" value="{{ $is_recert ? '1' : '0' }}" />
	</form>
</div>
<form class="upload-form certificate-image-upload-form" method="post" enctype="multipart/form-data" style="display: none">
    <input name="certificate_image[]"  accept="image/*" type="file" class="upload-file certificate-image-file certificate-image-upload-file" />
</form>
<script type="text/template" id="certificate-image-template">
    <li class="image-item certificate-image-item" data-file-id="{file_id}">
		<div class="image">
			<img src="{image}"  />
		</div>
        <a class="remove-item js-remove-certificate-image" title="删除">删除</a>
	</li>
</script>
<script type="text/template" id="store-cert-template">
	<div style="padding: 10px;">
    	{!! $store_cert['description'] !!}
	</div>
</script>
<script type="text/template" id="store-agreement-template">
	<div style="padding: 10px;">
    	{!! $store_agreement['description'] !!}
	</div>
</script>
@endsection
@section('footer')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/store/store.js') }}"></script>
@endsection

