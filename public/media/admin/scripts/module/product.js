var app = {
    init: function(){
        var self = this;
        var current_sku_image = '';
        //表单验证
        var validator = new FormValidator('save-product-form', self.productFormRule, function(errors, event) {
            var form = $(event.target);
            form.find('.errormsg').html('');
            if (errors.length > 0) {
                self.showValidatorError(errors, form);
                mylayer.showTip('请检查您填写的数据', 3000, 'error');
                return false;
            }
            var data = form.serializeObject();
            var formData = new FormData(form[0]);
            var sku_list_item = $(".sku-list-item");
            var is_color = $(".color_checked:checked");
            var is_size = $(".size_checked:checked");
            if(sku_list_item.size() == 0){
                mylayer.showTip('请添加规格！', 3000, 'error');
                return false;
            }
            var skus = [];
            var flag = true;
            sku_list_item.each(function(){
                var elem = $(this);
                var sku = {};
                var sku_id = elem.find('.sku_id').val();
                var sku_size = elem.find('.sku_size').val();
                var sku_price = parseFloat(elem.find('.sku_price').val());
                var sku_market_price = parseFloat(elem.find('.sku_market_price').val());
                var sku_share_integral = parseFloat(elem.find('.sku_share_integral').val());
                var sku_shipping = elem.find('.sku_shipping').val();
                var sku_stock = elem.find('.sku_stock').val();
                var r = /^\+?[1-9][0-9]*$/;　
                if(!r.test(sku_stock)){
                    mylayer.showTip('对不起库存必须是整数', 3000, 'error');
                    flag = false;
                    return false;
                }
                if(sku_shipping == ''){
                    sku_shipping = 0;
                }
                var sku_image = elem.find('.sku_image');
                var file_id = sku_image.attr('data-image-file-id');
                var image_path = sku_image.attr('data-image-path');
                if(!sku_price || sku_price <=0){
                    mylayer.showTip('对不起真正售价要大于0', 3000, 'error');
                    flag = false;
                    return false;
                }
                if(sku_price > sku_market_price && sku_market_price > 0){
                    mylayer.showTip('真正售价要小于原价', 3000, 'error');
                    flag = false;
                    return false;
                }
                if(sku_stock < 0){
                    mylayer.showTip('库存要大于等于0', 3000, 'error');
                    flag = false;
                    return false;
                }
                if(sku_share_integral >= sku_price){
                    mylayer.showTip('共享积分不能大于等于售价', 3000, 'error');
                    flag = false;
                    return false;
                }
                var sku = {
                    'sku_id': sku_id,
                    'price': sku_price,
                    'market_price': sku_market_price,
                    'shipping': sku_shipping,
                    'share_integral': sku_share_integral,
                    'stock': sku_stock
                };
                if(is_color.size() > 0){
                    var sku_color = elem.find('.sku_color').val();
                    if(sku_color == ''){
                        mylayer.showTip('请输入颜色！', 3000, 'error');
                        flag = false;
                        return false;
                    }
                    sku['color'] = sku_color;
                }
                if(is_size.size() > 0){
                    var sku_size = elem.find('.sku_size').val();
                    if(sku_size == ''){
                        mylayer.showTip('请输入规格！', 3000, 'error');
                        flag = false;
                        return false;
                    }
                    sku['size'] = sku_size;
                }
                $(".main-image-file .product-image-file").each(function(ikey, list){
                    var id = $(this).attr('id');
                    if(("product_image_file_" + file_id) ==  id){
                        sku['image_file'] = ikey;
                    }
                });
                if(!file_id && !image_path){
                    mylayer.showTip('请选择规格图片！', 3000, 'error');
                    flag = false;
                    return false;
                }
                if(image_path){
                    sku['image_path'] = image_path;
                }
                skus.push(sku);
            });
            if(!flag){
                return false;
            }
            var layer = mylayer.showLoad(true);
            formData.append('skus', JSON.stringify(skus));
            $.ajax({
                url: '/admin/api/product/add',
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(result){
                    if(result.code == "200"){
                        window.location.href = '/admin/product?is_self=1';
                    } else {
                        mylayer.hideLoad();
                        alert(result.message);
                    }
                },
                error: function(result){   
                    mylayer.hideLoad();
                    alert('系统错误！');
                }
            });
        });
        $(".save-product-form").on("submit", function(){
            return false;
        });
        //添加图片
        $(document).on("click", ".js-add-product-image", function(){
            var type = $(this).attr('data-type');
            if(type == 'description'){
                var image_size = $(".js-product-image-list .product-image-item").size();
                if(image_size >= 10){
                    mylayer.showTip('对不起,图片最多只能上传10张！');
                    return false;
                }
            }
            $(".product-image-upload-file").click();
        });
        //添加描述图片
        $(".js-add-description-image").on("click", function(){
            var type = $(this).attr('data-type');
            if(type == 'description'){
                var image_size = $(".js-description-image-list .product-image-item").size();
                if(image_size >= 10){
                    mylayer.showTip('对不起,图片最多只能上传10张！');
                    return false;
                }
            }
            $(".description-image-upload-file").click();
        });
        //选择图片后上传预览
        $(".product-image-upload-form").on("change", function(event){
            var form = $('.product-image-upload-form');
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            if(!elem.hasClass('product-image-upload-file')){
                return true;
            }
            try{
                var files = elem[0].files;
                if(files && files.length > 0){
                    //Verify that the file type
                    if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg']) == -1){
                        mylayer.showTip(tipMessage.upload_image_format_tip, 5000, "error");
                        return false;
                    }
                    if(files[0].size){
                        var sm = files[0].size / (1024 * 1024);
                        if(sm > 5){
                            mylayer.showTip(tipMessage.upload_maximum_tip, 5000, "error");
                            return false;
                        }
                    }
                }
                var file = files[0];
                var reader = new FileReader();
                // 将文件以Data URL形式进行读入页面
                reader.readAsDataURL(file);
                reader.onload = function(f){
                    var src = this.result;
                    var index = $(".js-product-image-list .product-image-item").size();
                    var current_file = elem.clone();
                    var file_id = self.random() + index;
                    current_file.attr('id', "product_image_file_" + file_id).removeClass('product-image-upload-file');
                    $(".main-image-file").append(current_file);
                    var item = $("#product-image-template");
                    var data = [{
                        'image': src,
                        'file_id': file_id
                    }];
                    var box = $.tmeplate(item, data);
                    $(".js-add-product-image").before(box);
                    var sku_image = src;
                    current_sku_image.find('img').attr('src', sku_image);
                    var layer = $(".layer-product-image-list");
                    mylayer.hideLayer(layer);
                    current_sku_image.attr('data-image-file-id', file_id);
                }
                var file_elem = form.find('input[type=file]');
                file_elem.after(file_elem.clone().val(""));   
                file_elem.remove();
            }
            catch(e){}
        });
        //选择图片后上传预览
        $(".description-image-upload-form").on("change", function(event){
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            if(!elem.hasClass('description-image-upload-file')){
                return true;
            }
            try{
                var files = elem[0].files;
                if(files && files.length > 0){
                    //Verify that the file type
                    if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg']) == -1){
                        mylayer.showTip(tipMessage.upload_image_format_tip, 5000, "error");
                        return false;
                    }
                    if(files[0].size){
                        var sm = files[0].size / (1024 * 1024);
                        if(sm > 5){
                            mylayer.showTip(tipMessage.upload_maximum_tip, 5000, "error");
                            return false;
                        }
                    }
                }
                var file = files[0];
                var reader = new FileReader();
                // 将文件以Data URL形式进行读入页面
                reader.readAsDataURL(file);
                reader.onload = function(f){
                    var src = this.result;
                    var index = $(".js-description-image-list .product-image-item").size();
                    var current_file = elem.clone();
                    var file_id = self.random() + index;
                    current_file.attr('id', "product_image_file_" + file_id).removeClass('description-image-upload-file');
                    $(".product-description-image-file").append(current_file);
                    var item = $("#product-image-template");
                    var data = [{
                        'image': src,
                        'file_id': file_id
                    }];
                    var box = $.tmeplate(item, data);
                    $(".js-add-description-image").before(box);
                }
                var file_elem = form.find('input[type=file]');
                file_elem.after(file_elem.clone().val(""));   
                file_elem.remove();
            }
            catch(e){}
        });
        //删除图片
        $(document).on("click", ".js-remove-product-image", function(){
            var product_image_item = $(this).closest('.product-image-item');
            if(product_image_item.size() > 0){
                var file_id = product_image_item.attr('data-file-id');
                $("#product_image_file_" + file_id).remove();
                product_image_item.remove();
            }
        });
        //添加属性
        $(".add_attributes").on("click", function(){
            var l = $("#product-attributes-template").html();
            $(".product_attributes_box").append(l);
        });
        //添加sku
        $(".add_sku").on("click", function(){
            var sku_item_tr = $(".sku-list-item").first().clone();
            if(sku_item_tr.size() == 0){
                var sku_item_template = $("#sku-item-template").html();
                $(".sku-list-box").append(sku_item_template);
            } else {
                $(".sku-list-box").append(sku_item_tr);
            }
        });
        $(document).on("click", '.js-remove-sku-item', function(){
            if($(".sku-list-item").size() == 1){
                mylayer.showTip('至少一个规格属性！');
                return false;
            }
            var sku_list_item = $(this).closest('.sku-list-item');
            sku_list_item.remove();
        });
        //选择属性
        $(".attributes_checked").on("click", function(){
            var attribute = $(this).attr('data-value');
            if(!$(this).is(":checked")){
                if($(".attributes_checked:checked").size() == 0){
                    return false;
                }
                if(attribute == 'color'){
                    $(".sku_color_td").hide();
                }
                if(attribute == 'size'){
                    $(".sku_size_td").hide();
                }
            } else {
                if(attribute == 'color'){
                    $(".sku_color_td").show();
                }
                if(attribute == 'size'){
                    $(".sku_size_td").show();
                }
            }
        });
        //属性值
        $(document).on("keydown", ".attributes_value_input", function(){
            var e = event || window.event;    
            var code = e.keyCode || e.which || e.charCode;  
            var elem = $(this);
            if(code == 13){
                var value = elem.val();
                var attributes_value_item = '<li style="border: 1px solid #eeeeee;padding: 10px;display:inline-block"><span>' + value + '</span></li>';
                var attributes_item = elem.closest('.product-attributes-item');
                attributes_item.find(".attributes_value_list").append(attributes_value_item);
                elem.val('');
                return true;
            }
            return true;
        });
        //选择sku图片
        $(document).on("click", ".sku_image", function(){
            current_sku_image = $(this);
            var content = $("#product-image-list-template").html();
            var product_image_item_list = $(".js-product-image-list").html();
            mylayer.init({
                content: content,
                close: false,
                class_name: "layer-product-image-list",
                position: 'center',
                success: function(){
                    
                }
            });
            $(".product-image-list-box").find('.product-image-list').html(product_image_item_list);
            $(".product-image-list-box").find('.product-image-list').html(product_image_item_list);
        });
        //选择sku图片
        $(document).on("click", '.product-image-list-box .product-image-item', function(){
            var sku_image = $(this).find('img').attr('src');
            current_sku_image.find('img').attr('src', sku_image);
            var file_id = $(this).attr('data-file-id');
            var layer = $(".layer-product-image-list");
            mylayer.hideLayer(layer);
            current_sku_image.attr('data-image-file-id', file_id);
        });
    },
     //生成当前时间戳+随机数
    random: function(){
        //当前时间戳
        var timestamp = parseInt(new Date().getTime()/1000);
        var r = Math.floor(Math.random()*100000); 
        return timestamp + "" + r;  
    },
    //地址验证规则
    productFormRule: [
        {
            name: 'name',
            rules: 'required|max_length[255]',
            message: {
                required: '请输入产品名称！'
            }
        }, {
            name: 'market_price',
            rules: 'required|decimal',
            message: {
                required: '请输入市场价！',
                decimal: '请输入数字'
            }
        }, {
            name: 'price',
            rules: 'required|decimal',
            message: {
                required: '请输入手机号码！',
                decimal: '请输入数字'
            }
        }
    ],
    //验证提示
    showValidatorError: function(errors, form){
        for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
            var elem = $(errors[i].element);
            if(elem.next('.errormsg').size() == 0){
                elem.after('<div class="errormsg"></div>');
            }
            elem.next('.errormsg').html(errors[i].message);
        }
        var top = form.offset().top;
        window.scrollTo(0, top);
    },
    //上传后回调
    uploadCallback: function(form){
        var file_elem = form.find('input[type=file]');
        file_elem.after(file_elem.clone().val(""));   
        file_elem.remove();
    }
};
var tipMessage = {
    upload_image_format_tip: '请选择png、jpg、jpeg格式图片！',
    upload_maximum_tip: '图片文件不能超过5M'
}
if(typeof app.init == 'function') {
    $(function () {
        app.init();
    });
}