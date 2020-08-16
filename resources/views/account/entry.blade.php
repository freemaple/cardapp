@extends('layouts.app')

@section('header')@endsection

@section('content')
    <style type="text/css">
        body {
           background-image: url("{{ Helper::asset_url('/media/images/sign_bg.jpg') }}");
           background-color: #f5f5f5;
           color: #fff;
        }
    </style>
    <div class="entry-box js-entry-box clearfix">
        <div class="entry-box-item entry-login-box">
            @if(!$is_address)
            <div style="padding: 20px 0px;text-align: center;font-weight: bold;color: #f00">马上添加收货地址，享受购物乐趣</div>
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
                    <div class="form-group set-default-box" style="display: none">
                        <input type="checkbox" class="check is_default_check" name="is_default" value="1" checked="checked" />默认地址
                    </div>
                    <div class="form-group">
                        <div class="button-box"><input type="submit" class="btn btn-primary add-address js-save-address" value="保存继续" /></div>
                    </div>
                </form>
            </div>
            @endif
            @if($is_address)
            <div class="reg-pack-panel entry-panel">
                <div class="sign-panel-content" style="margin-top: 5px">
                    <div class="vip-desc-box" style="border-bottom: 1px solid #e2e2e2">
                        <div class="vip-desc-box">
                            <div class="desc-title">抢购礼包双重赚福利说明：</div>
                            <ul>
                                <li>1、得到礼包产品</li>
                                <li>2、得到网店使用权限365天</li>
                                <li>3、得到礼包代购积分</li>
                                <li>4、得到礼包配送的麦粒</li>
                                <li>5、共享自营商城</li>
                            </ul>
                            <div>
                                温馨提示：购买礼包成功后，
                                第一步，先到【我的】页面，把礼包麦粒置换成“金麦穗”
                                第二步，点击【去完成】或商城首页【马上分享】去完成每天的分享任务！
                            </div>
                        </div>
                        <div style="margin-top: 20px">
                            <img src="/media/images/vip_gift.jpg" style="width: 100%">
                        </div>
                        <div class="form-group" style="margin-top: 20px">
                            <a class="btn btn-primary btn-block" href="{{ Helper::route('account_vipUpgrade') }}">马上购买礼包VIP</a>
                        </div>
                    </div>
                </div>
                <div class="sign-panel-content" style="margin-top: 5px;text-align: center;">
                    <div class="reg-vip-box">
                        <a class="a-link" href="{{ Helper::route('account_index') }}">跳过</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <input type="hidden" id="previous_link" name="previous_link" value="{{ $previous_link or '' }}">
    </div>
@endsection
@section('scripts')
@if(!$is_address)
<script src="{{ Helper::asset_url('/media/scripts/view/account/address.js') }}"></script>
@endif
@endsection
