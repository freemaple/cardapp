require(["jquery","validate","mylayer"],function(e,r,a){var n={init:function(){var r=this;e.validator.addMethod("phone",function(e,r){var a=/^[()+0-9_\-\s]*$/;return this.optional(r)||a.test(e)},e.tran("valid.valid_phone","请输入有效的电话号码")),e(".account_info_form").validate(this.change_info_valid),e(".account_info_form").on("click",function(a){var n=e(this),i=a||window.event,o=e(i.target||i.srcElement);o.hasClass("save_account_info")&&r.doChangeInfo(n,o)}),e(".alert_password_form").validate(this.alert_pwd_valid),e(".alert_password_form").on("click",function(a){var n=e(this),i=a||window.event,o=e(i.target||i.srcElement);o.hasClass("save_pwd")&&r.doAlertPwd(n,o)}),e.validatorMessageConfig()},change_info_valid:{rules:{public_name:{required:!0,maxlength:50},phone_number:{maxlength:50,phone:!0}}},alert_pwd_valid:{rules:{current_password:{required:!0},new_password:{required:!0,min:6,maxlength:50}}},review_form_valid:{rules:{score:{required:!0},content:{required:!0}}},course_reserve_form_valid:{rules:{tearcher_time:{required:!0},class_number:{required:!0}}},doChangeInfo:function(r,n){if(r.valid()){var i=r.serializeObject();e.postForm(n,"/api/account/changeinfo",i,function(r){"0x0000"==r.code?(a.showMessage("success",r.message),"undefined"!=typeof i.public_name&&e(".account_nick_name").text(i.public_name)):a.showMessage("error",r.message)})}},doAlertPwd:function(r,n){var i=!0,o=e.trim(r.find(".new_password").val()),s=e.trim(r.find(".confirm_new_pwd").val());if(s!=o?(r.find(".confirm_password_error").show(),i=!1):r.find(".confirm_password_error").hide(),r.valid()&&i){var t=r.serializeObject();e.postForm(n,"/api/account/changepwd",t,function(n){"0x0000"==n.code?(a.showMessage("success",n.message),e(r)[0].reset()):a.showMessage("error",n.message)})}}};e(function(){n.init()})});