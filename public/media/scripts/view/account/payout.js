require(["zepto","base","mylayer","validate","scrollComponent"],function(e,r,a,i,s){var o={init:function(){var r=this;new FormValidator("payout-apply-form",r.postValid,function(i,s){var o=e(s.target);if(o.find(".errormsg").html(""),i.length>0)return r.showValidatorError(i,o),!1;var t=o.serializeObject();a.showLoad(!0);return e.ajaxPost("/api/payout/apply",t,function(r){"Success"==r.code?(a.showTip(r.message,3e3,"success"),window.location.href="/account/payout/index",a.hideLoad()):(a.hideLoad(),e.showRequestError(r))},function(r){a.hideLoad(),e.showRequestError(r)}),!1});e(".js-send-phonecode").on("click",function(){var i=e(this);if(i.hasClass("disabled"))return!1;if(i.hasClass("is_send"))return!1;var s=e.trim(e(".user_phone").val());i.addClass("disabled"),e(".code_send_tip_info").hide(),e.ajaxPost("/api/auth/phone/code",{phone:s,type:"payout"},function(s){"Success"==s.code?(e(".verificate_code_time").text("60"),e(".code_send_tip_info").show(),e(".code_send_tip").html(s.message),r.VerificateCodeInterval(i)):""!=s.message?(i.removeClass("disabled"),a.showTip(s.message,"3000","error")):i.removeClass("disabled")})}),e(".payout-apply-form").on("submit",function(){return!1})},postValid:[{name:"fullname",rules:"required",message:{required:"请输入姓名"}},{name:"amount",rules:"required|numeric",message:{required:"请输入金额",numeric:"请输入数字金额"}},{name:"card_number",rules:"required",message:{required:"请输入银行卡号"}},{name:"card_bank",rules:"required",message:{required:"请输入开户行"}},{name:"card_bank",rules:"required",message:{required:"请输入交易密码"}},{name:"code",rules:"required",message:{required:"请输入验证码"}}],VerificateCodeInterval:function(r){var a=60,i=setInterval(function(){a>0&&(a-=1,e(".verificate_code_time").text(a)),a<=0&&(clearInterval(i),r.removeClass("disabled"),e(".code_send_tip_info").hide())},1e3)},showValidatorError:function(r,a){for(var i=0,s=r.length;i<s;i++){var o=e(r[i].element);0==o.next(".errormsg").size()&&o.after('<div class="errormsg"></div>'),o.next(".errormsg").html(r[i].message)}var t=a.offset().top;window.scrollTo(0,t)}},t={init:function(){var r=this,a=e(".js-payout-apply-list-box");s.init(),s.setScrollItem(a),s.setCallback(function(e,a){r.scrollLoadCallback(e,a)})},scrollLoadCallback:function(e,r){r.find(".js-payout-apply-list").append(e)}};"function"==typeof o.init&&e(function(){o.init(),t.init()})});