!function(){function t(t,n,e){var c=i[t];if(!c)return void(e&&e(!1));var o=function(){c.send(n,function(){plus.nativeUI.toast('分享到"'+c.description+'"成功！'),e&&e(!0)},function(t){plus.nativeUI.toast('分享到"'+c.description+'"失败！'),e&&e(!1)})};c.authenticated?o(c,n,e):c.authorize(function(){o(c,n,e)},function(t){console.log("认证授权失败"),e&&e(!1)})}function n(t,n){plus.share.sendWithSystem?plus.share.sendWithSystem(t,function(){},function(){}):n&&n(!1)}var e=function(t){window.plus?t():document.addEventListener("plusready",t)},i={},c=function(){plus.share.getServices(function(t){for(var n=0,e=t.length;n<e;n++)i[t[n].id]=t[n]})},o=function(){return plus.runtime.isApplicationExist&&plus.runtime.isApplicationExist({pname:"com.tencent.mm",action:"weixin://"})},s=function(e,c){if(i.weixin&&o()&&!/360\sAphone/.test(navigator.userAgent))plus.nativeUI.actionSheet({title:"分享到",cancel:"取消",buttons:[{title:"微信消息"},{title:"微信朋友圈"},{title:"更多分享"}]},function(i){var o=i.index;switch(o){case 1:e.extra={scene:"WXSceneSession"},t("weixin",e,c);break;case 2:e.title=e.content,e.extra={scene:"WXSceneTimeline"},t("weixin",e,c);break;case 3:var s=e.href?"( "+e.href+" )":"";e.title=e.title+s,e.content=e.content+s,n(e,c)}});else{var s=e.href?"( "+e.href+" )":"";e.title=e.title+s,e.content=e.content+s,n(e,c)}};e(c),window.plusShare=s}();