 require(['zepto', 'base', 'mylayer', 'loginReg', 'share'], function ($, md_base, mylayer, md_loginReg, md_share){
    var app = {};
    //公共事件
    app.init = function(){
        var self = this;
        var goods_id = $("#goods_id").val();
        //产品组件
        productComponent.initEvent();
        //评论组件
        if(goods_id){
            reviewComponent.loadBox(goods_id);
        }
        reviewComponent.initEvent();
        //浏览记录
        this.addRecently();
        //倒计时功能
        var interval = setInterval(function(){
            $(".expires-time").each(function(){
                var elem = $(this);
                var ts = $(this).attr('data-remaining-time');
                if(ts > 0){
                    timerComponent.timer(this, ts);
                    elem.show();
                }  else {
                    elem.hide();
                }
            });
        }, 1000);
        $(document).on("click", '.js-wish-product', function(){
            var elem = $(this);
            var product_id = $(this).attr('data-id');
            var is_wish = $(this).attr('data-is-wish');
            is_wish = is_wish == '1' ? '0' : 1;
            $.ajaxPost("/api/product/wish", {'product_id': product_id, 'is_wish': is_wish}, function(result){
                if(result.code == 'UNAUTH'){
                    window.location.href = '/auth/login';
                } 
                if(result.code == 'Success'){
                    if(is_wish == '1'){
                        elem.attr('data-is-wish', '1');
                        elem.addClass('is-wish');
                        mylayer.showTip(result.message, 3000, 'success');
                    }
                    if(is_wish == '0'){
                        elem.removeAttr('data-is-wish');
                        elem.removeClass('is-wish');
                        mylayer.showTip(result.message, 3000, 'error');
                    }
                    
                } else if (result.message != ''){
                    mylayer.showTip(result.message, 3000, 'error');
                }
            });
        });
        $(document).on("click", '.js-product-codeimage', function(){
            var layer = $(".layer-product-share-box");
            if(layer.size() > 0){
                 mylayer.hideLayer(layer);
            }
            var elem = $(this);
            var product_id = $(this).attr('data-id');
            if(self.codeImage){
               self.codeImageCallback(self.codeImage);
            } else {
                mylayer.showLoad();
                $.ajaxPost("/api/product/codeImage", {'product_id': product_id}, function(result){
                    mylayer.hideLoad();
                    if(result.code == 'Success'){
                        self.codeImage = result.data.image;
                        self.codeImageCallback(result.data.image);
                    } else if (result.message != ''){
                        mylayer.showTip(result.message, 3000, 'error');
                    }
                });
            }
        });
        $(document).on("click", ".js-share-product", function(event){
            /*if(is_weixin){
                require(['mylayer'], function(mylayer){
                    mylayer.showTip("点击右上角分享 . . . 通过发送给朋友！", 4000, 'error');
                    return false;
                });
            }*/
            if(!window.isAPP){
                self.shareCallback();
                return false;
            }
            var elem = $(this);
            var data = share_data;
            let u_type = elem.attr('data-u');
            require(['share'], function(md_share){
                md_share.init(share_data, function(res){
                    if(res){
                        if(u_type == 'vip'){
                            $.ajaxPost('/api/share/taskIntegral', {'goods_id': goods_id}, function(){
                            });
                        }
                    }
                });
            });
        });
        this.getStoreProduct();
    };
    app.shareCallback = function(){
        var self = this;
        var content = $("#product-share-box-template").html();
        mylayer.init({
            content: content,
            close: true,
            class_name: "layer-product-share-box layer-bottom bottom-to-top",
            position: 'bottom',
            callback: function(){
                
            }
        });
    };
    app.codeImageCallback = function(image){
        var self = this;
        var content = $("#code-image-template").html();
        mylayer.init({
            content: content,
            close: true,
            class_name: "layer-codeimage-box",
            position: 'top',
            callback: function(){
                
            }
        });
        $(".codeImage").attr('src', image);
        $.saveFile(image, self.random() + '.jpg', function(){
            mylayer.showTip('扫码购图片已保存到手机相册', 3000, 'success');
        });
    };
    //生成当前时间戳+随机数
    app.random = function(){
        //当前时间戳
        var timestamp = parseInt(new Date().getTime()/1000);
        var r = Math.floor(Math.random()*100000); 
        return timestamp + "" + r;  
    };
    //浏览记录
    app.addRecently = function(){
        var goods_id = $("#goods_id").val();
        if(goods_id){
            $.ajaxPost('/product/viewed', {'id': goods_id}, function(){});
        }
        if(window.localStorage){
            var ids = window.localStorage.getItem('viewd_goods_id');
            if(!ids){
                ids = [];
            } else {
                ids = JSON.parse(ids);
            }
            
            var index = $.inArray(goods_id, ids);
            if(index == -1){
                
            } else {
                ids.splice(index, 1);
            }
            ids.unshift(goods_id);
            ids = ids.slice(0, 50);
            window.localStorage.setItem('viewd_goods_id', JSON.stringify(ids));
        }
    };
    app.getStoreProduct = function(){
        var goods_id = $("#goods_id").val();
        $.ajaxPost('/api/product/getStoreProduct', {'product_id': goods_id}, function(result){
            if(result.code == 'Success'){
                if(result.view){
                    $(".js-store-product-list").html(result.view);
                    $(".store-product-box").show();
                    $.imgLazyLoad();
                }
            }
        });
        $.ajaxGet('/api/shop', {'product_id': goods_id}, function(result){
            if(result.code == 'Success'){
                if(result.view){
                    $(".js-self-product-list").html(result.view);
                    $(".self-store-product-box").show();
                    $.imgLazyLoad();
                }
            }
        });
    };
    //产品购买组件
    var productComponent = {
        initEvent: function(){
            var self = this;
            //返回
            $(".js-goods-back").on("click", function(){
                var referrer = document.referrer;
                var hostname = window.location.hostname;
                if(referrer == '' || referrer == window.location.href || referrer.indexOf(hostname) == -1){
                    referrer = "/";
                }
                if(referrer && referrer.indexOf('/checkout') != -1){
                    referrer = "/";
                }
                window.location.href = referrer;
            });
            //显示购买框
            $(".js-btn-buy").on("click", function(){
                var elem = $(this);
                if(elem.hasClass('disabled')){
                    return false;
                }
                self.showBuyBox(function(){
                });
            });
            //属性选择
            $(document).on('click', '.attributes-value-item', function(){
                var elem = $(this);
                var goods_buy_form = $(this).closest('.goods_buy_form');
                if(elem.hasClass('disabled')){
                    elem.removeClass('disabled');
                    goods_buy_form.find(".attributes-value-item").removeClass('select');
                }
                elem.addClass('select').siblings('li').removeClass('select');
                var attribute_ids = [];
                var attribute_values = [];
                var attributes_size = goods_buy_form.find(".attributes-item").length;
                goods_buy_form.find(".attributes-item").each(function(){
                    var attributes_select = $(this).find('.attributes-value-item.select').first();
                    if(attributes_select.size() == 0){
                        return true;
                    }
                    attribute_ids.push(attributes_select.attr('data-id'));
                    attribute_values.push(attributes_select.attr('data-value'));
                });
                if(attributes_size == attribute_values.length){
                    var sku = self.findSku(attribute_values);
                    if(sku && sku != null){
                        var gallery = sku.image;
                        var price = sku.price;
                        var market_price = sku.market_price;
                        goods_buy_form.find('.sku-image').attr('src', gallery);
                        goods_buy_form.find('.sku-price-value').html(price);
                        if(market_price > 0){
                            goods_buy_form.find('.market_price').show();
                        } else {
                            goods_buy_form.find('.market_price').hide();
                        }
                        var stock = sku.stock;
                        goods_buy_form.find('.sku-stock').show();
                        goods_buy_form.find('.sku-stock-value').html(stock);
                    }
                }
                self.filterAttribute(elem);
            });
            //qty增减
            $(document).on('click', '.qty-reduce', function(){
                var qty_input_box = $(this).parent().find('.qty-input');
                var qty_value = qty_input_box.val();
                var type = $(this).attr('data-type');
                if(type == 'decrease'){
                    var qty_value = parseInt(qty_value);
                    if(qty_value > 1){
                        qty_value = qty_value - 1;
                        qty_input_box.val(qty_value);
                    }
                } else if(type == 'increase') {
                    var qty_value = parseInt(qty_value) ? parseInt(qty_value) : 0;
                    qty_value = qty_value + 1;
                    var sku = self.getSku();
                    if(sku){
                        var sku_stock = parseInt(sku['stock']);
                        if(qty_value > sku_stock){
                            mylayer.showTip('库存不足！', 3000, 'error');
                            qty_value = sku_stock;
                        }
                    }
                    qty_input_box.val(qty_value);
                }
            });
            //qty增减
            $(document).on('change', '.qty-input', function(){
                var qty_input_box = $(this);
                var qty_value = qty_input_box.val();
                var sku = self.getSku();
                if(sku){
                    var sku_stock = parseInt(sku['stock']);
                    if(qty_value > sku_stock){
                        mylayer.showTip('库存不足！', 3000, 'error')
                        qty_value = sku_stock;
                        qty_input_box.val(qty_value);
                    }
                }
            });
            //购买确认
            $(document).on('click', '.js-buy-confirm', function(){
                var goods_buy_form = $(this).closest('.goods_buy_form');
                var attributes_item = goods_buy_form.find('.attributes-item');
                var sku = null;
                if(goods_sku_list.length == '1'){
                    sku = goods_sku_list[0];
                } else {
                    if(attributes_item.size() > 0){
                        var attribute_ids = [];
                        var attribute_values = [];
                        var attributes_size = attributes_item.size();
                        attributes_item.each(function(){
                            var attributes_select = $(this).find('.attributes-value-item.select').first();
                            if(attributes_select.size() == 0){
                                return true;
                            }
                            attribute_ids.push(attributes_select.attr('data-id'));
                            attribute_values.push(attributes_select.attr('data-value'));
                        });
                        if(attributes_size != attribute_ids.length){
                            mylayer.showMessage(tipMessage.select_attributes_tip);
                            return false;
                        }
                        sku = self.findSku(attribute_values);
                    } else {
                        sku = goods_sku_list.length > 0 ? goods_sku_list[0] : null;
                    }
                }
                if(!sku || sku == null){
                    mylayer.showMessage(tipMessage.sku_offline);
                    return false;
                }
                var qty = goods_buy_form.find('.qty-input').val();
                var org = /^[0-9]*[1-9][0-9]*$/;
                if(!org.test(qty)){
                    qty = 1;
                }
                if(sku['stock'] == 0){
                    mylayer.showMessage('此规格产品库存不足！');
                    return false;
                }
                if(qty > sku['stock']){
                    mylayer.showMessage('此规格产品库存只剩：' + sku['stock'] +'！');
                    return false;
                }
                var goods_id = goods_buy_form.find('[name=goods_id]').val();
                var goods_sku_id = sku['id'];
                var data = {'goods_id': goods_id, 'goods_sku_id': goods_sku_id, 'qty': qty};
                var sid = $("#sid").val();
                if(sid){
                    data['sid'] = sid;
                }
                var basket_code = self.random() + sku['id'];
                data['basket_code'] = basket_code;
                //data['use_integral'] = '1';
                var qdata = [];
                $.each(data, function(key, value){
                    qdata.push(key + "=" + value);
                });
                var query = qdata.join('&');
                var checkout_link = '/checkout/pay/?' + query;
                window.location.href = checkout_link;
            });
        },
        getSku: function(){
            var self = this;
            var goods_buy_form = $('.goods_buy_form');
            var attributes_item = goods_buy_form.find('.attributes-item');
            var sku = null;
            if(goods_sku_list.length == '1'){
                sku = goods_sku_list[0];
            } else {
                if(attributes_item.size() > 0){
                    var attribute_ids = [];
                    var attribute_values = [];
                    var attributes_size = attributes_item.size();
                    attributes_item.each(function(){
                        var attributes_select = $(this).find('.attributes-value-item.select').first();
                        if(attributes_select.size() == 0){
                            return true;
                        }
                        attribute_ids.push(attributes_select.attr('data-id'));
                        attribute_values.push(attributes_select.attr('data-value'));
                    });
                    if(attributes_size != attribute_ids.length){
                        return false;
                    }
                    sku = self.findSku(attribute_values);
                } else {
                    sku = goods_sku_list.length > 0 ? goods_sku_list[0] : null;
                }
            }
            if(!sku || sku == null){
                return false;
            }
            return sku;
        },
        //生成当前时间戳+随机数
        random: function(){
            //当前时间戳
            var timestamp = parseInt(new Date().getTime()/1000);
            var r = Math.floor(Math.random()*100000); 
            return timestamp + "" + r;  
        },
        filterAttribute: function(attributes_value_item){
            var self = this;
            var attributes_item = attributes_value_item.closest('.attributes-item');
            //var c_id = attributes_value_item.attr('data-id');
            var c_value = attributes_value_item.attr('data-value');
            attributes_item.siblings('.attributes-item').each(function(){
                var attr_item = $(this);
                attr_item.find('.attributes-value-item').each(function(){
                    var attr_item_value = $(this);
                    //var attribute_ids = [];
                    //attribute_ids.push(c_id);
                    var attribute_values = [];
                    attribute_values.push(c_value);
                    //var attr_value_id = attr_item_value.attr('data-id');
                    var attr_value = attr_item_value.attr('data-value');
                    //attribute_ids.push(attr_value_id);
                    attribute_values.push(attr_value);
                    var sku = self.findSku(attribute_values);
                    if(sku){
                        attr_item_value.removeClass('disabled');
                    } else {
                        attr_item_value.addClass('disabled');
                    }
                });
            });
        },
        //展示购买框
        showBuyBox: function(callback){
            var content = $("#buy-form-template").html();
            if($(".layer-buy-box").size() > 0){
                var layer = $(".layer-buy-box");
                mylayer.showLayer(layer);
                callback();
            } else {
                mylayer.init({
                    content: content,
                    close: true,
                    class_name: "layer-buy-box layer-bottom bottom-to-top no-remove",
                    position: 'bottom',
                    callback: function(){
                        callback();
                    }
                });
            }
        },
        //根据属性查找sku
        findSku: function(attribute_values){
            var sku = null;
            $.each(goods_sku_list, function(key, sku_item){
                var count = 0;
                $.each(attribute_values, function(akey, attribute_value){
                    $.each(sku_item['attributes'], function(akey, sku_attribute){
                        if(sku_attribute['option_value'] == attribute_value){
                            count ++;
                        }
                    });
                })
                if(count == attribute_values.length){
                    sku = sku_item;
                    return false;
                }
            });
            return sku;
        }
    };
    //倒计时
    var timerComponent = {
        timer: function(obj, ts){
            var hh = parseInt(ts / 60 / 60);//计算剩余的小时数  
            var mm = parseInt(ts / 60 % 60);//计算剩余的分钟数  
            var ss = parseInt(ts % 60);//计算剩余的秒数 
            hh = this.checkTime(hh);  
            mm = this.checkTime(mm);  
            ss = this.checkTime(ss);  
            $(obj).find(".hour").text(hh);
            $(obj).find(".minute").text(mm);
            $(obj).find(".second").text(ss);
            $(obj).find(".second").text(ss);
            $(obj).attr("data-remaining-time", ts - 1 >=0 ? ts -1 : 0);
        },
        checkTime: function(i){    
           if (i < 10) {    
               i = "0" + i;    
            }    
           return i;    
        }
    };
    //评论组件
    var reviewComponent = {
        loadBox(product_id){
            $.ajaxGet('/api/product/reviewsBox', {'product_id': product_id}, function(result){
                if(result.code == "Success" && result.view){
                   $(".product-reviews-box").html(result.view);
                } 
            });
        },
        initEvent: function(){
            var self = this;
            //加载评论记录
            $(".js-load-reviews").on("click", function(){
                mylayer.showLoad();
                var goods_id = $(this).attr('data-id');
                var current_page = '1';
                self.loadReview(goods_id, {'page': current_page}, function(result){
                    mylayer.hideLoad();
                    if(result.code == 'SUCCESS'){
                        if(result.data.data){
                            var content = $("#goods-reviews-template").html();
                            mylayer.init({
                                content: content,
                                close: false,
                                position: 'top',
                                class_name: 'layer-top layer-review',
                                callback: function(){
                                    self.layerEvent();
                                    var list = result.data.data;
                                    var template = $("#goods-review-template").html();
                                    var html = $.tpl(template, list);
                                    $(".goods-review-list").html(html); 
                                    if(result.data.last_page && result.data.last_page == current_page){
                                        var scrollitem = $(".layer-review").find(".js-review-scroll-container");
                                        scrollitem.attr('data-load-more' , '-1');
                                    }
                                }
                            });
                        } else if(result.message != ''){
                            mylayer.showTip(result.message,  3000, 'error');
                        }
                    }
                });
            });
        },
        layerEvent: function(){
            $(".layer-review").on("scroll", function(){
                var layer = $(this);
                var scrollitem = layer.find(".js-review-scroll-container");
                var nScrollHight = layer[0].scrollHeight;
                var nScrollTop = layer[0].scrollTop;
                var nDivHight = layer.height();
                var is_scroll_bottom = false;
                if(nScrollTop + nDivHight >= nScrollHight){
                   is_scroll_bottom = true;
                }
                if(!is_scroll_bottom){
                    return false;
                }
                var is_load = scrollitem.hasClass('loading') ? true : false;
                if(is_load){
                    return false;
                }
                var is_load_more = scrollitem.attr('data-load-more');
                if(is_load_more == '-1'){
                    return false;
                }
                var load_block = scrollitem.find(".js-load-block");
                load_block.show();
                scrollitem.addClass('loading');
                var action = scrollitem.attr('data-action');
                var page = parseInt(scrollitem.attr('data-page')) ? parseInt(scrollitem.attr('data-page')) : 0;
                var current_page = page + 1;
                $.ajaxGet(action, {'page': current_page}, function(result){
                    if(result.code == "SUCCESS" && result.data){
                        if(result.data.data && result.data.data.length > 0){
                            scrollitem.attr('data-page', current_page);
                            var list = result.data.data;
                            var template = $("#goods-review-template").html();
                            var html = $.tpl(template, list);
                            layer.find(".goods-review-list").append(html); 
                        } else {
                            scrollitem.attr('data-load-more' , '-1');
                        }
                        if(result.data.last_page && result.data.last_page == current_page){
                            scrollitem.attr('data-load-more' , '-1');
                        }
                    }
                    scrollitem.removeClass('loading');
                    load_block.hide();
                });
            });
        },
        //加载拼单记录
        loadReview: function(goods_id, data, callback){
            $.ajaxGet('/api/goods/reviews/' + goods_id, data, function(result){
                callback(result);
            });
        }
    };
    var tipMessage = {
        'sku_offline': '产品已下架!',
        'select_attributes_tip': '请选择属性!',
        'wishlist_remove_tip': '确认取消收藏？'
    }
    if(typeof app.init == 'function') {
        $(function () {
            app.init();
        });
    }
});


