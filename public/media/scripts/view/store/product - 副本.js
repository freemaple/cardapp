require(["zepto","base","mylayer","validate"],function(e,i,t,r){var a={init:function(){var i=this,r="";new FormValidator("save-store-product-form",i.productFormRule,function(r,a){var o=e(a.target);if(o.find(".errormsg").html(""),r.length>0)return i.showValidatorError(r,o),t.showTip("请检查您填写的数据",3e3,"error"),!1;var s=(o.serializeObject(),new FormData(o[0])),n=[],d=e(".sku-list-item"),c=e(".color_checked:checked"),u=e(".size_checked:checked");if(0==d.size())return t.showTip("请添加规格！",3e3,"error"),!1;var l=!0;if(d.each(function(){var i=e(this),r={},a=i.find(".sku_size").val(),o=parseFloat(i.find(".sku_price").val()),s=parseFloat(i.find(".sku_market_price").val()),d=i.find(".sku_shipping").val(),m=i.find(".sku_stock").val(),p=i.find(".sku_image"),f=p.attr("data-image-file-id");if(0==o)return t.showTip("活动价要大于0",3e3,"error"),l=!1,!1;if(o>s&&s>0)return t.showTip("活动价要小于原价",3e3,"error"),l=!1,!1;if(m<0)return t.showTip("库存要大于等于0",3e3,"error"),l=!1,!1;var r={price:o,market_price:s,shipping:d,stock:m};if(c.size()>0){var g=i.find(".sku_color").val();if(""==g)return t.showTip("请输入颜色！",3e3,"error"),l=!1,!1;r.color=g}if(u.size()>0){var a=i.find(".sku_size").val();if(""==a)return t.showTip("请输入规格！",3e3,"error"),l=!1,!1;r.size=a}return e(".main-image-file .product-image-file").each(function(i,t){var a=e(this).attr("id");"product_image_file_"+f==a&&(r.image_file=i)}),f?void n.push(r):(t.showTip("请选择图片！",3e3,"error"),l=!1,!1)}),!l)return!1;t.showLoad(!0);s.append("skus",JSON.stringify(n)),e.ajax({url:"/api/store/addProduct",type:"POST",data:s,processData:!1,contentType:!1,success:function(i){"Success"==i.code?(t.showTip(i.message,3e3,"success"),window.location.href="/account/store"):(t.hideLoad(),e.showRequestError(i))},error:function(i){t.hideLoad(),e.showRequestError(i)}})});e(".store-product-form").on("submit",function(){return!1}),e(document).on("click",".js-add-product-image",function(){var i=e(this).attr("data-type");if("description"==i){var r=e(".js-product-image-list .product-image-item").size();if(r>=10)return t.showTip("对不起,图片最多只能上传10张！"),!1}e(".product-image-upload-file").click()}),e(".js-add-description-image").on("click",function(){var i=e(this).attr("data-type");if("description"==i){var r=e(".js-description-image-list .product-image-item").size();if(r>=10)return t.showTip("对不起,图片最多只能上传10张！"),!1}e(".description-image-upload-file").click()}),e(".product-image-upload-form").on("change",function(a){var s=e(".product-image-upload-form"),n=a||window.event,d=e(n.target||n.srcElement);if(!d.hasClass("product-image-upload-file"))return!0;try{var c=d[0].files;if(c&&c.length>0){if(!c[0].type||e.inArray(c[0].type,["image/png","image/gif","image/jpg","image/jpeg"])==-1)return t.showTip(o.upload_image_format_tip,5e3,"error"),!1;if(c[0].size){var u=c[0].size/1048576;if(u>5)return t.showTip(o.upload_maximum_tip,5e3,"error"),!1}}var l=c[0],m=new FileReader;m.readAsDataURL(l),m.onload=function(a){var o=this.result,s=e(".js-product-image-list .product-image-item").size(),n=d.clone(),c=i.random()+s;n.attr("id","product_image_file_"+c).removeClass("product-image-upload-file"),e(".main-image-file").append(n);var u=e("#product-image-template"),l=[{image:o,file_id:c}],m=e.tmeplate(u,l);e(".js-add-product-image").before(m);var p=o;r.find("img").attr("src",p);var f=e(".layer-product-image-list");t.hideLayer(f),r.attr("data-image-file-id",c)};var p=s.find("input[type=file]");p.after(p.clone().val("")),p.remove()}catch(e){}}),e(".description-image-upload-form").on("change",function(r){var a=e(this),s=r||window.event,n=e(s.target||s.srcElement);if(!n.hasClass("description-image-upload-file"))return!0;try{var d=n[0].files;if(d&&d.length>0){if(!d[0].type||e.inArray(d[0].type,["image/png","image/gif","image/jpg","image/jpeg"])==-1)return t.showTip(o.upload_image_format_tip,5e3,"error"),!1;if(d[0].size){var c=d[0].size/1048576;if(c>5)return t.showTip(o.upload_maximum_tip,5e3,"error"),!1}}var u=d[0],l=new FileReader;l.readAsDataURL(u),l.onload=function(t){var r=this.result,a=e(".js-description-image-list .product-image-item").size(),o=n.clone(),s=i.random()+a;o.attr("id","product_image_file_"+s).removeClass("description-image-upload-file"),e(".product-description-image-file").append(o);var d=e("#product-image-template"),c=[{image:r,file_id:s}],u=e.tmeplate(d,c);e(".js-add-description-image").before(u)};var m=a.find("input[type=file]");m.after(m.clone().val("")),m.remove()}catch(e){}}),e(document).on("click",".js-remove-product-image",function(){var i=e(this).closest(".product-image-item");if(i.size()>0){var t=i.attr("data-file-id");e("#product_image_file_"+t).remove(),i.remove()}}),e(".add_attributes").on("click",function(){var i=e("#product-attributes-template").html();e(".product_attributes_box").append(i)}),e(".add_sku").on("click",function(){var i=e(".sku-list-item").first().clone();if(0==i.size()){var t=e("#sku-item-template").html();e(".sku-list-box").append(t)}else e(".sku-list-box").append(i)}),e(document).on("click",".js-remove-sku-item",function(){if(1==e(".sku-list-item").size())return t.showTip("至少一个规格属性！"),!1;var i=e(this).closest(".sku-list-item");i.remove()}),e(".attributes_checked").on("click",function(){var i=e(this).attr("data-value");if(e(this).is(":checked"))"color"==i&&e(".sku_color_td").show(),"size"==i&&e(".sku_size_td").show();else{if(0==e(".attributes_checked:checked").size())return!1;"color"==i&&e(".sku_color_td").hide(),"size"==i&&e(".sku_size_td").hide()}}),e(document).on("keydown",".attributes_value_input",function(){var i=event||window.event,t=i.keyCode||i.which||i.charCode,r=e(this);if(13==t){var a=r.val(),o='<li style="border: 1px solid #eeeeee;padding: 10px;display:inline-block"><span>'+a+"</span></li>",s=r.closest(".product-attributes-item");return s.find(".attributes_value_list").append(o),r.val(""),!0}return!0}),e(document).on("click",".sku_image",function(){r=e(this);var i=e("#product-image-list-template").html(),a=e(".js-product-image-list").html();t.init({content:i,close:!1,class_name:"layer-product-image-list",position:"center",success:function(){}}),e(".product-image-list-box").find(".product-image-list").html(a),e(".product-image-list-box").find(".product-image-list").html(a)}),e(document).on("click",".product-image-list-box .product-image-item",function(){var i=e(this).find("img").attr("src");r.find("img").attr("src",i);var a=e(this).attr("data-file-id"),o=e(".layer-product-image-list");t.hideLayer(o),r.attr("data-image-file-id",a)})},random:function(){var e=parseInt((new Date).getTime()/1e3),i=Math.floor(1e5*Math.random());return e+""+i},productFormRule:[{name:"name",rules:"required|max_length[50]",message:{required:"请输入产品名称！"}},{name:"category_id",rules:"required",message:{required:"请选择分类！"}},{name:"description",rules:"required",message:{required:"请输入描述！"}},{name:"integral_pay",rules:"required",message:{required:"请选择是否接受有赏积分支付"}}],showValidatorError:function(i,t){for(var r=0,a=i.length;r<a;r++){var o=e(i[r].element);0==o.next(".errormsg").size()&&o.after('<div class="errormsg"></div>'),o.next(".errormsg").html(i[r].message)}var s=t.offset().top;window.scrollTo(0,s)},uploadCallback:function(e){var i=e.find("input[type=file]");i.after(i.clone().val("")),i.remove()}},o={upload_image_format_tip:"请选择png、jpg、jpeg格式图片！",upload_maximum_tip:"图片文件不能超过5M"};"function"==typeof a.init&&e(function(){a.init()})});