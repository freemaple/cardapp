require(['jquery', 'validate', 'mylayer', 'tree'], function ($, validate, mylayer, tree) {
    var app = {};
    app.init = function(){
        var self = this;
        //电话号码验证 
        $.validator.addMethod("phone", function(value, element) { 
            var tel = /^[()+0-9_\-\s]*$/; 
            return this.optional(element) || (tel.test(value)); 
        }, $.tran("valid.valid_phone", "请输入正确的电话号码")); 
        this.orderEvent();
        //验证提示多语言设置
        $.validatorMessageConfig();
    };

    app.setForm = function(form, data) {
        var obj = form.find("[name]");
        var name;
        obj.each(function(item){
            name = $.trim($(this).attr("name"));
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
            else {
                $(this).val(data[name] ? data[name] : '');
            }
        });
    };
    app.orderEvent = function(){
        $(".update_order_record_status").on("click", function(){
            var elem = $(this);
            var order_record_id = $(this).attr("data-id");
            var status = $(this).attr("data-status");
            var data = {'order_record_id': order_record_id, 'status': status};
            $.postForm(elem, "/api/teacher/order/record/updatestatus", data, function (result) {
                if (result.code == "200") {
                    mylayer.showMessage("success", result.message, function(){
                        window.location.reload();
                    });
                } else {
                    mylayer.showMessage("error", result.message);
                }
            });
        });
        $(".load_course_record_review").on("click", function(){
            var elem = $(this);
            var order_id = $(this).attr("order-id");
            var course_record_id = $(this).attr("data-id");
            var data = {'user_type': '3'};
            data.order_id = order_id;
            data.course_record_id = course_record_id;
            $.postForm(elem, "/api/course/order/record/review/load", data, function (result) {
                if (result.code == "200") {
                   var content = $(".review_layer").html();
                    //弹出购买层
                    mylayer.init({
                        'title': "课程评论",
                        'content': content,
                        'no_remove': true,
                        'class_name': 'course_record_review_layer',
                        'layerClose': false,
                        success: function(){
                            $(".review_form").find(".submit_review").hide();
                            $(".review_form").find(".score_value").val(result.data.score);
                            $(".review_form").find(".content_value").val(result.data.content);
                            if(result.data.score > 5){
                                result.data.score = 5;
                            }
                            $(".score_value").val(result.data.score);
                            $(".score_value_text").text(result.data.score);
                            var width = $(".review_form").find(".score_value_s").width();
                            $(".review_form").find(".review_s_value").width(result.data.score / 5 * width);
                        }
                    });
                } else {
                    mylayer.showMessage("error", result.message);
                }
            });
        });
    }
    $(function () {
        app.init();
    });
});