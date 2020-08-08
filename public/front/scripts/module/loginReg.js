define(['jquery', 'validate', 'mylayer'], function ($, validate, mylayer) {
    var app = {
        //登录框事件初始化
        init: function () {
            var _this = this;
            //登录验证
            $(".login_form").validate(this.loginValid);
            //注册验证
            $(".reg_form").validate(this.regValid);
            //忘记密码验证
            $(".forget_form").validate(this.forgetValid);
            //密码重置验证
            $(".reset_form").validate(this.resetValid);
            //激活验证
            $(".user_active_form").validate(this.activeValid);
            //登录表单事件
            $(".login_form").on("click", function (event) {
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击登录提交按钮
                if (elem.hasClass("submit_login")) {
                    _this.doLogin(form, elem);
                }
            });
            //注册表单事件
            $(".reg_form").on("click", function (event) {
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击注册提交按钮
                if (elem.hasClass("submit_reg")) {
                    _this.doReg(form, elem);
                }
            });
            //忘记密码表单事件
            $(".forget_form").on("click", function (event) {
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击忘记密码提交按钮
                if (elem.hasClass("submit_forget")) {
                    _this.doForget(form, elem);
                }
            });
            //密码重置表单事件
            $(".reset_form").on("click", function (event) {
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击登录提交按钮
                if (elem.hasClass("submit_reset")) {
                    _this.doReset(form, elem);
                }
            });
            //重新发送激活事件
            $(".send_active").on("click", function(){
                var elem = $(this);
                var activate_message = $(".re_activate_message").removeClass('show');
                $.postForm(elem, '/api/auth/useractive', {}, function(result){
                    if (result.code == "0x0000") {
                        activate_message.addClass('show').removeClass("error").addClass('success').find(".msg_alert_content").html(result.message);
                    } else if (result.message) {
                         activate_message.addClass('show').removeClass("success").addClass('error').find(".msg_alert_content").html(result.message);
                    }
                })
            });
            //用户激活表单事件
            $(".user_active_form").on("click", function(event) {
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击登录提交按钮
                if (elem.hasClass("submit_user_active")) {
                    _this.doActive(form, elem);
                }
            });
            //显示登录
            $(".show_login").on("click", function () {
                _this.showLogin();
            });
            //显示注册
            $(".show_sign").on("click", function () {
                _this.showSignUp();
            });
            //显示注册
            $(".show_sign_select").on("click", function () {
                _this.showSignUpSelect();
            });
            //显示忘记密码
            $(".show_forget_password").on("click", function () {
                _this.showForget();
            });
            //显示忘记密码
            $(".entry_box .forget_password").off("click").on("click", function () {
                $(".entry_box .forget_panel").show().siblings('.panel').hide();
            });
            //显示忘记密码
            $(".entry_box .show_login").off("click").on("click", function () {
                $(".entry_box .login_panel").show().siblings('.panel').hide();
            });
            //显示注册
            $(".entry_box .show_sign").off("click").on("click", function () {
                $(".entry_box .reg_panel").show().siblings('.panel').hide();
            });
            //关闭登录窗口
            $(".layer_login .hide_login").on("click", function () {
                _this.hideLogin();
            });
            //SNS登录小窗口打开
            var sFeatures = "height=716, width=346, scrollbars=yes, resizable=yes";
            $('.social_facebook').on('click', function () {
                window.open($(this).attr('href'), '3km', sFeatures);
                return false;
            });
            //Facebook登录小窗口打开
            var sFeatures = "height=495, width=835, scrollbars=yes, resizable=yes";
            $('.social_google').on('click', function () {
                window.open($(this).attr('href'), '3km', sFeatures);
                return false;
            });
            $(".layer_login").on("click", function (e) {
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                if (elem.hasClass('layerBox')) {
                    _this.hideLogin();
                }
            });
            //验证提示多语言设置
            $.validatorMessageConfig();
        },
        //登录验证规则
        loginValid: {
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true
                }
            }
        },
        //注册验证规则
        regValid: {
            rules: {
                nickname: {
                    required: true,
                    maxlength: 50
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 50
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 50
                }
            }
        },
        //忘记密码验证
        forgetValid: {
            rules: {
                email: {
                    required: true,
                    email: true,
                }
            }
        },
        //密码重置规则
        resetValid: {
            rules: {
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 50
                }
            }
        },
        //密码重置规则
        activeValid: {
            rules: {
                email: {
                    required: true,
                    email: true
                }
            }
        },
        //登录提交
        doLogin: function (form, elem) {
            var login_message = form.find(".login_message");
            login_message.removeClass('show');
            var _this = this;
            if (form.valid()) {
                //表单数据系列化
                var data = form.serializeObject();
                var email = $.trim(form.find(".user_email").val());
                $.postForm(elem, "/api/auth/login", data, function (result) {
                    if (result.code == "0x0000") {
                        var url = window.location.href;
                        var return_url = elem.attr('data-redirect-link');
                        if(return_url == null){
                            return_url = document.referrer;
                            var host_name = window.location.hostname + '/account';
                            if(return_url == '' || return_url.indexOf(host_name) == -1){
                                return_url = '/account/setting';
                            }
                        }
                        if(form.hasClass('is_redirect') || url.indexOf("account/entry") > -1){
                            window.location.href = return_url;
                            return false;
                        }
                        if (_this.fevent && typeof _this.fevent == "function") {
                           _this.fevent();
                        }
                        var data = result.data;
                        if(data){
                            _this.initLogin(data);
                            _this.hideLogin();
                        } else {
                            window.location.reload();
                        }
                       
                    } else {
                        if(result.message){
                            login_message.addClass('show').addClass('error').find(".msg_alert_content").html(result.message);
                        }
                    }
                });
            }
        },
        //注册提交
        doReg: function (form, elem) {
            var reg_message = form.find(".reg_message");
            reg_message.removeClass('show');
            var _this = this;
            var flag = true;
            var sub_btn = form.find(".submit_reg");
            var password_obj = form.find(".password");
            var confirm_password_obj = form.find(".confirm_password");
            if (confirm_password_obj.val() !== password_obj.val()) {
                form.find('.confirm_password_error').show();
                flag = false;
            } else {
                form.find('.confirm_password_error').hide();
            }
            if (form.valid() && flag) {
                //表单数据系列化
                var data = form.serializeObject();
                 var email = $.trim(form.find(".user_email").val());
                $.postForm(elem, "/api/auth/register", data, function (result) {
                    if (result.code == '0x0000') {
                        if(typeof _this.regEvent == function(){
                            _this.regEvent();
                        });
                        if(elem.attr("data-redirect-link")){
                            window.location.href = elem.attr("data-redirect-link");
                        } else {
                            mylayer.showMessage('success', result.message, function(){
                                window.location.reload();
                            });
                        }
                    } else {
                        if(result.message){
                            reg_message.addClass('show').removeClass("success").addClass('error').find(".msg_alert_content").html(result.message);
                        }
                    }
                });
            }
        },
        //忘记密码提交
        doForget: function (form, elem) {
            var forgot_msg = form.find(".forgot_msg");
            forgot_msg.removeClass('show');
            if (form.valid()) {
                //表单数据系列化
                var data = form.serializeObject();
                $.postForm(elem, "/api/auth/forget_password", data, function (result) {
                    if (result.code == '0x0000') {
                        if(result.message){
                            forgot_msg.addClass('show').removeClass("error").addClass('success').find(".msg_alert_content").html(result.message);
                        }
                    } else {
                        if(result.message){
                            forgot_msg.addClass('show').removeClass("success").addClass('error').find(".msg_alert_content").html(result.message);
                        }
                    }
                });
            }
        },
        //密码重置提交
        doReset: function (form, elem) {
            var _this = this;
            var flag = true;
            var password_obj = form.find(".new_pwd");
            var confirm_password_obj = form.find(".confirm_new_pwd");
            if (confirm_password_obj.val() !== password_obj.val()) {
                form.find('.confirm_password_error').show();
                flag = false;
            } else {
                form.find('.confirm_password_error').hide();
            }
            if (form.valid() && flag) {
                //表单数据系列化
                var data = form.serializeObject();
                var token = form.find(".token").val();
                var url = "/api/auth/reset/" +token;
                $.postForm(elem, url, data, function (result) {
                    if (result.code == "0x0000") {
                       mylayer.showMessage("success", result.message,function(){
                            window.location.href = '/account/entry';
                       });
                    } else if (result.message) {
                        mylayer.showMessage("error", result.message);
                    }
                });
            }
        },
        //用户激活提交
        doActive: function (form, elem) {
            var _this = this;
            var activate_message = form.find(".activate_message").removeClass('show');
            if (form.valid()) {
                //表单数据系列化
                var data = form.serializeObject();
                $.postForm(elem, '/api/auth/useractive', data, function (result) {
                    if (result.code == "0x0000") {
                        activate_message.addClass('show').removeClass("error").addClass('success').find(".msg_alert_content").html(result.message);
                    } else if (result.message) {
                         activate_message.addClass('show').removeClass("success").addClass('error').find(".msg_alert_content").html(result.message);
                    }
                });
            }
        },
        //设置登录完成回调
        initSuccess: function (fevent) {
            this.fevent = fevent;
        },
        //显示登录
        showLogin: function () {
            var size = $(".mylayer").size();
            if (size > 0) {
                var z_index = $(".mylayer").eq(size - 1).css("z-index") + 1;
                $(".overlay_login").css("z-index", z_index - 1);
                $(".layer_login").css("z-index", z_index);
            }
            $(".layer_login").addClass('show');
            $(".overlay_login").show();
            $(".layer_login").find(".login_panel").show().siblings('.panel').hide();
            this.initlayer_login();
        },
        //显示注册
        showSignUp: function () {
            $(".layer_login").addClass('show');
            $(".overlay_login").show();
            $(".layer_login").find(".reg_panel").show().siblings('.panel').hide();
            this.initlayer_login();
        },
        //显示注册
        showSignUpSelect: function () {
            $(".layer_login").addClass('show');
            $(".overlay_login").show();
            $(".layer_login").find(".reg_select_panel").show().siblings('.panel').hide();
            this.initlayer_login();
        },
        //显示忘记密码
        showForget: function () {
            $(".layer_login").addClass('show');
            $(".overlay").show();
            $(".forgot_tip").html("").hide();
            $(".layer_login").find(".forget_panel").show().siblings('.panel').hide();
            this.initlayer_login();
        },
        //关闭登录
        hideLogin: function () {
            $(".layer_login").removeClass('show');
            $(".overlay_login").hide();
            if ($(".mylayer").size() === 0) {
                $("html,body").css("overflow-y", "auto");
            }
            this.fevent = "";
        },
        //初始化登录弹出层
        initlayer_login: function () {
            $("html,body").css("overflow-y", "hidden");
            var layerBox_wrapper = $(".layer_login").find(".layerBox_wrapper");
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
        },
        initLogin:function(data){
            var nav = '';
            if(data.user_type == '2'){
                nav += '<li><a class="text_transition" href="/account/organization">机构管理</a></li>';
                nav += '<li><a class="text_transition" href="/account/organization/teacher">教师管理</a></li>';
                nav += '<li><a class="text_transition" href="/account/organization/course">开设课程</a></li>';
                nav += '<li><a class="text_transition" href="/account/organization/course/order">课程订单</a></li>';
            } else {
                nav += '<li><a class="text_transition" href="/account/order">我的订单</a></li>';
                nav += '<li><a class="text_transition" href="/account/wish/course">您的收藏</a></li>';
            }
            var html = '<ul class="account clearfix">'+
                '<li class="dt_nav_right_item">'+
                    '<span>欢迎您，</span>'+
                    '<a class="text_transition" title="{{ $nickname }}">'+data['nickname'] + 
                        '<span class="sub"></span>'+                        
                    '</a>'+
                    '<ul>'+
                        '<li><a class="text_transition" href="/account/setting">帐号设置</a></li>'+ nav 
                        +'<li><a class="text_transition" href="/auth/logout">注销</a></li>'+
                    '</ul>'+
                '</li>'+
            '</ul>';
            $(".account_user").html(html);
        }
    };
    if (typeof app !== undefined) {
        $(function () {
            app.init();
        });
    }
    return app;
});