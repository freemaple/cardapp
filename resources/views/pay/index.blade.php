@extends('layouts.app')

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-title">支付</div>
    </div>
</div>
@endsection

@section('content')
<div class="checkout-box">
    <div class="checkout-box">
        <div class="checkout-panel">
            <div class="checkout-panel-header">支付方式</div>
            <div class="checkout-panel-content">
                <div class="payment-list">
                    <div class="payment-item selected" data-code="weixin">
                        <span class="weixin-logo">
                            <span class="iconfont icon-weixin-zf"></span>
                            <span>微信支付</span>
                        </span>
                        <span class="checkbox">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    <div class="checkot-footer">
        <ul class="clearfix">
            <li class="order-amount">
                <div class="box">
                    <span>总共:</span>
                    <span class="total-amount-info">
                        ￥1
                    </span>
                </div>
            </li>
            <li class="pay-btn-block checkout-pay-submit">
                <div class="box">
                    <a href="javascript:void(0)" class="js-vip-pay">
                       支付
                    </a>
                </div>
            </li>
        </ul>
    </div>
@endsection

@section('scripts')
<script>
    //基础加载
    require(['zepto', 'base', 'mylayer'], function ($, md_base, mylayer) {
        var app = {
            init: function(){
                var self = this;
                $(".js-vip-pay").on("click", function(){
                    var layer = mylayer.showLoad(true, true);
                    $.ajaxPost('/wx/pay_order', {}, function(result){
                        if(result.code == 'Success'){
                            if(result.data.is_weixin){
                                self.wxPay(result.data);
                            } else if(result.data.mweb_url){
                                window.location.href = result.data.mweb_url;
                            }
                        }
                    });
                })
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
                    if(res.err_msg == "get_brand_wcpay_request:ok" ){
                        alert("支付成功！");
                    } else {
                        alert(res.err_msg);
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

</script>
@endsection

