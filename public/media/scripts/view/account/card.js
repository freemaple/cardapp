//基础加载
require(['zepto', 'base', 'mylayer', 'validate'], function ($, md_base, mylayer, validate) {
    var card_edit = {
        init: function(){
            var self = this;
            //启用名片
            $(document).on("click", ".js-card-enable", function(){
                var elem = $(this);
                var id = elem.attr('data-id');
                var enable = '';
                if(elem.attr('data-enable') == '1'){
                   enable = 0;
                } else {
                    enable = 1;
                }
                var confirm  = $(this).attr('data-confirm');
                mylayer.showConfirm(confirm, function(){
                    $.ajaxPost('/api/card/setting', {'id': id, 'enable': enable}, function(result){
                        if(result.code == 'Success'){
                            window.location.reload();
                        } else if(result.message != ''){
                            mylayer.showTip('error', result.message);
                        }
                    });
                });
            });
            //设置默认名片
            $(document).on("click", ".js-card-setdefault", function(){
                var elem = $(this);
                var id = elem.attr('data-id');
                var confirm  = $(this).attr('data-confirm');
                mylayer.showConfirm(confirm, function(){
                    $.ajaxPost('/api/card/setdefault', {'id': id}, function(result){
                        if(result.code == 'Success'){
                            window.location.reload();
                        } else if(result.message != ''){
                            mylayer.showTip('error', result.message);
                        }
                    });
                });
            });
        }
    };
    var card_save = {
        init: function(){
            var self = this;
            //保存名片
            $('.js-save-card').on("click", function(){
                $(".card-save-form").find('.btn-submit').click();
            });
            //设置表单验证
            var validator = new FormValidator('card-save-form', self.cardValid, function(errors, event) {
                var form = $(event.target);
                form.find('.errormsg').html('');
                if (errors.length > 0) {
                    self.showValidatorError(errors, form);
                    return false;
                }
                var action = form.attr('data-action');
                var data = form.serializeObject();
                var weixin_qr = $(".qr-image").attr('src');
                if(weixin_qr){
                    data['weixin_qr'] = weixin_qr;
                }
                var layer = mylayer.showLoad(true);
                $.ajaxPost(action, data, function(result){
                    mylayer.hideLoad();
                    if(result.code == 'Success'){
                        mylayer.showTip(result.message, 3000, 'success');
                        var link = form.attr('data-custom-link');
                        window.location.href = link;
                    } else {
                        $.showRequestError(result);
                    }
                }, function(result){
                    mylayer.hideLoad();
                    $.showRequestError(result);
                });
                return false;
            });
            $(".card-save-form").on("submit", function(){
                return false;
            });
            //二维码
            $(".js-change-qt").on("click", function(){
                $(".qr-upload-file").click();
            })
            //选择图片后上传预览
            $(".qr-upload-form").on("change", function(event){
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                if(elem.hasClass("qr-upload-file")){
                    try{
                        var files = elem[0].files;
                        if(files && files.length > 0){
                            //Verify that the file type
                            if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg']) == -1){
                                mylayer.showTip(tipMessage.upload_image_format_tip, 5000, "error");
                                return false;
                            }
                            if(files[0].size){
                                var sm = files[0].size / (1024 * 1024);
                                if(sm > 3){
                                    mylayer.showTip(tipMessage.upload_maximum_tip, 5000, "error");
                                    return false;
                                }
                            }
                        }
                        var file = files[0];
                        var reader = new FileReader();
                        // 将文件以Data URL形式进行读入页面
                        reader.readAsDataURL(file);
                        reader.onload = function(f){
                            var src = this.result;
                            var img = $('.qr-image');
                            $('.qr-image').attr('src', src);
                        }
                    }
                    catch(e){}
                }
            });
            require(['jquery', 'distpicker'], function($j){
                var distpicker1 = $j("#distpicker1");
                distpicker1.distpicker({
                    province: distpicker1.attr('data-province'),
                    city: distpicker1.attr('data-city'),
                    district: distpicker1.attr('data-district')
                });
            });
            //地图
            if(typeof BMap != 'undefined'){
                //导航
                $(".js-show-map").on("click", function(){
                    if($(".layer-map").size() > 0){
                        var layer = $(".layer-map");
                        mylayer.showLayer(layer);
                    } else {
                        var content = $("#map-template").html();
                        mylayer.init({
                            content: content,
                            close: false,
                            class_name: "layer-map no-remove",
                            position: 'top',
                            success: function(){
                                map.init();
                            }
                        });
                    }
                    var address = self.getAddress();
                    map.init(address);
                });
                //位置
                map.autoLocation(function(address){
                    $(".address_street").val(address.address_street);
                    require(['jquery', 'distpicker'], function($j){
                        $j("#distpicker1").distpicker('destroy')
                        $j("#distpicker1").distpicker(address);
                    });
                });
            }
        },
        //验证规则
        cardValid: [
            {
                name: 'name',
                rules: 'required|is_letter',
                message: {
                    required: '请输入名片名称！'
                }
            }, {
                name: 'organization',
                rules: 'is_letter',
                message: {
                    required: '请输入公司名称'
                }
            }, {
                name: 'department',
                rules: 'is_letter',
            }, {
                name: 'position',
                rules: 'is_letter'
            }, {
                name: 'province',
                rules: 'required',
                message: {
                    required: '请输入省'
                }
            }, {
                name: 'city',
                rules: 'required',
                message: {
                    required: '请输入城市'
                }
            }, {
                name: 'district',
                rules: 'required',
                message: {
                    required: '请输入区'
                }
            }
        ],
         //验证提示
        showValidatorError: function(errors, form){
            for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
                var elem = $(errors[i].element);
                if(elem.next('.errormsg').size() == 0){
                    elem.after('<div class="errormsg"></div>');
                }
                elem.next('.errormsg').html(errors[i].message);
            }
            var top = form.offset().top;
            window.scrollTo(0, top);
        },
        getAddress: function(){
            var province = $(".province").val();
            var city = $(".city").val();
            var district = $(".district").val();
            var address_street = $(".address_street").val();
            var address = province + city + district + address_street;
            return address;
        }
    };
    var card_custom = {
        init: function(){
            var self = this;
            //选择背景
            $(document).on("click", ".js-show-select-background", function(){
                if($(".layer-select-background").size() > 0){
                    var layer = $(".layer-select-background");
                    mylayer.showLayer(layer);
                    typeof callback == 'function' && callback();
                } else {
                    var content = $("#background-select-template").html();
                    mylayer.init({
                        content: content,
                        close: false,
                        class_name: "layer-select-background layer-top left-to-top",
                        position: 'top'
                    });
                }
            });
            //选择背景
            $(document).on("click", ".js-background-item", function(){
                var src = $(this).attr('data-src');
                var id = $(this).attr('data-id');
                var layer = $(".layer-select-background");
                mylayer.hideLayer(layer);
                $(".theme-bg").css('background-image', "url(" + src +")");
                $(".background_image").val(src);
            });
            //自定义保存图片
            $(".card-custom-save").on("click", function(){
                var form = $(".card-custom-form");
                var data = form.serializeObject();
                $.ajaxPost('/api/card/custom/save', data, function(result){
                    if(result.code == 'Success'){
                        window.location.reload();
                    }
                });
            });
            //编辑音乐
            $(".card-music-edit").on("click", function(){
                var content = $("#edit-music-template").html();
                var src = $(".card_music").val();
                mylayer.init({
                    content: content,
                    close: false,
                    class_name: "layer-edit-music",
                    position: 'center'
                });
                $(".music_link").val(src);
            });
            //保存音乐
            $(document).on("click", ".save-music", function(){
                var link = $('.music_link').val();
                var layer = $(".layer-edit-music");
                mylayer.hideLayer(layer);
                $(".card_music").val(link);
                $('#card-music-audio').attr('src', link);
            });
            //播放音乐
            $(document).on("click", ".ic-music-auto", function(){
                var elem = $(this);
                if(elem.attr('data-play') == '1'){
                    self.stop();
                    $(this).removeAttr('data-play');
                    elem.removeClass('play');
                } else {
                    self.music();
                    $(this).attr('data-play', '1');
                    elem.addClass('play');
                }
            });
            //图标
            $(".simple-microlink-item svg").attr('fill', function(){
                return '#ffffff';
            });
            //图像上传
            $(".js-avatar-edit").on("click", function(){
                $(".avatar-upload-file").click();
            });
            //选择图片后上传预览
            $(".avatar-upload-file").on("change", function(event){
                var form = $('.avatar-upload-form');
                var elem = $(this);
                try{
                    var files = elem[0].files;
                    if(files && files.length > 0){
                        //Verify that the file type
                        if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg']) == -1){
                            mylayer.showTip(tipMessage.upload_image_format_tip, 5000, "error");
                            return false;
                        }
                        if(files[0].size){
                            var sm = files[0].size / (1024 * 1024);
                            if(sm > 3){
                                mylayer.showTip(tipMessage.upload_maximum_tip, 5000, "error");
                                return false;
                            }
                        }
                    }
                    self.avatarUload(form);
                }
                catch(e){}
            });
        },
         //头像上传
        avatarUload: function(form){
            var self = this;
            var formData = new FormData(form[0]);
            mylayer.showLoad();
            $.ajax({
                url: "/api/account/changeavatar",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(result){
                    mylayer.hideLoad();
                    self.uploadCallback(form);
                    if(result.code == "Success"){
                        window.location.reload();
                    } else {
                        $.showRequestError(result);
                    }
                },
                error: function(result){   
                    mylayer.hideLoad();
                    self.uploadCallback(form);
                    $.showRequestError(result);
                }
            });
        },
        //上传后回调
        uploadCallback: function(form){
            var file_elem = form.find('input[type=file]');
            file_elem.after(file_elem.clone().val(""));   
            file_elem.remove();
        },
        stop: function(){
            var audio = document.getElementById('card-music-audio'); 
            audio.currentTime = 0;
        },
        music: function(){
            var audio = document.getElementById('card-music-audio'); 
            if(audio != null){             
                //检测播放是否已暂停.audio.paused 在播放器播放时返回false.
                if(audio.paused)                     {                 
                    audio.play();//audio.play();// 这个就是播放  
                }else{
                    audio.pause();// 这个就是暂停
                }
            } 
        }
    };
    var card_screen = {
        init: function(){
            var self = this;
            this.productPrintDrop();
            this.mzoom();
        },
       //产品打印区域设置
        productPrintDrop: function() {
            var self = this;
            var front_currentX, front_currentY;
            var front_drop;
            var product_front_design;
            var front_zoom;
            var front_width, front_height;
            var front_design_left, front_design_top;
            //正面拖动
            $(".product_front_design").on("mousedown", function(e) {
                product_front_design = $(this);
                var ev = e || window.event;  
                front_currentX = ev.clientX - parseFloat(product_front_design.css("left"));
                front_currentY = ev.clientY - parseFloat(product_front_design.css("top"));
                front_drop = true;
                return false;
            });
            //正面缩放
            $(".design_front_zoom").on("mousedown", function(e){
                product_front_design = $(this).parent().find('.product_front_design');
                var ev = e || window.event;  
                front_currentX = ev.clientX;
                front_currentY = ev.clientY;
                front_width = product_front_design.width();
                front_height = product_front_design.height();
                front_design_left = parseFloat(product_front_design.css("left"));
                front_design_top = parseFloat(product_front_design.css("top"));
                front_zoom = true;
                return false;
            });
            //鼠标移动时
            $(document).on("mousemove", function(e) {
                var ev = e || window.event;
                var elem = $(e.target || e.srcElement);
                if(front_drop){                 
                    var front_left = ev.clientX - front_currentX;
                    var front_top = ev.clientY - front_currentY;
                    product_front_design.css({
                        'left': front_left + "px",
                        'top': front_top + "px"
                    });
                } else if(front_zoom){
                    var x = ev.clientX;
                    var y = ev.clientY;
                    var newwidth = front_width + 2 * (x - front_currentX);
                    var sc = newwidth / front_width;
                    var newheight = front_height * sc;
                    if(sc > 0){
                        var left = front_design_left - (newwidth - front_width)/2;
                        var top = front_design_top - (newheight - front_height)/2;
                        product_front_design.css({
                            'width': newwidth + "px",
                            'left': left + "px",
                            'top': top + "px"
                        });
                    }
                }
            });
            // 鼠标抬起时
            $(document).on("mouseup", function(e) {
                var ev = e || window.event;  
                front_drop = false;
                front_zoom = false;
            });
        },
        //移动端设计元素变换
        mzoom: function(){
            var self = this;
            var front_currentX, front_currentY;
            var front_drop;
            var product_front_design;
            var front_zoom;
            var front_width, front_height;
            var front_design_left, front_design_top;
            //鼠标按下时
            $(".product_front_design").on("touchstart", function(e){
                //var ev = e || window.event;
                var ev = e.targetTouches[0];  
                product_front_design = $(this);
                front_currentX = ev.clientX - parseFloat(product_front_design.css("left"));
                front_currentY = ev.clientY - parseFloat(product_front_design.css("top"));
                front_drop = true;
                return false;
            });
            //鼠标移动时
            $(document).on("touchmove", function(e) {
                var ev = e.targetTouches[0];  
                if(front_drop){                 
                    var front_left = ev.clientX - front_currentX;
                    var front_top = ev.clientY - front_currentY;
                    product_front_design.css({
                        'left': front_left + "px",
                        'top': front_top + "px"
                    });
                } else if(front_zoom){
                    var x = ev.clientX;
                    var y = ev.clientY;
                    var newwidth = front_width + 2 * (x - front_currentX);
                    var sc = newwidth / front_width;
                    var newheight = front_height * sc;
                    if(sc > 0){
                        var left = front_design_left - (newwidth - front_width)/2;
                        var top = front_design_top - (newheight - front_height)/2;
                        product_front_design.css({
                            'width': newwidth + "px",
                            'left': left + "px",
                            'top': top + "px"
                        });
                    }
                }
            });
        }
    };
    var tipMessage = {
        upload_image_format_tip: '请选择png、jpg、jpeg格式图片！',
        upload_maximum_tip: '图片文件不能超过5M'
    };
    var map = {
        init: function(address){
            // 百度地图API功能
            var map = new BMap.Map("allmap");    // 创建Map实例
            var myGeo = new BMap.Geocoder();
            if(address){
                myGeo.getPoint(address, function(point){
                    if(point){
                        map.centerAndZoom(point, 16);
                        map.addOverlay(new BMap.Marker(point));
                    }
                });
            } else {
                this.autoLocation();
            }
            //添加地图类型控件
            map.addControl(new BMap.MapTypeControl({
                mapTypes:[
                    BMAP_NORMAL_MAP,
                    BMAP_HYBRID_MAP
                ]
            }));     
            //开启鼠标滚轮缩放
            map.enableScrollWheelZoom(true);     
            map.addEventListener("click", function(e){  
                this.setLocation(e.point); 
            });
        },
        autoLocation: function(callback){
            var self = this;
            var geolocation = new BMap.Geolocation();
            geolocation.getCurrentPosition(function(r){
                if(this.getStatus() == BMAP_STATUS_SUCCESS){
                    //self.setLocation(r.point, callback);
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
                    district: district,
                    address_street: address_street
                };
                callback(address_data);
        　  });
        }
    }
    $(function () {
        card_edit.init();
        card_save.init();
        card_custom.init();
        card_screen.init();
    });
}); 
