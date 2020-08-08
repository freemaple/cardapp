var md_course = (function ($) { 
    var app = {};
    app.init = function(){
        var self = this;
        $(".setting_enable").on("change", function(){
            var id = $(this).attr("data-id");
            var enable = $(this).val();
            var data = {'enable': enable};
            $.post("/admin/organization/setting/" + id, data, function(result){
                if(result.code == '200'){
                    window.location.reload();
                }
            }, 'json');
        });
        $(".btn_organization_approval").on("click", function() {
            $(".organization_approval_form")[0].reset();
            var id = $(this).attr("data-id");
            self.organization_id = id;
            $("#organization_approval_modal").modal();
        });
        $(".organization_approval_form").on("change", function(event){
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            if(elem.hasClass("handle_option")){
                var handle_option = elem;
            } else {
                var handle_option = elem.closest(".handle_option");
            }
            if(handle_option.size() > 0){
                var approval_value = handle_option.val();
                if(approval_value == 'refused'){
                    form.find(".handle_remark_block").show();
                } else {
                    form.find(".handle_remark_block").hide();
                }
            }
        });
        $(".organization_approval_form").on("click", function(){
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            if(elem.hasClass("btn_submit_approval")){
                var submit_elem = elem;
            } else {
                var submit_elem = elem.closest(".btn_submit_approval");
            }
            if(submit_elem.size() > 0){
                var id = self.organization_id;
                var data = {};
                var handle_option = form.find(".handle_option");
                var approval_value = handle_option.val();
                data['approval'] = approval_value;
                if(approval_value == 'refused'){
                    var handle_remark = form.find(".handle_remark").val();
                    data['remark'] = handle_remark;
                } 
                submit_elem.addClass("disabled");
                $.post("/admin/organization/setting/" + id, data, function(result){
                    submit_elem.removeClass("disabled");
                    if(result.code == '200'){
                        window.location.reload();
                    }
                }, 'json')
            }
        });
    };
    return app;
})(jQuery);
if (typeof md_course.init == "function") {
    $(function () {
        md_course.init();
    });
}