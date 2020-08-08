require(["zepto","base","mylayer","validate"],function(e,i,r,a){var t={init:function(){var i=this,a="";new FormValidator("save-store-product-form",i.productFormRule,function(a,t){var o=e(t.target);if(o.find(".errormsg").html(""),a.length>0)return i.showValidatorError(a,o),r.showTip("请检查您填写的数据",3e3,"error"),!1;var s=(o.serializeObject(),new FormData(o[0])),d=e(".sku-list-item"),n=e(".color_checked:checked"),c=e(".size_checked:checked");if(0==d.size())return r.showTip("请添加规格！",3e3,"error"),!1;var u=[],l=!0;if(d.each(function(){var i=e(this),a={},t=i.find(".sku_id").val(),o=i.find(".sku_size").val(),s=parseFloat(i.find(".sku_price").val()),d=parseFloat(i.find(".sku_market_price").val()),p=parseFloat(i.find(".sku_share_integral").val()),m=i.find(".sku_shipping").val(),f=i.find(".sku_stock").val(),g=/^\+?[0-9][0-9]*$/;if(!g.test(f))return r.showTip("对不起库存必须是整数",3e3,"error"),l=!1,!1;""==m&&(m=0);var h=i.find(".sku_image"),v=h.attr("data-image-file-id"),_=h.attr("data-image-path");if(s<=0)return r.showTip("对不起活动价要大于0",3e3,"error"),l=!1,!1;if(s>d&&d>0)return r.showTip("活动价要小于原价",3e3,"error"),l=!1,!1;if(p<0)return l=!1,r.showTip("共享积分要大于等于0",3e3,"error"),!1;var k=.02*s,w=.5*s;if(p<k)return l=!1,r.showTip("共享积分不能小于活动价的2%",3e3,"error"),!1;if(p>w)return l=!1,r.showTip("共享积分不能超过活动价的一半",3e3,"error"),!1;if(f<0)return r.showTip("库存要大于等于0",3e3,"error"),l=!1,!1;var a={sku_id:t,price:s,market_price:d,share_integral:p,shipping:m,stock:f};if(n.size()>0){var y=i.find(".sku_color").val();if(""==y)return r.showTip("请输入颜色！",3e3,"error"),l=!1,!1;a.color=y}if(c.size()>0){var o=i.find(".sku_size").val();if(""==o)return r.showTip("请输入规格！",3e3,"error"),l=!1,!1;a.size=o}return e(".main-image-file .product-image-file").each(function(i,r){var t=e(this).attr("id");"product_image_file_"+v==t&&(a.image_file=i)}),v||_?(_&&(a.image_path=_),void u.push(a)):(r.showTip("请选择规格图片！",3e3,"error"),l=!1,!1)}),!l)return!1;r.showLoad(!0);s.append("skus",JSON.stringify(u));var p="add"==save_type?"/api/store/addProduct":"/api/store/saveProduct";e.ajax({url:p,type:"POST",data:s,processData:!1,contentType:!1,success:function(i){"Success"==i.code?(r.showTip(i.message,3e3,"success"),window.location.href="/account/store/products"):(r.hideLoad(),e.showRequestError(i))},error:function(i){r.hideLoad(),e.showRequestError(i)}})});e(".save-store-product-form").on("submit",function(){return!1}),e(document).on("click",".js-add-product-image",function(){var i=e(this).attr("data-type");if("description"==i){var a=e(".js-product-image-list .product-image-item").size();if(a>=100)return r.showTip("对不起,图片最多只能上传100张！"),!1}e(".product-image-upload-file").click()}),e(".js-add-description-image").on("click",function(){var i=e(this).attr("data-type");if("description"==i){var a=e(".js-description-image-list .product-image-item").size();if(a>=20)return r.showTip("对不起,图片最多只能上传20张！"),!1}e(".description-image-upload-file").click()}),e(".product-image-upload-form").on("change",function(t){var s=e(".product-image-upload-form"),d=t||window.event,n=e(d.target||d.srcElement);if(!n.hasClass("product-image-upload-file"))return!0;try{var c=n[0].files;if(c&&c.length>0){if(!c[0].type||e.inArray(c[0].type,["image/png","image/gif","image/jpg","image/jpeg"])==-1)return r.showTip(o.upload_image_format_tip,5e3,"error"),!1;if(c[0].size){var u=c[0].size/1048576;if(u>5)return r.showTip(o.upload_maximum_tip,5e3,"error"),!1}}if("edit"==save_type)i.productImageUload(s);else{var l=c[0],p=new FileReader;p.readAsDataURL(l),p.onload=function(t){var o=this.result,s=e(".js-product-image-list .product-image-item").size(),d=n,c=i.random()+s;d.attr("id","product_image_file_"+c).removeClass("product-image-upload-file"),d.appendTo(e(".main-image-file"));var u=e("#product-image-template"),l=[{image:o,file_id:c}],p=e.tmeplate(u,l);e(".js-add-product-image").before(p);var m=o;a.find("img").attr("src",m);var f=e(".layer-product-image-list");r.hideLayer(f),a.attr("data-image-file-id",c)};var m=s.find("input[type=file]");m.after(m.clone().val("")),m.remove()}}catch(e){}}),e(".description-image-upload-form").on("change",function(a){var t=e(".description-image-upload-form"),s=a||window.event,d=e(s.target||s.srcElement);if(!d.hasClass("description-image-upload-file"))return!0;try{var n=d[0].files;if(n&&n.length>0){if(!n[0].type||e.inArray(n[0].type,["image/png","image/gif","image/jpg","image/jpeg"])==-1)return r.showTip(o.upload_image_format_tip,5e3,"error"),!1;if(n[0].size){var c=n[0].size/1048576;if(c>5)return r.showTip(o.upload_maximum_tip,5e3,"error"),!1}}var u=n[0],l=new FileReader;l.readAsDataURL(u),l.onload=function(r){var a=this.result,t=e(".js-description-image-list .product-image-item").size(),o=d,s=i.random()+t;o.attr("id","product_image_file_"+s).removeClass("description-image-upload-file"),o.appendTo(e(".product-description-image-file"));var n=e("#product-image-template"),c=[{image:a,file_id:s}],u=e.tmeplate(n,c);e(".js-add-description-image").before(u)};var p=t.find("input[type=file]");p.after(p.clone().val("")),p.remove()}catch(e){}}),e(document).on("click",".js-remove-product-image",function(){var i=e(this).closest(".product-image-item");if(i.size()>0){var r=i.attr("data-file-id");r&&e("#product_image_file_"+r).remove(),i.remove()}var a=e(this).attr("data-product-id"),t=e(this).attr("data-id");t&&e.ajaxPost("/api/store/removeProductImage",{product_id:a,product_image_id:t},function(e){"Success"==e.code&&window.location.reload()})}),e(".add_sku").on("click",function(){var i=e(".sku-list-item").first().clone();if(0==i.size()){var r=e("#sku-item-template").html();e(".sku-list-box").append(r)}else i.find(".sku_id").val(""),e(".sku-list-box").append(i)}),e(document).on("click",".js-remove-sku-item",function(){if(1==e(".sku-list-item").size())return r.showTip("至少一个规格属性！"),!1;var i=e(this).closest(".sku-list-item");if(i){var a=i.find(".sku_id").val();if(a){var t=e(this).attr("data-product-id");r.showConfirm("您确认删除此规格？",function(){e.ajaxPost("/api/store/deleteProductSku",{product_id:t,product_sku_id:a},function(e){"Success"==e.code?i.remove():e.message&&r.showTip(e.message,3e3,"error")})})}else i.remove()}}),e(".attributes_checked").on("click",function(){var i=e(this).attr("data-value");if(e(this).is(":checked"))"color"==i&&e(".sku_color_td").show(),"size"==i&&e(".sku_size_td").show();else{if(0==e(".attributes_checked:checked").size())return!1;"color"==i&&e(".sku_color_td").hide(),"size"==i&&e(".sku_size_td").hide()}}),e(document).on("click",".sku_image",function(){a=e(this);var i=e("#product-image-list-template").html(),t=e(".js-product-image-list").html();r.init({content:i,close:!1,class_name:"layer-product-image-list",position:"center",success:function(){}}),e(".product-image-list-box").find(".product-image-list").html(t)}),e(document).on("click",".product-image-list-box .product-image-item",function(){var i=e(this).find("img").attr("src");a.find("img").attr("src",i);var t=e(this).attr("data-file-id"),o=e(this).attr("data-image-path"),s=e(".layer-product-image-list");r.hideLayer(s),a.attr("data-image-path",o),a.attr("data-image-file-id",t)})},random:function(){var e=parseInt((new Date).getTime()/1e3),i=Math.floor(1e5*Math.random());return e+""+i},productFormRule:[{name:"name",rules:"required|max_length[100]",message:{required:"请输入产品名称！"}},{name:"category_id",rules:"required",message:{required:"请选择分类！"}},{name:"integral_pay",rules:"required",message:{required:"请选择是否接受有赏积分支付"}}],showValidatorError:function(i,r){for(var a=0,t=i.length;a<t;a++){var o=e(i[a].element);0==o.next(".errormsg").size()&&o.after('<div class="errormsg"></div>'),o.next(".errormsg").html(i[a].message)}var s=r.offset().top;window.scrollTo(0,s)},productImageUload:function(i){var a=this,t=new FormData(i[0]);r.showLoad(),e.ajax({url:"/api/store/addProductImage",type:"POST",data:t,processData:!1,contentType:!1,success:function(t){if(r.hideLoad(),a.uploadCallback(i),"Success"==t.code){var o=e("#product-image-template"),s=[{image_id:t.data.image_id,image:t.data.image_link,image_path:t.data.image_path}],d=e.tmeplate(o,s);e(".js-add-product-image").before(d)}else e.showRequestError(t)},error:function(t){r.hideLoad(),a.uploadCallback(i),e.showRequestError(t)}})},descriptionImageUload:function(){var i=this,a=new FormData(form[0]);r.showLoad(),e.ajax({url:"/api/store/addProductImage",type:"POST",data:a,processData:!1,contentType:!1,success:function(a){r.hideLoad(),i.uploadCallback(form),"Success"==a.code?window.location.reload():e.showRequestError(a)},error:function(a){r.hideLoad(),i.uploadCallback(form),e.showRequestError(a)}})},productVideoUload:function(i){var a=this,t=new FormData(i[0]);r.showLoad(),e.ajax({url:"/api/store/editProductVideo",type:"POST",data:t,processData:!1,contentType:!1,success:function(t){r.hideLoad(),a.uploadCallback(i),"Success"==t.code?window.location.reload():e.showRequestError(t)},error:function(t){r.hideLoad(),a.uploadCallback(i),e.showRequestError(t)}})},uploadCallback:function(e){var i=e.find("input[type=file]");i.after(i.clone().val("")),i.remove()}},o={upload_image_format_tip:"请选择png、jpg、jpeg格式图片！",upload_maximum_tip:"图片文件不能超过5M"};"function"==typeof t.init&&e(function(){t.init()})});