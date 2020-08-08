@extends('layouts.app')

@section('styles')
<style type="text/css">
    .btn {
        padding: 5px 10px 5px 5px;color: #f00;border: 1px solid #f00;float: right;display: inline-block;
    }
    .merchant_form {
        padding: 15px 5px 5px 5px;
        background-color: #ffffff;
        margin-bottom: 20px;
    }  
</style>

@endsection

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="{{ Helper::route('home') }}" class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">{{ $title }}</div>
        </div>
    </div>
@endsection

@section('content')
<div>
    <form class="merchant_form">
        <div class="form-group">
            <div class="clearfix js-position-select-box">
                <div class="position-select">
                    <select class="form-control address-select province_select"   name="provice_id">
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
            <div class="search-box">
                <span class="mark">
                    <i class="iconfont icon-search"></i>
                </span>
                <input type="text" class="flex pub-input" name="keyword" value="{{ $form['keyword'] or '' }}" placeholder="请输入店铺名称搜索">
            </div>
        </div>
    </form>
</div>
<div class="js-merchant-list-box" data-action="/api/merchant/list" data-page="1" style="min-height: 300px">
    <ul class="clearfix js-merchant-list">
        
    </ul>
    <div class="waiting-load-block js-load-block"  style="display: none">
        <div class="waiting-loading"></div>
        <div class="text">Loading...</div>
    </div>
</div>
<script type="text/template" id="no-list-template">
    <div class="no-results">
        <div class="result-img">@include('template.rote')</div>
        <div class="result-content">
            此宝地尚未开采,需我王来坐拥江山！<br />
        </div>
    </div>
</script>
<div id="allmap" style="display: none;"></div>
<script type="text/template" id="map-template">
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">地图</div>
        </div>
    </div>
    <div id="map"></div>
</script>
@endsection
@section('copyright', view('template.copyright'))
@section('scripts')
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=9da280703fc98372b55dc44dda8e7fad"></script>
<script type="text/javascript">
    //基础加载
    require(['zepto', 'base', 'scrollComponent', 'mylayer'], function ($, md_base, scrollComponent, mylayer) {
        var app = {
            init: function(){
                var self = this;
                var scroll_item = $(".js-merchant-list-box");
                scroll_item.addClass('loading');
                this.autoLocation(function(address_data){
                    let province = address_data['province'];
                    let province_id = 0;
                    $(".province_select option").each(function(key, item){
                        if($(this).text() == province){
                            $(this).prop("selected", 'selected'); 
                            province_id = $(this).val();
                        }
                    });
                    if(province_id > 0){
                        self.getCity(province_id, function(citys){
                            self.cityCallback(citys, address_data);
                        });
                    }
                    self.initMerchantList(address_data);
                });
                //滚动加载
                scrollComponent.init();
                scrollComponent.setScrollItem(scroll_item);
                scrollComponent.setCallback(function(view, scroll_item){
                    self.scrollLoadCallback(view, scroll_item);
                });
                $(".merchant_form").on("submit", function(){
                    var form = $(".merchant_form");
                    var data = form.serializeObject();
                    self.initMerchantList(data);
                    return false;
                })
                $(document).on("change", ".address-select", function(){
                    var form = $(".merchant_form");
                    var data = form.serializeObject();
                    self.initMerchantList(data);
                });
                $(document).on("click", ".jsLocation", function(){
                    var elem = $(this);
                    var province = elem.attr('data-province');
                    var city = elem.attr('data-city');
                    var district = elem.attr('data-district');
                    var city = elem.attr('data-city');
                    var town = elem.attr('data-town');
                    var village = elem.attr('data-village');
                    var address_street = elem.attr('data-address');
                    var address = province + city + district + town + village + address_street;
                    var store_name = $(this).attr('data-store-name');
                    var store_data = {
                        'city': city,
                        'store_name': store_name
                    }
                    if(address == null){
                        mylayer.showTip('用户未设置地址！', 5000, 'error');
                        return false;
                    }
                    self.goLocation(address, store_data);
                });
            },
            getCity: function(province_id, callback){
                var self = this;
                if(window.sessionStorage){
                    var citys = window.sessionStorage.getItem('citys:' + province_id);
                    if(citys){
                        citys = JSON.parse(citys);
                        callback(citys);
                        return false;
                    }
                }
                $.ajaxGet('/api/position/getCity', {'province_id': province_id}, function(result){
                    callback(result.data);
                    if(window.sessionStorage){
                        let l_data = JSON.stringify(result.data);
                        window.sessionStorage.setItem('citys:' + province_id, l_data);
                    }
                });
            },
            cityCallback: function(data, address_data){
                var self = this;
                var options = '<option value="">请选择</option>';
                if(data){
                    $.each(data, function(key, item){
                        options += '<option value="' + item['city_id'] + '">' + item['city_name'] +'</option>';
                    });
                }
                $(".city_select").html(options);
                $(".county_select").html('');
                $(".town_select").html('');
                $(".village_select").html('');
                let city = address_data['city'];
                var city_id = 0;
                $(".city_select option").each(function(key, item){
                    if($(this).text() == city){
                        $(this).prop("selected", 'selected'); 
                        city_id = $(this).val();
                    }
                });
                if(city_id){
                    self.getCounty(city_id, function(data){
                        var options = '<option value="">请选择</option>';
                        if(data){
                            $.each(data, function(key, item){
                                options += '<option value="' + item['county_id'] + '">' + item['county_name'] +'</option>';
                            });
                        }
                        $(".county_select").html(options);
                        $(".town_select").html('');
                        $(".village_select").html('');
                    })
                }
            },
            getCounty: function(city_id, callback){
                var self = this;
                if(window.sessionStorage){
                    var countys = window.sessionStorage.getItem('countys:' + city_id);
                    if(countys){
                        countys = JSON.parse(countys);
                        callback(countys);
                        return false;
                    }
                }
                $.ajaxGet('/api/position/getCounty', {'city_id': city_id}, function(result){
                    callback(result.data);
                    if(window.sessionStorage){
                        let l_data = JSON.stringify(result.data);
                        window.sessionStorage.setItem('countys:' + city_id, l_data);
                    }
                });
            },
            initMerchantList: function(data){
                var scroll_item = $(".js-merchant-list-box");
                scroll_item.addClass('loading');
                scroll_item.find('.js-load-block').show();
                var action = scroll_item.attr('data-action');
                data['page'] = 1;
                 scroll_item.find('.js-merchant-list').html('');
                $.ajaxGet(action, data, function(result){
                     mylayer.hideLoad();
                    scroll_item.removeClass('loading');
                    scroll_item.find('.js-load-block').hide();
                    if(result.view != ''){
                        scroll_item.find('.js-merchant-list').html(result.view);
                    } else {
                        var content = $("#no-list-template").html();
                        scroll_item.find('.js-merchant-list').html(content);
                    }
                });
            },
            //滚动加载回调
            scrollLoadCallback: function(view, scrollitem){
                scrollitem.find('.js-merchant-list').append(view);
            },
            autoLocation: function(callback){
                var self = this;
                // 百度地图API功能
                var map = new BMap.Map("allmap");    // 创建Map实例
                var geolocation = new BMap.Geolocation();
                geolocation.getCurrentPosition(function(r){
                    if(this.getStatus() == BMAP_STATUS_SUCCESS){
                        self.setLocation(r.point, callback);
                        self.cpoint = r.point;
                    }     
                });
            },
            //设置地理位置
            setLocation: function(point, callback){
                var myGeo = new BMap.Geocoder();
                myGeo.getLocation(point, function(rs){
            　　　　var addComp = rs.addressComponents;
            　　　　var address = addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber;
            　　　　var state = addComp.province;
                    var city = addComp.city;
                    var district = addComp.district;
                    var address_street =  addComp.street + addComp.streetNumber;
                    var address_data = {
                        address: address,
                        province: state,
                        city: city,
                        //district: district,
                        address_street: address_street
                    };
                    callback(address_data);
            　  });
            },
            goLocation: function(address, store_data, callback){
                var self = this;
                var map = new BMap.Map("allmap");    // 创建Map实例
                var myGeo = new BMap.Geocoder();
                if(address){
                    myGeo.getPoint(address, function(point){
                        if(point){
                            let lat = point.lat;
                            let lng = point.lng;
                            let titleName = store_data.store_name;
                            let content = store_data.store_name;
                            let clat = self.cpoint.lat;
                            let clng = self.cpoint.lng;
                            if(typeof plus != 'undefined' && plus){
                                if(self.isAPP('com.tencent.map')){
                                    window.location.href = "qqmap://map/routeplan?type=drive&from=我的位置&fromcoord=" 
                                        + clat + "," + clng + "&to=" + address +"&tocoord=" + lat + "," + lng +  "&policy=0&referer=myapp";
                                        return false;
                                } if(self.isAPP('com.baidu.BaiduMap')){
                                    window.location.href = "baidumap://map/direction?origin=" + clat + "," + clng + "&destination=" + lat + "," + lng +  "&mode=driving&output=html&region=" + store_data.city;
                                    return false;
                                } 
                                else {
                                    window.open("http://apis.map.qq.com/uri/v1/routeplan?type=drive&from=我的位置&fromcoord=" 
                                        + clat + "," + clng + "&to=" + address +"&tocoord=" + lat + "," + lng +  "&policy=0&referer=myapp");
                                    return false;
                                }
                            } else {
                               window.open("qqmap://map/routeplan?type=drive&from=我的位置&fromcoord=" 
                                        + clat + "," + clng + "&to=" + address +"&tocoord=" + lat + "," + lng +  "&policy=0&referer=myapp");
                                window.setTimeout(function(){
                                   window.open("http://apis.map.qq.com/uri/v1/routeplan?type=drive&from=我的位置&fromcoord=" 
                                        + clat + "," + clng + "&to=" + address +"&tocoord=" + lat + "," + lng +  "&policy=0&referer=myapp");
                                    return false;
                                }, 3000);
                            }
                        }
                    });
                }
            },
            isAPP: function(packageName){
                 try {
                    var main = plus.android.runtimeMainActivity();
                    var packageManager = main.getPackageManager();
                    var PackageManager = plus.android.importClass(packageManager);
                    var packageInfo = packageManager.getPackageInfo(packageName, PackageManager.GET_ACTIVITIES);
                    if(packageInfo) {
                        //已安装
                        return true;
                    } else {
                        //未安装
                        return false;
                    }
                } catch(e) {
                    //未安装
                    return false;
                }
            },
            location: function(rs){
               
            }
        }
        $(function(){
            app.init();
        });
    }); 
</script>
@endsection

