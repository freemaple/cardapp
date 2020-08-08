 require(['jquery', 'validate', 'mylayer', 'base'], function ($, validate, mylayer, md_base) {
    var app = {
        init: function () {
            var _this = this;
            $(".show_course_record_review").on("click", function(){
                var order_id = $(this).attr("order-id");
                var course_record_id = $(this).attr("data-id");
                if($(".course_record_review_layer").size() == 0){
                    var content = $(".review_layer").html();
                    //弹出购买层
                    mylayer.init({
                        'title': "课程评论",
                        'content': content,
                        'no_remove': true,
                        'class_name': 'course_record_review_layer',
                        'layerClose': false,
                        success: function(){
                            $(".review_form").find(".submit_review").show();
                            $(".review_form").validate(_this.review_form_valid);
                            $(".review_form").on("click", function(event){
                                var form = $(this);
                                var e = event || window.event;
                                var elem = $(e.target || e.srcElement);
                                //判断是点击密码修改提交按钮
                                if (elem.hasClass("submit_review")) {
                                    if (form.valid()){
                                        //表单数据系列化
                                        var data = form.serializeObject();
                                        data.order_id = order_id;
                                        data.course_record_id = course_record_id;
                                        $.postForm(elem, "/api/course/order/record/review", data, function (result) {
                                            if (result.code == "200") {
                                                mylayer.showMessage("success", result.message, function(){
                                                    window.location.reload();
                                                });
                                            } else {
                                                mylayer.showMessage("error", result.message);
                                            }
                                        });
                                    }
                                }
                            });
                            $(".review_course_block").on("click", function(event){
                                var review_course_block = $(this);
                                var e = event || window.event;
                                var elem = $(e.target || e.srcElement);
                                var review_s = elem;
                                if(elem.closest('.review_s').size() > 0){
                                    review_s = elem.closest('.review_s');
                                }
                                if(review_s.hasClass('review_s')){
                                    var offsetWidth = e.offsetX;
                                    var width = review_s.width();
                                    var score_value = Math.ceil(((offsetWidth / width) * 5));
                                    if(score_value > 5){
                                        score_value = 5;
                                    }
                                    review_course_block.find(".score_value").val(score_value);
                                    review_course_block.find(".score_value_text").text(score_value);
                                    review_s.find(".review_s_value").width(score_value / 5 * width);
                                }
                            });
                        }
                    });
                } else {
                    $(".course_record_review_layer").addClass('show');
                    $(".overlay_course_record_review_layer").show();
                }
            });
            $(".load_course_record_review").on("click", function(){
                var elem = $(this);
                var order_id = $(this).attr("order-id");
                var course_record_id = $(this).attr("data-id");
                var data = {};
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
            $(".add_course_reserve").on("click", function(){
                var order_id = $(this).attr("order-id");
                if($(".course_reserve_layer_block").size() == 0){
                    var content = $(".course_reserve_layer").html();
                    //弹出购买层
                    mylayer.init({
                        'title': "课程预约",
                        'content': content,
                        'no_remove': true,
                        'class_name': 'course_reserve_layer_block',
                        'layerClose': false,
                        success: function(){
                            _this.setReserveDate();
                            $(".course_reserve_form").validate(_this.course_reserve_form_valid);
                            $(".course_reserve_form").on("click", function(event){
                                var form = $(this);
                                var e = event || window.event;
                                var elem = $(e.target || e.srcElement);
                                //判断是点击密码修改提交按钮
                                if (elem.hasClass("submit_course_reserve")) {
                                    if (form.valid()){
                                        //表单数据系列化
                                        var data = form.serializeObject();
                                        data.order_id = order_id;
                                        $.postForm(elem, "/api/course/order/reserve", data, function (result) {
                                            if (result.code == "200") {
                                                mylayer.showMessage("success", result.message, function(){
                                                    window.location.reload();
                                                });
                                            } else {
                                                mylayer.showMessage("error", result.message);
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    });
                } else {
                    $(".course_reserve_layer_block").addClass('show');
                    $(".overlay_course_reserve_layer_block").show();
                }
            });
            $(".update_order_record_status").on("click", function(){
                var elem = $(this);
                var confim = elem.attr('data-confim');
                mylayer.showConfirm(confim, function(){
                    var order_record_id = elem.attr("data-id");
                    var status = elem.attr("data-status");
                    var data = {'order_record_id': order_record_id, 'status': status};
                    $.postForm(elem, "/api/course/order/record/updatestatus", data, function (result) {
                        if (result.code == "200") {
                            mylayer.showMessage("success", result.message, function(){
                                window.location.reload();
                            });
                        } else {
                            mylayer.showMessage("error", result.message);
                        }
                    });
                });
            });
            $(".btn_order_cancel").on("click", function(){
                var elem = $(this);
                mylayer.showConfirm('确认取消您的订单？', function(){
                    var order_id = elem.attr("order-id");
                    var data = {'order_id': order_id};
                    $.postForm(elem, "/api/course/order/cancel", data, function (result) {
                        if (result.code == "200") {
                            mylayer.showMessage("success", result.message, function(){
                                window.location.reload();
                            });
                        } else {
                            mylayer.showMessage("error", result.message);
                        }
                    });
                });
            });
            $(".order_status_link").on("click", function(){
                $.cachePut('order_status_link', true);
            });
            var order_status_link = $.cacheGet('order_status_link');
            if(order_status_link){
                var top = $(".order_record_box").offset().top;
                window.scrollTo(0, top);
                $.cacheRemove('order_status_link');
            }
            //验证提示多语言设置
            $.validatorMessageConfig();
        }
    }
    app.setReserveDate = function(){
        //时间控件选择
        if(typeof laydate != undefined){
            laydate.skin('molv');
            //开始时间
            var start = {
                format: 'YYYY-MM-DD hh:mm',
                istime: true,
                istoday: false,
                isclear: false
            };
            $(".reserve_time").on("click", function(){
                laydate(start);
            });
        }
    }
    $(function () {
        app.init();
    });
});