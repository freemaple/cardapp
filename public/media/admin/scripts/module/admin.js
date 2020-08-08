var md_admin = (function ($) { 
    var app = {};
    app.init = function(){
        /*后台用户模块开始*/
        //修改后台用户密码
        $("#alert_form").validate({
            rules: {
                old_pwd: {
                    required: true
                },
                new_pwd: {
                    required: true,
                    maxlength: 50
                },
                new_pwd1: {
                    required: true,
                    equalTo: "#new_pwd"
                }
            },
            messages: {
                old_pwd: {
                    required: "旧密码不能为空"
                },
                new_pwd: {
                    required: "密码不能为空",
                    maxlength: "密码不能大于50个字符"
                },
                new_pwd1: {
                    required: "确认密码不能为空",
                    equalTo: "新密码和确认密码不一致"
                }
            }
        });
        //提交密码修改
        $("#btn_alertPwd").on("click", function () {
            if ($("#alert_form").valid()) {
                $.post("/admin/user/alertpwd", $("#alert_form").serialize(), function (result) {
                    if (result.code == "0x00000") {
                        $.showMessage("修改成功！");
                        $("#alert_form")[0].reset();
                    } else {
                        $.showMessage(result.msg);
                    }
                }, 'json').error(function () {
                    $.showMessage("系统错误或者网络问题，请联系管理员");
                });
            }
        });
        //后台添加用户表单校验
        $("#add_user_form").validate({
            rules: {
                username: {
                    required: true,
                    maxlength: 50
                },
                userpwd: {
                    required: true,
                    minlength: 6,
                    maxlength: 50
                }
            },
            messages: {
                username: {
                    required: "请输入用户名",
                    maxlength: "用户名不能超过50个字符"
                },
                userpwd: {
                    required: "请输入密码",
                    minlength: "密码最少6个字符",
                    maxlength: "密码不能超过50个字符"
                }
            },
            submitHandler: function (form) {
                $.post('/admin/user/save', $(form).serialize(), function (result) {
                    if (result.code == "0x00000") {
                        if (result.id) {
                            $("#user_id").val(result.id);
                        }
                        $.showMessage("保存成功");
                    } else if (result.msg) {
                        $.showMessage(result.msg);
                    }
                }, 'json').error(function () {
                    $.showMessage("系统错误或者网络问题，请联系管理员");
                });
                return false;
            }
        });
        //后台编辑用户表单校验
        $("#edit_user_form").validate({
            rules: {
                username: {
                    required: true,
                    maxlength: 50
                },
                userpwd: {
                    minlength: 6,
                    maxlength: 50
                }
            },
            messages: {
                username: {
                    required: "请输入用户名",
                    maxlength: "用户名不能超过50个字符"
                },
                userpwd: {
                    minlength: "密码最少6个字符",
                    maxlength: "密码不能超过50个字符"
                }
            },
            submitHandler: function (form) {
                $.post('/admin/user/save', $(form).serialize(), function (result) {
                    if (result.code == "0x00000") {
                        $.showMessage("保存成功");
                    } else if (result.msg) {
                        $.showMessage(result.msg);
                    }
                }, 'json').error(function () {
                    $.showMessage("系统错误或者网络问题，请联系管理员");
                });
                return false;
            }
        });
        /*后台用户模块结束*/
    };
    return app;
})(jQuery);
if (typeof md_admin.init == "function") {
    $(function () {
        md_admin.init();
    });
}
