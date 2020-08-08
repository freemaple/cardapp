require(['jquery', 'validate', 'mylayer'], function ($, validate, mylayer) {
    var app = {
        init: function () {
            var self = this;
            //登录验证
            $(".feedback_form").validate(this.feedbackValid);
            //登录表单事件
            $(".feedback_form").on("click", function (event) {
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击登录提交按钮
                if (elem.hasClass("submit_feedback")) {
                    self.doFeedback(form, elem);
                }
            });
        },
        //登录验证规则
        feedbackValid: {
            rules: {
                fullname: {
                    required: true,
                    maxlength: 50
                },
                email: {
                    required: true,
                    email: true,
                },
                content: {
                    required: true
                }
            }
        },
        //提交
        doFeedback: function (form, elem) {
            var feedback_message = form.find(".feedback_message");
            feedback_message.hide();
            var self = this;
            if (form.valid()) {
                //表单数据系列化
                var data = form.serializeObject();
                $.postForm(elem, "/api/feedback", data, function (result) {
                    if (result.code == "0x0000") {
                        
                       
                    } else {
                        if(result.message){
                            
                        }
                    }
                });
            }
        }
    };
    $(function () {
        app.init();
    });
});