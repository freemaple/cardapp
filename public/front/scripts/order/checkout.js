require(['jquery', 'lazyload', 'validate', 'mylayer', 'loginReg'], function ($, lazyload, validate, mylayer, md_loginReg) {
    var app = {};
    //页面初始化事件
    app.init = function() {
        var _this = this;
        this.basket_number = $.trim($("#basket_number").val());
        //图片延迟加载
        $(".lazyload").lazyload({threshold: -100});
        //地址事件
        this.contactEvent();
        //checkout事件
        this.checkoutEvent();
        //验证提示多语言设置
        $.validatorMessageConfig();
        if($(".layer_login.is_show").size() > 0){
            md_loginReg.showLogin();
        }
    };
    //checkout相关事件
    app.checkoutEvent = function() {
        var _this = this;
        //payment_method 列表事件委托
        $(".payment_list").on("click", function(event){
            var payment_list = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            if(elem.hasClass("payment_item")){
                var payment_item = elem;
            } else {
                var payment_item = elem.closest(".payment_item");
            }
            //判断是点击选择运输方式
            if (payment_item.size() > 0) { 
                var payment_method_check  = payment_item.find(".payment_method_check");
                payment_list.find(".shipping_method_check").removeAttr('checked');
                payment_method_check.prop("checked", 'checked');
            }
        });
        //订单提交
        $(".order_submit").on("click", function () {
            var elem = $(this);
            if($(this).hasClass('disabled')){
                return false;
            }
            mylayer.showConfirm('您是否确定申请此课程？', function(){
                var payment_method_code = "";
                var payment_method_check  = $(".payment_method_check:checked");
                if(payment_method_check.size() > 0){
                    payment_method_code = payment_method_check.attr("data-code");
                }
                _this.checkoutSubmit();
            });
        });
    };
    app.checkoutSubmit = function(){
        var _this = this;
        var flag = true;
        var contact_fullname = $.trim($(".contact_fullname").text());
        var contact_phone = $.trim($(".contact_phone").text());
        var family_address = $.trim($(".family_address").text());
        if(contact_fullname == ''){
            flag = false;
        }
        if(contact_phone == ''){
            flag = false;
        }
        if(family_address == ''){
            flag = false;
        }
        var payment_method_check  = $(".payment_method_check:checked");
        var payment_method_code = "";
        if(payment_method_check.size() == 0){
            flag = false;
            $(".payment_method_error").show();
        } else {
            payment_method_code = payment_method_check.attr("data-code");
            $(".payment_method_error").hide();
        }
        var order_student_item = $(".order_student_item");
        if(order_student_item.size() > 0){
            $(".order_student_error").hide();
        } else {
            flag = false;
            $(".order_student_error").show();
        }
        if(flag){
            var token = $.trim($("#_token").val());
            var data = {
                'basket_number': this.basket_number,
                'payment_method': payment_method_code,
                '_token': token
            };
            $(".order_submit").addClass('disabled');
            _this.post("/checkout/" + this.basket_number, data);
        } else {
            window.scrollTo(0, 0);
            var msg_alert = $('<div class="site_msg_art msg_alert error show"><div class="msg_alert_content">' + $.tran("checkout.check_checkout_info", "请完善您的订单信息") +'</div></div>').prependTo('.checkout_form');
            setTimeout(function(){
                msg_alert.remove();        
            }, 2000);
        }
    }
    //表单提交
    app.post = function post(url, params) {
        var form = document.createElement("form");
        form.action = url;
        form.method = "post";
        form.style.display = "none";
        for (var x in params) {
            var opt = document.createElement("input");
            opt.name = x;
            opt.value = params[x];
            form.appendChild(opt);
        }
        document.body.appendChild(form);
        form.submit();
        return form;
    };
    //联系信息相关事件
    app.contactEvent = function() {
        // 电话号码验证 
        $.validator.addMethod("phone", function(value, element) { 
            var tel = /^[()+0-9_\-\s]*$/; 
            return this.optional(element) || (tel.test(value)); 
        }, $.tran("valid.valid_phone", "请输入正确的电话号码")); 
        var _this = this;
        //联系信息表单验证
        $(".contact_form").validate(this.contact_valid);
        //联系信息表单事件委托
        $(".contact_form").on("click", function(event){
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            //判断是点击保存按钮
            if (elem.hasClass("save_contact")) {
                //地址验证通过
                if (form.valid()) {
                    _this.doSaveContact(form, elem);
                } 
                _this.initLayerContact();
            }
        }); 
        //地址表单事件委托
        $(".contact_form").on("keyup", function(event){
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            //判断是点击地址保存按钮
            if (elem.hasClass("form_control")) {
               _this.initLayerContact();
            }
        }); 
        //隐藏地址弹出层
        $(".hide_contact_layer").on("click", function(){
            $(".contact_layer").removeClass('show');
            $(".contact_layer_box").removeClass('show');
            $("html,body").css("overflow-y", "auto");
        });
        //地址列表事件
        $(".edit_checkout_contact").on("click", function(event) {
            var contact_fullname = $.trim($(".contact_fullname").text());
            var contact_phone = $.trim($(".contact_phone").text());
            var family_address = $.trim($(".family_address").text());
            $data = {'fullname': contact_fullname, 'phone': contact_phone, 'family_address': family_address};
            //加载地址编辑表单
            _this.initLayerContact($data);
        });
        //隐藏地址弹出层
        $(".hide_student_layer").on("click", function(){
            $(".student_layer_overlay").hide();
            $(".student_layer").removeClass('show');
            $(".student_layer_box").removeClass('show');
            $("html,body").css("overflow-y", "auto");
        });
        //联系信息表单验证
        $(".contact_student_form").validate(this.student_valid);
        //地址列表事件
        $(".edit_student").on("click", function(event) {
            var elem = $(this);
            var student_id = elem.attr("data-id");
            $.postForm(elem, '/api/account/student/load', {'student_id': student_id}, function(result){
                if(result.code == '200'){
                    var data = {};
                    data['student_id'] = student_id;
                    data['fullname'] = result.data.fullname;
                    data['sex'] = result.data.sex;
                    try{
                        var birthday = result.data.birthday;
                        var date = new Date(birthday);
                        var year = date.getFullYear();
                        var month = date.getMonth() + 1;
                        if(month < 10){
                            month = '0' + month;
                        }
                        var day = date.getDate();
                        if(day < 10){
                            day = '0' + day;
                        }
                        data['year'] = year;
                        data['month'] = month;
                        data['day'] = day;
                    } catch(e){}
                    //加载地址编辑表单
                    _this.initLayerStudent(data);
                }
            });
        });
        $(".add_student").on("click", function(){
            var data = {'sex': '1'};
            var date = new Date('1991-01-01');
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            if(month < 10){
                month = '0' + month;
            }
            var day = date.getDate();
            if(day < 10){
                day = '0' + day;
            }
            data['year'] = year;
            data['month'] = month;
            data['day'] = day;
            //加载地址编辑表单
            _this.initLayerStudent(data);
        });
         //联系信息表单事件委托
        $(".contact_student_form").on("click", function(event){
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            //判断是点击保存按钮
            if (elem.hasClass("save_student")) {
                //地址验证通过
                if (form.valid()) {
                    _this.doSaveStudent(form, elem);
                } 
                _this.initLayerStudent();
            }
        }); 
    };
    //地址表单验证规则
    app.contact_valid = {
        rules: {
            fullname: {
                required: true,
                maxlength: 50
            },
            family_address: {
                required: true,
                maxlength: 255
            },
            phone: {
                required: true,
                phone: true,
                minlength: 7,
                maxlength: 32
            }
        }
    };
    //地址表单验证规则
    app.student_valid = {
        rules: {
            fullname: {
                required: true,
                maxlength: 50
            },
            year: {
                required: true
            },
            month: {
                required: true
            },
            day: {
                required: true
            },
        }
    };
    //保存联系表单
    app.doSaveContact = function(form, elem, success) {
        var _this = this;
        if(form.valid()){
            //表单数据序列化
            var data = form.serializeObject();
            $.postForm(elem, "/api/account/contact/update", data, function(result){
                if(result.code == "200"){
                    mylayer.showMessage("success", result.message, function(){
                        window.location.reload();
                    });
                } else {
                    mylayer.showMessage("error", result.message);
                }
            });
        }
    };
    //保存联系表单
    app.doSaveStudent = function(form, elem, success) {
        var _this = this;
        if(form.valid()){
            //表单数据序列化
            var data = form.serializeObject();
            var year = data['year'];
            var month = data['month'];
            var day = data['day'];
            data['birthday'] = year + '-' + month + '-' + day;
            $.postForm(elem, "/api/account/student/save", data, function(result){
                if(result.code == "200"){
                    mylayer.showMessage("success", result.message, function(){
                        window.location.reload();
                    });
                } else {
                    mylayer.showMessage("error", result.message);
                }
            });
        }
    };
    //初始化地址弹出层
    app.initLayerContact = function(data) {
        $(".contact_layer").addClass('show');
        $(".contact_layer_box").addClass('show');
        $("html,body").css("overflow-y", "hidden");
        var layerBox_wrapper = $(".contact_layer_box").find(".layerBox_wrapper");
        layerBox_wrapper.css({
            "left": "50%",
            "margin-left": "-" + layerBox_wrapper.width() / 2 + "px", //处理水平居中
        });
        var height = mylayer.getWinHeight();
        if (layerBox_wrapper.height() < height) {
            layerBox_wrapper.css({
                "top": "50%",
                "margin-top": "-" + layerBox_wrapper.height() / 2 + "px",
            });
        } else {
            layerBox_wrapper.css({
                "top": "10px",
                "margin-top": "0px"
            });
        }
        if(data){
            //设置表单值
            this.setContactForm(data);
        }
    };
    //初始化地址弹出层
    app.initLayerStudent = function(data) {
        $(".student_layer").addClass('show');
        $(".student_layer_overlay").show();
        $(".student_layer_box").addClass('show');
        $("html,body").css("overflow-y", "hidden");
        var layerBox_wrapper = $(".student_layer_box").find(".layerBox_wrapper");
        layerBox_wrapper.css({
            "left": "50%",
            "margin-left": "-" + layerBox_wrapper.width() / 2 + "px", //处理水平居中
        });
        var height = mylayer.getWinHeight();
        if (layerBox_wrapper.height() < height) {
            layerBox_wrapper.css({
                "top": "50%",
                "margin-top": "-" + layerBox_wrapper.height() / 2 + "px",
            });
        } else {
            layerBox_wrapper.css({
                "top": "10px",
                "margin-top": "0px"
            });
        }
        if(data){
            //设置表单值
            this.setStudentForm(data);
        }
    };
    //设置地址表单数据
    app.setContactForm = function(data) {
        var form = $(".contact_form");
        var obj = form.find("[name]");
        var name;
        obj.each(function(item){
            name = $(this).attr("name");
            if($(this).hasClass('check')){
                if(data[name]){
                    $(this).prop('checked', 'checked');
                } else{
                    $(this).removeAttr('checked');
                }
            }
            $(this).val(data[name] ? data[name] : '');
        });
    };
     //设置地址表单数据
    app.setStudentForm = function(data) {
        var form = $(".contact_student_form");
        var obj = form.find("[name]");
        var name;
        obj.each(function(item){
            name = $(this).attr("name");
            if($(this).hasClass('check')){
                if(data[name]){
                    $(this).prop('checked', 'checked');
                } else{
                    $(this).removeAttr('checked');
                }
            }
            if($(this).is("select")){
                $(this).find('option[value="' + data[name] + '"]').prop("selected", 'selected');
            }
            $(this).val(data[name] ? data[name] : '');
        });
    };
    //页面加载事件
    $(function () {
        app.init();
    });
});