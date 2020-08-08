//基础加载
require(['zepto', 'base', 'mylayer', 'validate', 'scrollComponent'], function ($, md_base, mylayer, validate, scrollComponent) {
    var app = {
        init: function(){
            var self = this;
            var order_no = $("#order_no").val();
            alert($order_no);
            if(order_no){
                this.checkOrderPay(order_no);
            }
            $(".js-pay-card-order").on("click", function(){
                var layer = mylayer.showLoad(true, true);
                $.ajaxPost('/api/order/cardRenewalOrder', {}, function(result){
                    if(result.code == 'Success'){
                        var order_no = result.data.order_no;
                        if(order_no){
                            self.pushState(order_no);
                        }
                        mylayer.showLoad(true);
                        if(result.data.is_weixin){
                            self.wxPay(result.data);
                            self.payInterval = window.setInterval(function(){
                                self.checkOrderPay(order_no);
                            }, 3000);
                        } else if(result.data.mweb_url){
                            self.payInterval = window.setInterval(function(){
                                self.checkOrderPay(order_no);
                            }, 3000);
                            window.location.href = result.data.mweb_url;
                        }
                    }
                });
            });

        },
        //改变pushState
        pushState: function(order_no){
            if(history && history.pushState){
                var search_url = window.location.search;
                if(typeof order_no != "undefined" && order_no){
                    search_url = $.changeURLArg(search_url, 'order_no', order_no);
                }
                var url = window.location.pathname + search_url + window.location.hash;
                history.pushState({ title: document.title, url: url }, document.title, url);
            }
        },
        wxPay: function(result){
            var self = this;
            var config = result.data;
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest', {
                   "appId": config['appId'],     //公众号名称，由商户传入     
                   "timeStamp": config['timestamp'],         //时间戳，自1970年以来的秒数     
                   "nonceStr": config['nonceStr'], //随机串     
                   "package":  config['package'],     
                   "signType": config['signType'],         //微信签名方式：     
                   "paySign": config['paySign'] //微信签名 
                },
                function(res){
                    var order_no = result.data.order_no;
                    self.checkOrderPay(order_no);
                    if(res.err_msg == "get_brand_wcpay_request:ok"){
                        
                    } else {
                        window.location.reload();
                    }
                }
            ); 
        },
        checkOrderPay: function(order_no){
            $.ajaxPost('/api/order/checkCardRenewalOrderPay', {'order_no': order_no}, function(result){
                if(result.code == 'Success'){
                    if(typeof self.payInterval != 'undefined'){
                        window.clearInterval(self.payInterval);
                    }
                    window.location.href = '/checkout/card/success/' + order_no;
                }
            });
        }
    }
    if(typeof app.init == 'function') {
        $(function () {
            app.init();
        });
    }
}); 
