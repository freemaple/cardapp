//基础加载
require(['zepto', 'base', 'mylayer', 'validate', 'scrollComponent'], function ($, md_base, mylayer, validate, scrollComponent) {
    var app = {
        init: function(){
            var self = this;
            var order_no = $("#order_no").val();
            if(order_no){
                this.checkOrderPay(order_no);
            }
            $(".js-vip-pay").on("click", function(){
                var amount_value = $(".amount_value").val();
                if(amount_value <=0){
                   mylayer.showTip('金额必须大于0！', 3000, 'error');
                   return false;
                }
                if(amount_value <=0 || amount_value == ''){
                   mylayer.showTip('请输入金额！', 3000, 'error');
                   return false;
                }
                var org = /^[0-9]*[1-9][0-9]*$/;
                if(!org.test(amount_value)){
                   mylayer.showTip('请输入金额！', 3000, 'error');
                   return false;
                }
                var layer = mylayer.showLoad(true, true);
                $.ajaxPost('/api/order/integral', {'amount': amount_value}, function(result){
                    if(result.code == 'Success'){
                        var order_no = result.data.order_no;
                        if(order_no){
                            self.pushState(order_no);
                        }
                        if(result.data.is_weixin){
                            self.wxPay(result.data);
                        } else if(result.data.mweb_url){
                            window.location.href = result.data.mweb_url;
                        }
                    }
                });
            })
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
            $.ajaxPost('/api/order/checkIntegralOrderPay', {'order_no': order_no}, function(result){
                if(result.code == 'Success'){
                    window.location.href = '/checkout/integral/success/' + order_no;
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
