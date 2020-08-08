 require(['zepto', 'base', 'mylayer', 'loginReg', 'share'], function ($, md_base, mylayer, md_loginReg, md_share){
    var app = {};
    //公共事件
    app.init = function(){
        var self = this;
        //产品组件
        productComponent.initEvent();
        //评论组件
        reviewComponent.initEvent();
    };
    //产品购买组件
    var productComponent = {
        initEvent: function(){
            var self = this;
            //显示购买框
            $(".js-btn-buy").on("click", function(){
                var elem = $(this);
                if(elem.hasClass('disabled')){
                    return false;
                }
                if(goods_sku_list.length == '1'){
                    var sku = goods_sku_list[0];
                    self.checkoutLink(sku);
                    return;
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
                self.checkoutLink(sku);
            });
        },
        checkoutLink(sku){
            if(!sku || sku == null){
                mylayer.showMessage(tipMessage.sku_offline);
                return false;
            }
            if(sku['stock'] == 0){
                mylayer.showMessage('此规格产品库存不足！');
                return false;
            }
            var goods_sku_id = sku['id'];
            let checkoutUrl = $("#checkoutUrl").val();
            var checkout_link = checkoutUrl  + "&product_sku_id=" + goods_sku_id;
            window.location.href = checkout_link;
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
    //评论组件
    var reviewComponent = {
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
        'select_attributes_tip': '请选择属性!'
    }
    if(typeof app.init == 'function') {
        $(function () {
            app.init();
        });
    }
});
