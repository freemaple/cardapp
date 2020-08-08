var md_order = (function ($) { 
    var app = {};
    app.init = function(){
        var self = this;
        $(".show_teacher_assign").on("click", function(){
            $("#teacher_assign_modal").modal();
        });
        $(".btn_submit_assign").on("click", function(){
            var data = $(".teacher_assign_form").serializeObject();
            var elem = $(this);
            $.postForm(elem, '/admin/order/teacher/syn', data, function(result){
                if(result.code == '200'){
                    $.showMessage(result.message, function(){
                        window.location.reload();
                    });
                } else if(result.message){
                    $.showMessage(result.message);
                }
            });
        });
         $(".load_course_record_review").on("click", function(){
            var elem = $(this);
            var order_id = $(this).attr("order-id");
            var course_record_id = $(this).attr("data-id");
            var data = {};
            data.order_id = order_id;
            data.course_record_id = course_record_id;
            $.postForm(elem, "/admin/order/record/review", data, function (result) {
                if (result.code == "200") {
                    $("#order_record_review_modal").modal();
                    $(".review_form").find(".score_value").val(result.data.score);
                    $(".review_form").find(".content_value").val(result.data.content);
                    if(result.data.score > 5){
                        result.data.score = 5;
                    }
                    $(".score_value").val(result.data.score);
                    $(".score_value_text").text(result.data.score);
                    var width = $(".review_form").find(".score_value_s").width();
                    $(".review_form").find(".review_s_value").width(result.data.score / 5 * width);
                } else {
                    mylayer.showMessage("error", result.message);
                }
            });
        });
    };
    return app;
})(jQuery);
if (typeof md_order.init == "function") {
    $(function () {
        md_order.init();
    });
}