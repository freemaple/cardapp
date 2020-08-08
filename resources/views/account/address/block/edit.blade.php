<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">地址管理</div>
    </div>
</div>
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
                    <select class="form-control address-select province_select" name="province">
                    @foreach($provices as $provice)
                        <option value="{{ $provice['provice_id'] }}">{{ $provice['provice_name'] }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select city_select" name="city">
                        @foreach($provices as $provice)
                        <option value="{{ $provice['provice_id'] }}">{{ $provice['provice_name'] }}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select county_select" name="district">
                        @foreach($provices as $provice)
                        <option value="{{ $provice['provice_id'] }}">{{ $provice['provice_name'] }}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select town_select"  name="town">
                        @foreach($provices as $provice)
                        <option value="{{ $provice['provice_id'] }}">{{ $provice['provice_name'] }}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="position-select">
                    <select class="form-control address-select village_select"  name="village">
                        @foreach($provices as $provice)
                        <option value="{{ $provice['provice_id'] }}">{{ $provice['provice_name'] }}</option>
                        @endforeach 
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="address_line">地址<span class="text-red">*</span></label>
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
            <div class="button-box"><input type="submit" class="add-address js-save-address" value="保存" /></div>
        </div>
    </form>
</div>