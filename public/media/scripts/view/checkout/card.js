require(["zepto","base","mylayer","validate","scrollComponent"],function(a,e,n,t,o){function r(a){var e=i();return e[a]}function i(){for(var a=(location.hash||"").replace(/^\#/,"").split("&"),e={},n=0;n<a.length;n++){var t=a[n].split("=");2==t.length&&(e[t[0]]=t[1])}return e}var c={init:function(){var e=this,t=a("#order_no").val();t||(t=r("order_no")),t&&e.checkOrderPay(t),a(".js-pay-card-order").on("click",function(){n.showLoad(!0,!0);a.ajaxPost("/api/order/cardRenewalOrder",{},function(a){if("Success"==a.code){var t=a.data.order_no;e.checkPay(t),t&&e.pushState(t),n.showLoad(!0),a.data.is_weixin?e.wxPay(a):a.data.mweb_url&&(window.location.href=a.data.mweb_url)}})})},pushState:function(e){if(location.hash="order_no="+e,!is_weixin&&history&&history.pushState){var n=window.location.search;"undefined"!=typeof e&&e&&(n=a.changeURLArg(n,"order_no",e));var t=window.location.pathname+n+window.location.hash;history.pushState({title:document.title,url:t},document.title,t)}},checkPay:function(a){var e=this,n=1;e.payInterval=setInterval(function(){n++,n>100?window.clearInterval(e.payInterval):e.checkOrderPay(a)},3e3)},wxPay1:function(a){var e=this,n=a.data;WeixinJSBridge.invoke("getBrandWCPayRequest",{appId:n.appId,timeStamp:n.timestamp,nonceStr:n.nonceStr,package:n.package,signType:n.signType,paySign:n.paySign},function(n){var t=a.data.order_no;e.checkOrderPay(t),window.location.reload()})},wxPay:function(a){var e=this,n=a.data,t=a.data.order_no;wx.config({appId:n.appId,timestamp:n.timestamp,nonceStr:n.nonceStr,signature:n.paySign,jsApiList:["chooseWXPay"]});var o={appId:n.appId,timestamp:n.timestamp,nonceStr:n.nonceStr,package:n.package,signType:n.signType,paySign:n.paySign,success:function(){e.checkOrderPay(t)},cancel:function(a){window.location.reload()}};wx.ready(function(){wx.chooseWXPay(o)})},checkOrderPay:function(e){var n=this;a.ajaxPost("/api/order/checkCardRenewalOrderPay",{order_no:e},function(a){"Success"==a.code&&("undefined"!=typeof n.payInterval&&window.clearInterval(n.payInterval),window.location.href="/checkout/card/renewal/success/"+e)})}};"function"==typeof c.init&&a(function(){c.init()})});