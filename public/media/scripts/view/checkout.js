require(["zepto","base","mylayer","loginReg","validate"],function(e,r,t,o,i){var a={};a.init=function(){var r=this;e(".js-checkout-back").on("click",function(){var r=e.getQueryVariable("goods_id");if(r)window.location.href="/product/"+r;else{var t=document.referrer,o=window.location.hostname;""==t||t==window.location.href||t.indexOf(o)==-1?(t="/",window.location.href=t):window.history.go(-1)}}),e(".qty-input").on("change",function(){var r=e(this),o=parseInt(r.val()),i=/^[0-9]*[1-9][0-9]*$/;i.test(o)||(r.val("1"),o=1);var a=window.location.href,a=e.changeURLArg(a,"qty",o);t.showLoad(),window.location.href=a}),e(".payment-item").on("click",function(){e(this).addClass("selected").siblings(".payment-item").removeClass("selected")}),e(".checkout-pay-submit").on("click",function(){var o=r.getOrderData(),i=o.address_id;return!i||i<=0?(t.showTip(n.no_address_tip,3e3,"error"),!1):(t.showLoad(!0),void(r.payOrderId?(o.order_id=r.payOrderId,r.orderSubmit(o)):e.ajaxPost("/api/order/create",o,function(o){if("Success"==o.code){if(o.data.order_id&&(r.payOrderId=o.data.order_id),o.data.is_pay)return window.location.href="/account/order/pay/success/"+o.data.order_no,!0;e.setCookie("is_auto_pay","1"),window.location.href="/checkout/order/pay/"+o.data.order_no+"?is_auto_pay=1"}else t.hideLoad(),e.showRequestError(o)},function(r){t.hideLoad(),e.showRequestError(r)})))}),e(".use_integral_checked").on("click",function(){var r=window.location.href,o=!!e(this).is(":checked");r=o?e.changeURLArg(r,"use_integral","1"):e.delParam(r,"use_integral"),t.showLoad(),window.location.href=r}),e(window).on("popstate",function(r){var t=e.getQueryVariable("goods_id");t&&(window.location.href="/product/"+t)}),this.checkIsCheckout()},a.orderSubmit=function(e){var r="/checkout/order/pay/"+order_id+"?payment_method="+e.payment;window.location.href=r+"&is_auto_pay=1"},a.checkIsCheckout=function(){var e=(window.referrer,this.getCheckoutPayOrder());if(e&&""!=e){var r="/checkout/order/pay/"+e;window.location.href=r}},a.setCheckoutPay=function(r){if(window.sessionStorage){var t=e.getQueryVariable("basket_code");window.sessionStorage.setItem("basket_code:pay"+t,r)}},a.getCheckoutPayOrder=function(){if(window.sessionStorage){var r=e.getQueryVariable("basket_code"),t=window.sessionStorage.getItem("basket_code:pay"+r);if(t&&""!=t)return t}return!1},a.getOrderData=function(){var r=[];e(".sku-qty-value").each(function(){var t=e.trim(e(this).val());r.push(t)});var t=e(".order-comment-value").val(),o=e(".order_form_data").serializeObject();o.qtys=r;var i=e(".payment-item.selected").attr("data-code");return o.payment=i,o.comment=t,o},a.pushOrderPayState=function(e){if(history&&history.pushState){var r="/user/order/pay/"+e;history.pushState({title:document.title,url:r},document.title,r)}};var n={no_address_tip:"请先维护地址",request_error_tip:e.tran("tip.request_error_tip")},s={init:function(){var r=this;require(["jquery","distpicker"],function(e){e("#distpicker1").distpicker("destroy")});new FormValidator("shipping-address-form",r.addressFormRule,function(o,i){var a=e(i.target);if(a.find(".errormsg").html(""),o.length>0)return r.showValidatorError(o,a),!1;var n=(a.attr("data-action"),a.serializeObject()),s=(t.showLoad(!0),a.find("[name=is_default]").prop("checked")),d=s?"1":"0";n.is_default=d,t.showLoad(!0),e.ajaxPost("/api/address/add",n,function(r){t.hideLoad(),"Success"==r.code?window.location.href=window.location.href:e.showRequestError(r)})});e(".shipping-address-form").on("submit",function(){return!1})},addressFormRule:[{name:"fullname",rules:"required|is_letter|max_length[50]",message:{required:"请输入姓名！"}},{name:"phone",rules:"required|numeric|max_length[50]",message:{required:"请输入手机号码！",numeric:"请输入手机号码！"}},{name:"province",rules:"required",message:{required:"请选择省份！"}},{name:"city",rules:"required",message:{required:"请选择城市！"}},{name:"district",rules:"required",message:{required:"请选择区！"}},{name:"address",rules:"required",message:{required:"请输入地址！"}},{name:"zip",rules:"required",message:{required:"请输入邮编！"}}],showValidatorError:function(r,t){for(var o=0,i=r.length;o<i;o++){var a=e(r[o].element);0==a.next(".errormsg").size()&&a.after('<div class="errormsg"></div>'),a.next(".errormsg").html(r[o].message)}var n=t.offset().top;window.scrollTo(0,n)}};"function"==typeof a.init&&e(function(){a.init(),s.init()})});