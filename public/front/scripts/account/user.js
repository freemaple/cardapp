require(['jquery', 'validate', 'mylayer'], function ($, validate, mylayer) {
    var app = {
        init: function () {
            var _this = this;
            //电话号码验证 
            $.validator.addMethod("phone", function(value, element) { 
                var tel = /^[()+0-9_\-\s]*$/; 
                return this.optional(element) || (tel.test(value)); 
            }, $.tran("valid.valid_phone", "请输入有效的电话号码")); 
            //账号信息表单验证
            $(".account_info_form").validate(this.change_info_valid);
            $(".account_info_form").on("click", function(event){
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击保存提交按钮
                if (elem.hasClass("save_account_info")) {
                    _this.doChangeInfo(form, elem);
                }
            });
            //修改密码表单验证
            $(".alert_password_form").validate(this.alert_pwd_valid);
            $(".alert_password_form").on("click", function(event){
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击密码修改提交按钮
                if (elem.hasClass("save_pwd")) {
                    _this.doAlertPwd(form, elem);
                }
            });
            //验证提示多语言设置
            $.validatorMessageConfig();
        },
        //修改密码验证规则
        change_info_valid: {
            rules: {
                public_name: {
                    required: true,
                    maxlength: 50
                },
                phone_number: {
                    maxlength: 50,
                    phone: true
                }
            }
        },
        //修改密码验证规则
        alert_pwd_valid: {
            rules: {
                current_password: {
                    required: true
                },
                new_password: {
                    required: true,
                    min: 6,
                    maxlength: 50
                }
            }
        },
        review_form_valid: {
            rules: {
                score: {
                    required: true
                },
                content: {
                    required: true
                }
            }
        },
        course_reserve_form_valid: {
            rules: {
                tearcher_time: {
                    required: true
                },
                class_number: {
                    required: true
                }
            }
        },
        //修改基本信息
        doChangeInfo: function(form, elem){
            if (form.valid()){
                //表单数据系列化
                var data = form.serializeObject();
                $.postForm(elem, "/api/account/changeinfo", data, function (result) {
                    if (result.code == "0x0000") {
                       mylayer.showMessage("success", result.message);
                        if(typeof data.public_name != "undefined"){
                            $(".account_nick_name").text(data.public_name);
                        }
                    } else {
                        mylayer.showMessage("error", result.message);
                    }
                });
            }
        },
        //修改密码
        doAlertPwd: function(form, elem){
            var flag = true;
            var new_pwd = $.trim(form.find(".new_password").val());
            var confirm_new_pwd = $.trim(form.find(".confirm_new_pwd").val());
            if(confirm_new_pwd != new_pwd){
                form.find(".confirm_password_error").show();
                flag = false;
            } else {
                form.find(".confirm_password_error").hide();
            }
            if (form.valid() && flag){
                //表单数据系列化
                var data = form.serializeObject();
                $.postForm(elem, "/api/account/changepwd", data, function (result) {
                    if (result.code == "0x0000") {
                       mylayer.showMessage("success", result.message);
                       $(form)[0].reset();
                    } else {
                        mylayer.showMessage("error", result.message);
                    }
                });
            }
        }
    };
    $(function () {
        app.init();
    });
});