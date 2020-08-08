require(['jquery', 'validate', 'mylayer', 'tree'], function ($, validate, mylayer, tree) {
    var app = {};
    app.init = function(){
        var self = this;
        //电话号码验证 
        $.validator.addMethod("phone", function(value, element) { 
            var tel = /^[()+0-9_\-\s]*$/; 
            return this.optional(element) || (tel.test(value)); 
        }, $.tran("valid.valid_phone", "请输入正确的电话号码")); 
        this.orgJoinEvent();
        this.orgEdit();
        this.teacherInit();
        this.orderEvent();
        this.courseSetting();
        //验证提示多语言设置
        $.validatorMessageConfig();
        var treeCategoryData = typeof treeCategory == 'undefined' ? [] : treeCategory;
        $('#course_category_tree').tree({
            data: treeCategoryData
        });
        var course_category_tree = $('#course_category_tree');
        course_category_tree.bind(
            'tree.select',
            function(event) {
                if (event.node) {
                    var node = event.node;
                    $(".course_category_id").val(node.id);
                    $(".course_category_name").val(node.name);
                    var course_category_tree_box = $(".course_category_tree_box");
                    course_category_tree_box.hide();
                }
            }
        );
        $(".course_category_name").on("click", function(){
            var course_category_tree_box = $(".course_category_tree_box");
            if(course_category_tree_box.is(":hidden")){
                course_category_tree_box.show();
            } else {
                course_category_tree_box.hide();
            }
        });
        var course_category_id = $(".course_category_id").val();
        if(course_category_id){
            var node = course_category_tree.tree('getNodeById', course_category_id);
            course_category_tree.tree('selectNode', node);
        }
        $("body").on("click", function(event){
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            if(!elem.hasClass('course_category_tree_box') && elem.closest('.course_category_tree_box').size() == 0 && !elem.hasClass('course_category_name')){
                var course_category_tree_box = $(".course_category_tree_box");
                course_category_tree_box.hide();
            }
        });
        //选择图片后上传预览
        $(".image_file_select").off("change").on("change",function(){
            try{
                var elem = $(this);
                var files = elem[0].files;
                if(files && files.length>0){
                    //Verify that the file type
                    if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg', 'image/gif']) == -1){
                        mylayer.showMessage("error",'请检查图片格式，只能上传png/jpg/gif image');
                        return false;
                    }
                    var file_size = files[0].size;
                    file_size = file_size / 1024 / 1024;
                    if(file_size > 2){
                        mylayer.showMessage("error",'请上传小于3M的图片');
                        return false;
                    }
                }
            }
            catch(e){}
        });
    };
    app.orgJoinEvent = function(){
        var self = this;
        $(".org_nav").click(function(){
            var elem = $(this);
            if(elem.hasClass('disabled')){
                return false;
            }
            var nav = $(this).attr('data-nav');
            $(this).addClass("current").siblings(".org_nav").removeClass('current');
            $(".org_panel").hide();
            $(".org_panel_" + nav).show();
        });
        $(".show_org_next").on("click", function(){
            $(".org_nav.current").removeClass("current").next(".org_nav").addClass("current");
            $(".org_panel:visible").hide().next(".org_panel").show();
            var top = $(".org_box").offset().top - 100;
            window.scrollTo(0, top);
        });
        $(".org_join_form").validate(this.orgValid);
        $(".submit_org_info").on("click", function(){
            var elem = $(this);
            if(elem.hasClass('disabled')){
                return false;
            }
            var form = $(".org_join_form");
            if(form.valid()){
                form.submit();
                elem.addClass('disabled');
            }
        });
        
    };
    app.orgEdit = function(){
        var self = this;
        //更换banner
        $(".organization_image").on("click", function(){
            $(".organization_image_file").click();
        });
        //选择图片后上传预览
        $(".organization_image_file").off("change").on("change",function(){
            try{
                var elem = $(this);
                var files = elem[0].files;
                if(files && files.length>0){
                    //Verify that the file type
                    if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg', 'image/gif']) == -1){
                        mylayer.showMessage("error",'请检查图片格式，只能上传png/jpg/gif image');
                        return false;
                    }
                    var file_size = files[0].size;
                    file_size = file_size / 1024 / 1024;
                    if(file_size > 3){
                        mylayer.showMessage("error",'请上传小于3M的图片');
                        return false;
                    }
                }
                self.uploadOrganizationBanner({
                    'elem': elem
                });
            }
            catch(e){}
        });
        $(".show_org_info_box").on("click", function(){
            $(".org_form_box").hide();
            var org_info_box = $(".org_info_box");
            if(org_info_box.is(":hidden")){
                org_info_box.show();
            }
            var top = org_info_box.offset().top -100;
            window.scrollTo(0, top);
        });
        $(".hide_org_info_box").on("click", function(){
            var org_info_box = $(".org_info_box");
            org_info_box.hide();
            var top = $(".org_block").offset().top -100;
            window.scrollTo(0, top);
        });
        $(".show_org_setting_box").on("click", function(){
            $(".org_form_box").hide();
            var org_setting_box = $(".org_setting_box");
            if(org_setting_box.is(":hidden")){
                org_setting_box.show();
            }
            var top = org_setting_box.offset().top -100;
            window.scrollTo(0, top);
        });
        $(".hide_org_setting_box").on("click", function(){
            var org_setting_box = $(".org_setting_box");
            org_setting_box.hide();
            var top = $(".org_block").offset().top -100;
            window.scrollTo(0, top);
        });
        $(".org_info_form").validate(this.orgUpdateValid);
        $(".update_org_info_box").on("click", function(){
            var form = $(".org_info_form");
            if(form.valid()){
                var data = form.serializeObject();
                self.updateOrganization(data, function(result){
                    if(result.code == '200'){
                        mylayer.showMessage("success", result.message, function(){
                            window.location.href = window.location.href;
                        });
                    }
                });
            }
        });
        $(".public_org").on("click", function(){
            var elem = $(this);
            var alert_info = $(".update_public_org_alert");
            var is_public = elem.is(":checked") ? '1' : '0';
            var data = {'public': is_public};
            alert_info.html('');
            $.postForm(elem, '/api/organization/update', data, function(result){
                if(result.code == '200'){
                    alert_info.html("<span class='text_success'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                    if(is_public == '1'){
                        elem.prop("checked", 'checked');
                    } else {
                        elem.removeAttr("checked");
                    }
                } else if(result.message){
                    alert_info.html("<span class='text_red'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                }
            });
            return false;
        });
        $(".online_org").on("click", function(){
            var elem = $(this);
            var alert_info = $(".update_off_org_alert");
            var online = elem.is(":checked") ? '1' : '0';
            var data = {'online': online};
            alert_info.html('');
            $.postForm(elem, '/api/organization/update', data, function(result){
                if(result.code == '200'){
                    alert_info.html("<span class='text_success'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                    if(online == '1'){
                        elem.prop("checked", 'checked');
                    } else {
                        elem.removeAttr("checked");
                    }
                } else if(result.message){
                    alert_info.html("<span class='text_red'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                }
            }, 'json');
            return false;
        });
        if($("#course_description_editor").size() > 0){
            require(['wangEditor'], function(wangEditor){
                $.descriptionEditor(wangEditor, 'course_description_editor');
            });
        }
        if($("#organization_description_editor").size() > 0){
            require(['wangEditor'], function(wangEditor){
                $.descriptionEditor(wangEditor, 'organization_description_editor');
            });
        }
        $(".organization_submit_approval").on('click', function(){
            var elem = $(this);
            $.postForm(elem, '/api/organization/submitApproval', {}, function(result){
                if(result.code == '200'){
                    mylayer.showMessage('success', '已提交审核，请耐心等待！', function(){
                        window.location.reload();
                    });
                } else {
                    mylayer.showMessage('error', result.message);
                }
            });
        });
    };
    app.courseSetting = function(){
        $(".add_course_form").validate(this.addcourseValid);
        $(".edit_course_form").validate(this.editcourseValid);
        $(".public_course").on("click", function(){
            var elem = $(this);
            var course_id = elem.attr('data-id');
            var alert_info = $(".update_public_alert");
            var is_public = elem.is(":checked") ? '1' : '0';
            var data = {'public': is_public};
            alert_info.html('');
            $.postForm(elem, '/account/organization/course/setting/' + course_id, data, function(result){
                if(result.code == '200'){
                    alert_info.html("<span class='text_success'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                    if(is_public == '1'){
                        elem.prop("checked", 'checked');
                    } else {
                        elem.removeAttr("checked");
                    }
                } else if(result.message){
                    alert_info.html("<span class='text_red'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                }
            });
            return false;
        });
        $(".online_course").on("click", function(){
            var elem = $(this);
            var course_id = elem.attr('data-id');
            var alert_info = $(".update_off_alert");
            var online = elem.is(":checked") ? '1' : '0';
            var data = {'online': online};
            alert_info.html('');
            $.postForm(elem, '/account/organization/course/setting/' + course_id, data, function(result){
                if(result.code == '200'){
                    alert_info.html("<span class='text_success'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                    if(online == '1'){
                        elem.prop("checked", 'checked');
                    } else {
                        elem.removeAttr("checked");
                    }
                } else if(result.message){
                    alert_info.html("<span class='text_red'>" + result.message + "</span>")
                    alert_info.show().delay(3000).hide(0);
                }
            }, 'json');
            return false;
        });
    };
    app.orgValid = {
        rules: {
            name: {
                required: true,
                maxlength: 50
            },
            banner: {
                required: true,
                accept: ''
            },
            address: {
                required: true,
                maxlength: 255
            },
            creation_time: {
                required: true,
                maxlength: 50
            },
            business_hours: {
                required: true,
                maxlength: 255
            },
            contact_phone: {
                required: true,
                phone: true,
                maxlength: 50
            },
            contact_user_name: {
                required: true,
                maxlength: 50
            },
            busines_license_number: {
                required: true,
                maxlength: 50
            },
            busines_license_image: {
                required: true,
                accept: ''
            },
        }
    };
    app.orgUpdateValid = {
        rules: {
            name: {
                required: true,
                maxlength: 50
            },
            address: {
                required: true,
                maxlength: 255
            },
            creation_time: {
                required: true,
                maxlength: 50
            },
            contact_phone: {
                required: true,
                phone: true,
                maxlength: 50
            },
            contact_user_name: {
                required: true,
                maxlength: 50
            },
            busines_license_number: {
                required: true,
                maxlength: 50
            }
        }
    };
    app.addcourseValid = {
        rules: {
            name: {
                required: true,
                maxlength: 50
            },
            banner: {
                required: true,
                accept: ''
            },
            price: {
                required: true,
                number: true
            },
            special: {
                number: true
            },
            class_number: {
                required: true,
                digits: true,
                min: 1
            },
            description: {
                required: true 
            }
        }
    };
    app.editcourseValid = {
        rules: {
            name: {
                required: true,
                maxlength: 50
            },
            banner: {
                accept: ''
            },
            price: {
                required: true,
                number: true
            },
            special: {
                number: true
            },
            class_number: {
                required: true,
                digits: true,
                min: 1
            },
            description: {
                required: true
            }
        }
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
    app.updateOrganization = function(data, success){
        $.post('/api/organization/update', data, function(result){
            if(typeof success == 'function'){
                success(result);
            }
        }, 'json');
    };
    app.teacherInit = function(){
        var self = this;
        //隐藏价格弹出层
        $(".hide_teacher_layer").on("click", function(){
            $(".teacher_layer").removeClass('show');
            $(".teacher_layer_box").removeClass('show');
            $(".teacher_layer_overlay").hide();
            $("html,body").css("overflow-y", "auto");
        });
        $(".add_teacher").on("click", function(event) {
            var data = {'status': '1', 'type': '1'};
            //加载编辑表单
            self.initTeacherLayer(data);
        });
        $(".edit_tearcher").on("click", function(){
            var elem = $(this);
            var teacher_id = elem.attr("data-id");
            $.postForm(elem, "/api/organization/teacher/load", {'teacher_id': teacher_id}, function(result){
                if(result.code == '200'){
                    var data = result.data;
                    data.type = '2';
                    //加载编辑表单
                    self.initTeacherLayer(data);
                }
            });
        });
        $(".teacher_info_form").validate(this.teacherValid);
        //注册表单事件
        $(".teacher_info_form").on("click", function (event) {
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            //判断是点击注册提交按钮
            if (elem.hasClass("save_teacher")) {
                if(form.valid()){
                    var data = form.serializeObject();
                    $.postForm(elem, '/api/organization/teacher/save', data, function(result){
                        if(result.code == '200'){
                            mylayer.showMessage('success', result.message, function(){
                                window.location.reload();
                            });
                        } else if(result.message){
                            mylayer.showMessage('success', result.message);
                        }
                    });
                }
            }
        });
        $(".show_org_order_teacher").on("click", function(){
            var order_id = $(this).attr("order-id");
            $.post('/api/organization/order/teacher', {}, function(result){
                if(result.code == '200'){
                    //弹出购买层
                    mylayer.init({
                        'title': "教师分配",
                        'content': result.view,
                        'class_name': 'org_order_teacher_layer',
                        'layerClose': false,
                        success: function(){
                            $(".order_teacher_syn_form").on("click", function(){
                                var form = $(this);
                                var e = event || window.event;
                                var elem = $(e.target || e.srcElement);
                                //判断是点击注册提交按钮
                                if (elem.hasClass("update_order_teacher")) {
                                    if(form.valid()){
                                        var data = form.serializeObject();
                                        data['order_id'] = order_id;
                                        $.postForm(elem, '/api/organization/order/teacher/syn', data, function(result){
                                            if(result.code == '200'){
                                                mylayer.showMessage('success', result.message, function(){
                                                    window.location.reload();
                                                });
                                            } else if(result.message){
                                                mylayer.showMessage('success', result.message);
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            }, 'json');
        });
    };
    //教师编辑验证规则
    app.teacherValid = {
        rules: {
            fullname: {
                required: true,
                maxlength: 50
            },
            phone: {
                required: true,
                phone: true,
                maxlength: 50
            }
        }
    };
    //初始化教师编辑弹出层
    app.initTeacherLayer = function(data) {
        $(".teacher_layer_overlay").show();
        $(".teacher_layer").addClass('show');
        $(".teacher_layer_box").addClass('show');
        $("html,body").css("overflow-y", "hidden");
        var layerBox_wrapper = $(".teacher_layer_box").find(".layerBox_wrapper");
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
            var form = $(".teacher_info_form");
            //设置表单值
            this.setForm(form, data);
        }
    };
    app.orderEvent = function(){
        $(".update_order_record_status").on("click", function(){
            var elem = $(this);
            var order_record_id = $(this).attr("data-id");
            var status = $(this).attr("data-status");
            var data = {'order_record_id': order_record_id, 'status': status};
            $.postForm(elem, "/api/organization/order/record/updatestatus", data, function (result) {
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
    }
    //设计器上传
    app.uploadOrganizationBanner = function(option){
        var self = this;
        if(option && option.elem){
            var form = option.elem.parent();
        }
        if(form && form.size() > 0){
            var data = {};
            var op = {
                type: 'POST',
                url: "/api/organization/uploadBanner",//提交地址
                dataType: 'json',//设置返回类型为json
                data: data,
                //提交完成事件
                success: function(result){
                    if(result.code == "200"){
                        window.location.reload();
                    } else if(result.message) {
                        mylayer.showMessage("error", result.message);
                    }
                },
                error: function(){
                    mylayer.showMessage("error", "Oh~ damn it,Please be patient and I'm trying to speed up.");
                },
                resetForm:true
            };
            require(['ajaxForm'], function(ajaxForm){
                form.ajaxForm(op);
                form.submit(); 
            });
        }
    };
    $(function () {
        app.init();
    });
});