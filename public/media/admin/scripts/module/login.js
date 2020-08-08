var md_loginReg = (function ($) {
    var app = {};
    //初始化事件
    app.init = function () {
        var _this = this;
        //登录表单校验
        $("#login_form").validate(_this.loginValid);
        //登录提交
        $("#login_form").on("submit", function () {
            var form = $("#login_form");
            if (form.valid()) {
                $.post("/admin/auth/login", form.serialize(), function (result) {
                    if (result.code == "0x00000") {
                        window.location.href = "/admin";
                    } else if (result.msg) {
                        $.showMessage(result.msg);
                    }
                }, 'json').error(function () {
                    $.showMessage("网络问题或者系统错误，请联系管理员");
                });
            }
            return false;
        });
    };
    //登录校验规则
    app.loginValid = {
        rules: {
            username: {
                required: true
            },
            pwd: {
                required: true
            }
        },
        messages: {
            username: {
                required: "用户名不能为空"
            },
            pwd: {
                required: "密码不能为空"
            }
        }
    };
    return app;
})(jQuery);
if (typeof md_loginReg != undefined) {
    $(function () {
        md_loginReg.init();
    });
}