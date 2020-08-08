require(["zepto","base","mylayer"],function(e,i,t){var a={init:function(){r.init()}},r={init:function(){var i=this;e(".js-rating-star li").on("click",function(){e(this).addClass("select");var i=e(this).index();e(".js-rating-star li").each(function(){var t=e(this).index();t<=i?e(this).addClass("select"):e(this).removeClass("select")});var t=e(this).parent().attr("data-id"),a=e(".order-product-review-"+t);a.find(".review_rate_value").val(i+1)}),e(".review_text").on("keyup",function(){var i=e(this),t=e.trim(i.val()),a="";""==t&&(a=o.require_content),(/<script/.test(t)||/<style/.test(t))&&(a=o.review_content_check_tip),""!=a?i.parent().find(".errormsg").html(a).show():i.parent().find(".errormsg").html("").hide()}),e(".js-submit-order-review").on("click",function(){var i=e(".order-review"),a=i.attr("data-action"),r=!0;i.find(".review_text").each(function(){var i=e(this),t=e.trim(i.val()),a="";""==t&&(a=o.require_content,r=!1),(/<script/.test(t)||/<style/.test(t))&&(a=o.review_content_check_tip,r=!1),""!=a?(r=!1,i.parent().find(".errormsg").html(a).show()):i.parent().find(".errormsg").html("").hide()}),r&&t.showConfirm(o.review_submit_confirm,function(){e(".upload-image-file").attr("name",""),e(".upload-review-image-item").each(function(){var i=e(this).attr("data-file-id"),t=e("#review_file_"+i);if(t.size()>0){var a=t.attr("data-name");t.attr("name",a)}});var r=new FormData(i[0]);e(".upload-image-file").attr("name",function(){return e(this).attr("data-name")}),t.showLoad(!0),e.ajax({url:a,type:"POST",data:r,processData:!1,contentType:!1,success:function(i){"Success"==i.code?window.location.reload():"HAS_BEEN_COMMENT"==i.code?t.showMessage(i.message,function(){window.location.reload()}):e.showRequestError(i),t.hideLoad()},error:function(i){t.hideLoad(),e.showRequestError(i)}})})}),e(".js-add-review-image").on("click",function(){var i=e(this).attr("data-id"),a=e(".order-product-review-"+i),r=a.find(".upload-review-image-item"),n=r.size();if(n>=3)return t.showTip(o.review_image_limit_tip,3e3,"error"),!1;var d=n;a.find(".upload-image-file").eq(d).click()}),e(".upload-image-file").on("change",function(){var a=e(this),r=(e(this).attr("data-index"),e(this).attr("data-id")),n=e(".order-product-review-"+r);try{var d=a[0].files;if(d&&d.length>0){if(!d[0].type||e.inArray(d[0].type,["image/png","image/gif","image/jpg","image/jpeg"])==-1)return t.showTip(o.upload_image_format_tip,5e3,"error"),!1;if(d[0].size){var s=d[0].size/1048576;if(s>3)return t.showTip(o.upload_maximum_tip,5e3,"error"),!1}n.find(".upload-image-list");if("undefined"!=typeof FileReader){var c=new FileReader;c.onload=function(e){var t='<img  src="'+e.target.result+'" />';i.addImgaeCallback(a,t)},c.readAsDataURL(d[0])}else{var l='<div class="img" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\''+d.value+"'\"></div>";i.addImgaeCallback(a,l)}}}catch(e){}}),e(document).on("click",".js-remove-review-image",function(){var i=e(this).closest(".upload-review-image-item"),t=i.attr("data-file-id"),a=e(this).closest(".order-product-review-item");i.remove(),a.find("#review_file_"+t).val("")})},random:function(){var e=parseInt((new Date).getTime()/1e3),i=Math.floor(1e5*Math.random());return e+""+i},addImgaeCallback:function(e,i){var t=e.index(),a=e.closest(".order-product-review-item"),r=a.find(".upload-image-list"),o=(e.clone(),e.attr("data-id")),n=o+"_"+this.random()+t;e.attr("id","review_file_"+n),r.append('<div class="upload-review-image-item" data-file-id="'+n+'"><div class="review-image-item-box">'+i+'<a class="remove js-remove-review-image" href="javascript:void(0)">×</a></div></div>')}},o={require_content:"请输入评论内容",review_content_check_tip:"请输入评论内容",review_image_limit_tip:e.tran("tip.review_image_limit_tip"),review_submit_confirm:"确认提交评论？",upload_image_format_tip:e.tran("tip.upload_image_format_tip"),upload_maximum_tip:e.tran("tip.upload_maximum_tip")+" 3 MB"};"function"==typeof a.init&&e(function(){a.init()})});