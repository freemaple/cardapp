@extends('layouts.app')

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="javascript::void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">{{ $title }}</div>
        </div>
    </div>
@endsection

@section('content')
    <div class="pd-10">
        @if(count($address_books) > 0)
        <div class="address-list-box js-address-list-box">
            <div class="js-address-list">
                @foreach($address_books as $akey => $a_item)
                <div class="address-list-item address-list-item-{{ $a_item['id'] }} @if($a_item['is_default']) selected @endif">
                    <div class="address-value">
                        <p class="weight"><span>{{ $a_item['fullname'] }}</span><span> ( {{ $a_item['phone'] }} )</span></p>
                        <p><span>{{ $a_item['province'] }}, </span><span>{{ $a_item['city'] }}, </span><span>{{ $a_item['district'] }}</span>, <span>{{ $a_item['town'] }}</span>, <span>{{ $a_item['village'] }}</span></p>
                        <p>{{ $a_item['address'] }}</p>
                        <p>{{ $a_item['zip'] }}</p>
                    </div>
                    <div>
                        <a href="javascript:void(0)" data-id="{{ $a_item['id'] }}" class="operate-btn edit-address js-edit-address" data-fullname="{{ $a_item['fullname'] }}" data-phone="{{ $a_item['phone'] }}" data-province="{{ $a_item['province_id'] }}" data-city="{{ $a_item['city_id'] }}" data-district="{{ $a_item['district_id'] }}" data-town="{{ $a_item['town_id'] }}" data-village="{{ $a_item['village_id'] }}" data-address="{{ $a_item['address'] }}" data-zip="{{ $a_item['zip'] }}" data-default="{{ $a_item['is_default'] }}">编辑</a>
                        <a href="javascript:void(0)" data-id="{{ $a_item['id'] }}" class="operate-btn remove-address js-remove-address">删除</a>
                    </div>
                    <a href="javascript:void(0)" data-id="{{ $a_item['id'] }}" class="select-address js-select-address"><span class="checkbox">✓</span></a>
                </div>
                @endforeach
            </div>
        </div>
        <div class="button-box">
            <a class="btn btn-primary btn-block add-address js-add-address">添加地址</a>
        </div>
        @else
        <div class="no-results">
            <div class="result-img">@include('template.rote')</div>
            <div class="result-content">
                <p>我皇魅力十足，赶紧完善地址资料，说不定有人偷偷送礼物哦！</p>
            </div>
            <div class="button-box"><a class="btn btn-primary btn-block add-address js-add-address">添加地址</a></div>
        </div>
        @endif
    </div>
    <script type="text/template" id="address-form-template">
        @if(!$plus_webview)
        <div class="mobile-header clearfix">
            <div class="mobile-header-box clearfix">
                <div class="mobile-header-back">
                    <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
                </div>
                <div class="mobile-header-title">地址管理</div>
            </div>
        </div>
        @endif
        <div class="address-form-box">
            <form class="shipping-address-form clearfix" name="shipping-address-form" onsubmit="return false">
                <input type="hidden" name="id" value="">
                <div class="form-group-list clearfix">
                    <div class="form-group">
                        <label class="form-label" for="first_name">姓名<span class="text-red">*</span></label>
                        <input type="text" class="form-control" required="required" name="fullname" placeholder="姓名" maxlength="50" />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">联系电话<span class="text-red">*</span></label>
                        <input type="text" class="form-control" required="required" name="phone" placeholder="联系电话" maxlength="50" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="clearfix js-position-select-box">
                        <div class="position-select">
                            <select class="form-control address-select province_select"   name="province_id">
                            <option value="">请选择</option>
                            @foreach($provices as $provice)
                                <option value="{{ $provice['provice_id'] }}">{{ $provice['provice_name'] }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="position-select">
                            <select class="form-control address-select city_select" name="city_id"></select>
                        </div>
                        <div class="position-select">
                            <select class="form-control address-select county_select" name="district_id"></select>
                        </div>
                        <div class="position-select">
                            <select class="form-control address-select town_select"  name="town_id"></select>
                        </div>
                        <div class="position-select">
                            <select class="form-control address-select village_select"  name="village_id"></select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="address_line">详细地址<span class="text-red">*</span></label>
                    <input type="text" class="form-control" required="required" name="address" placeholder="如：168号" maxlength="255" />
                    <span>如：168号</span>
                </div>
                <div class="form-group">
                    <label class="form-label" for="address_line">邮编<span class="text-red">*</span></label>
                    <input type="text" class="form-control" required="required" name="zip" placeholder="邮编" maxlength="255" />
                </div>
                <div class="form-group set-default-box">
                    <input type="checkbox" class="check is_default_check" name="is_default" value="1" checked="checked"  />默认地址
                </div>
                <div class="layer-footer-box">
                    <div class="button-box"><input type="submit" class="btn btn-primary add-address js-save-address" value="保存" /></div>
                </div>
            </form>
        </div>
    </script>
    <input type="hidden" name="back_url" id="backredirecturl" value="{{ Helper::backurl() }}">
@endsection

@section('footer')@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/address.js') }}"></script>
@endsection