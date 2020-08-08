require(["zepto","base","mylayer","validate"],function(e,r,t,i){var a={init:function(){var r=this,i=e("#distpicker"),a={province:i.attr("data-province"),city:i.attr("data-city"),district:i.attr("data-district")};require(["jquery","distpicker"],function(e){e("#distpicker").distpicker(a)});new FormValidator("store-info-form",r.storeFormRule,function(i,a){var o=e(a.target);if(o.find(".errormsg").html(""),i.length>0)return r.showValidatorError(i,o),!1;var n=o.serializeObject();if(!n.agreement)return t.showTip("同意店铺协议书，方可提交！",3e3,"error"),!1;var s=(t.showLoad(!0),new FormData(o[0]));e.ajax({url:"/api/store/saveInfo",type:"POST",data:s,processData:!1,contentType:!1,success:function(e){"Success"==e.code?(t.showTip(e.message,3e3,"success"),window.location.href="/account/store"):(t.hideLoad(),""==e.message?t.showTip("哎呀，发生错误了，等会再试下!",3e3,"error"):t.showTip(e.message,3e3,"error"))},error:function(r){t.hideLoad(),e.showRequestError(r)}})});if(e(".store-info-form").on("submit",function(){return!1}),e(".js-store-banner-upload").on("click",function(){e(".store-banner-file").click()}),e(".js-add-certificate-image").on("click",function(){var r=e(".certificate-image-item").size();return r>=10?(t.showTip("对不起,图片最多只能上传10张！",3e3,"error"),!1):void e(".certificate-image-upload-file").click()}),e(".certificate-image-upload-form").on("change",function(i){var a=e(".certificate-image-upload-form"),n=i||window.event,s=e(n.target||n.srcElement);if(!s.hasClass("certificate-image-file"))return!0;try{var c=s[0].files;if(c&&c.length>0){if(!c[0].type||e.inArray(c[0].type,["image/png","image/gif","image/jpg","image/jpeg"])==-1)return t.showTip(o.upload_image_format_tip,5e3,"error"),!1;if(c[0].size){var d=c[0].size/1048576;if(d>5)return t.showTip(o.upload_maximum_tip,5e3,"error"),!1}}var u=c[0],m=new FileReader;m.readAsDataURL(u),m.onload=function(t){var i=this.result,a=e(".product-image-item").size(),o=s.clone(),n=r.random()+a;o.attr("id","certificate_image_file_"+n).removeClass("certificate-image-upload-file"),e(".image-file").append(o);var c=e("#certificate-image-template"),d=[{image:i,file_id:n}],u=e.tmeplate(c,d);e(".js-add-certificate-image").before(u)};var l=a.find("input[type=file]");l.after(l.clone().val("")),l.remove()}catch(e){}}),e(document).on("click",".js-remove-certificate-image",function(){var r=e(this).closest(".certificate-image-item");if(r.size()>0){var t=r.attr("data-file-id");e("#certificate_image_file_"+t).remove(),r.remove()}}),e(".store-banner-upload-form").on("change",function(i){var a=e(this),n=i||window.event,s=e(n.target||n.srcElement);if(s.hasClass("store-banner-file"))try{var c=s[0].files;if(c&&c.length>0){if(!c[0].type||e.inArray(c[0].type,["image/png","image/gif","image/jpg","image/jpeg"])==-1)return t.showTip(o.upload_image_format_tip,5e3,"error"),!1;if(c[0].size){var d=c[0].size/1048576;if(d>3)return t.showTip(o.upload_maximum_tip,5e3,"error"),!1}}r.bannerUload(a)}catch(e){}}),e(document).on("click",".js-product-enable",function(){var r=e(this),i=r.attr("data-id"),a="";a="1"==r.attr("data-enable")?"1":"0";var o=e(this).attr("data-confirm");t.showConfirm(o,function(){e.ajaxPost("/api/store/enableProduct",{product_id:i,enable:a},function(e){"Success"==e.code?(t.showTip(e.message,3e3,"success"),window.location.reload()):""!=e.message&&t.showTip(e.message,3e3,"error")})})}),e(document).on("click",".js-product-delete",function(){var r=e(this),i=r.attr("data-id"),a=e(this).attr("data-confirm");t.showConfirm(a,function(){e.ajaxPost("/api/store/deleteProduct",{product_id:i},function(e){"Success"==e.code?window.location.reload():""!=e.message&&t.showTip(e.message,3e3,"error")})})}),e(document).on("click",".js-show-store-agreement",function(){var i=e("#store-agreement-template").html();t.init({content:i,close:!1,class_name:"layer-store-agreement",position:"top"}),r.pushAgreementState()}),e(document).on("click",".js-show-store-cert",function(){var i=e("#store-cert-template").html();t.init({content:i,close:!1,class_name:"layer-store-cert",position:"top"}),r.pushState()}),e(window).on("popstate",function(r){var i=e(".layer-store-cert");t.hideLayer(i);var i=e(".layer-store-agreement");t.hideLayer(i)}),this.orderCountInfo(),"undefined"!=typeof store_expire_tip&&"1"==store_expire_tip){var n=e("#store-expire-template").html();t.init({content:n,close:!1,class_name:"layer-store-expire",position:"center"});t.resizeLayer(e(".layer-store-expire"))}e(document).on("click",".js-product-codeimage",function(){var i=(e(this),e(this).attr("data-id"));e.ajaxPost("/api/product/codeImage",{product_id:i},function(i){if("Success"==i.code){var a=e("#code-image-template").html();t.init({content:a,close:!0,class_name:"layer-codeimage-box",position:"top",callback:function(){}}),e(".codeImage").attr("src",i.data.image),e.saveFile(i.data.image,r.random()+".jpg",function(){t.showTip("扫码购图片已保存到手机相册",3e3,"success")})}else""!=i.message&&t.showTip(i.message,3e3,"error")})});var s=e.getQueryVariable("to_product"),c=e.getQueryVariable("page");if(("1"==s||""!=c)&&e(".store-product-box").size()>0){var d=e(".store-product-box").offset().top-80;e(window).scrollTop(d)}e(document).on("click",".js-product-toShared",function(){var r=e(this),i=r.attr("data-id"),a=e(this).attr("data-confirm");t.showConfirm(a,function(){e.ajaxPost("/api/store/productToShare",{product_id:i},function(e){"Success"==e.code?t.showTip(e.message,5e3,"success"):""!=e.message&&t.showTip(e.message,6e3,"error")})})})},orderCountInfo:function(){return 0!=e(".order_status_number").size()&&void e.ajaxPost("/api/store/orderCount",{},function(r){var t=r.data;t.pending&&e(".order-pending-number").html(t.pending).show(),t.shipping&&e(".order-shipping-number").html(t.shipping).show(),t.shipped&&e(".order-shipped-number").html(t.shipped).show(),t.review&&e(".order-review-number").html(t.review).show(),t.refund&&e(".order-refund-number").html(t.refund).show()})},pushState:function(){if(history&&history.pushState){var r=window.location.search;r=e.changeURLArg(r,"link","cert");var t=window.location.pathname+r+window.location.hash;history.pushState({title:document.title,url:t},document.title,t)}},pushAgreementState:function(){if(history&&history.pushState){var r=window.location.search;r=e.changeURLArg(r,"link","agreement");var t=window.location.pathname+r+window.location.hash;history.pushState({title:document.title,url:t},document.title,t)}},random:function(){var e=parseInt((new Date).getTime()/1e3),r=Math.floor(1e5*Math.random());return e+""+r},storeFormRule:[{name:"name",rules:"required|max_length[50]",message:{required:"请输入店铺名称！"}},{name:"contact_user_name",rules:"required|max_length[50]",message:{required:"请输入姓名！"}},{name:"contact_phone",rules:"required|numeric|max_length[50]",message:{required:"请输入手机号码！",numeric:"请输入手机号码！"}},{name:"province_id",rules:"required",message:{required:"请选择省份！"}},{name:"city_id",rules:"required",message:{required:"请选择城市！"}},{name:"district_id",rules:"required",message:{required:"请选择区！"}},{name:"address",rules:"required",message:{required:"请输入地址！"}},{name:"business_entity_name",rules:"required",message:{required:"请输入主营主体"}},{name:"description",rules:"required",message:{required:"请输入预售商品描述"}}],showValidatorError:function(r,t){for(var i=0,a=r.length;i<a;i++){var o=e(r[i].element);0==o.next(".errormsg").size()&&o.after('<div class="errormsg"></div>'),o.next(".errormsg").html(r[i].message)}var n=t.offset().top;window.scrollTo(0,n)},bannerUload:function(r){var i=this,a=new FormData(r[0]);t.showLoad(),e.ajax({url:"/api/store/changebanner",type:"POST",data:a,processData:!1,contentType:!1,success:function(a){t.hideLoad(),i.uploadCallback(r),"Success"==a.code?window.location.reload():e.showRequestError(a)},error:function(a){t.hideLoad(),i.uploadCallback(r),e.showRequestError(a)}})},uploadCallback:function(e){var r=e.find("input[type=file]");r.after(r.clone().val("")),r.remove()}},o={upload_image_format_tip:"请选择png、jpg、jpeg格式图片！",upload_maximum_tip:"图片文件不能超过5M"};"function"==typeof a.init&&e(function(){a.init()})});