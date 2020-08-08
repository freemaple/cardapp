require(["zepto","base","mylayer","validate"],function(t,e,a,n){var r={init:function(){var e=this;t(".card-background-image").on("load",function(){t(".bg-btn").removeClass("hover")}),t(".card-background-item").on("click",function(){var e=t(this).find("img").attr("src"),a=t(this).attr("data-image");t(".card-background-item").removeClass("current"),t(this).addClass("current"),t(".card-background-image").attr("src",e).attr("data-image",a)}),t(".js-bg-left").on("click",function(){var a=e.getCurrent("left");t(".card-background-item").removeClass("current"),a.addClass("current");var n=a.attr("data-src"),r=a.attr("data-image");t(this).addClass("hover"),t(".card-background-image").attr("src",n).attr("data-image",r)}),t(".js-bg-right").on("click",function(){var a=e.getCurrent("right");t(".card-background-item").removeClass("current"),a.addClass("current");var n=a.attr("data-src"),r=a.attr("data-image");t(this).addClass("hover"),t(".card-background-image").attr("src",n).attr("data-image",r)}),t(".js-save-screen").on("click",function(){var n=t(".product_front_design"),r={left:parseFloat(n.css("left")),top:parseFloat(n.css("top")),width:n.width(),height:n.height()},i=t(".card-background-image").attr("data-image");a.showLoad(),t.ajaxPost("/api/card/screen",{img:i,postion:r},function(n){"Success"==n.code&&n.data&&("undefined"!=typeof plus?t.saveFile(n.data,e.random()+".jpg",function(){a.showTip("图片已保存到手机相册",3e3,"success"),a.hideLoad()}):a.hideLoad(),t(".screen-img").attr("src",n.data),t(".success-box").show(),t(".screen-box").hide(),t(".wrap-content").addClass("pd-small"))})}),this.drop(),this.mzoom()},getCurrent:function(e){var a=t(".card-background-item").length,n=t(".card-background-item.current").index();return 0==t(".card-background-item.current").size()&&(n=0),"right"==e?n==a-1?t(".card-background-item").first():t(".card-background-item").eq(n+1):"left"==e?0==n?t(".card-background-item").last():t(".card-background-item").eq(n-1):void 0},random:function(){var t=parseInt((new Date).getTime()/1e3),e=Math.floor(1e5*Math.random());return t+""+e},saveFile1:function(t,e){},downFile:function(t){var e=this;if("undefined"!=typeof plus){var a=plus.downloader.createDownload(t,{},function(t,a){200==a?plus.gallery.save(t.filename,e.random()+".jpg",function(){plus.nativeUI.closeWaiting(),alert("已保存到手机相册")},function(){plus.nativeUI.closeWaiting(),alert("保存失败，请重试！")}):alert("Download failed: "+a)});a.start()}},saveFile:function(t,e){if("undefined"!=typeof plus){quality=100;var n=new plus.nativeObj.Bitmap("test");n.loadBase64Data(t,function(){n.save("/sdcard/1.jpg",{overwrite:!0,quality:quality},function(t){plus.gallery.save(t.target,function(t){n.clear(),plus.nativeUI.closeWaiting(),a.showTip("已保存到手机相册",3e3,"success"),"function"==typeof e&&e()},function(){plus.nativeUI.closeWaiting(),alert("保存失败，请重试！")})},function(t){alertg("保存图片失败："+JSON.stringify(t))})},function(t){alert("加载图片失败："+JSON.stringify(t))})}},drop:function(){var e,a,n,r,i,o,c,s,d;t(".product_front_design").on("mousedown",function(i){r=t(this);var o=i||window.event;return e=o.clientX-parseFloat(r.css("left")),a=o.clientY-parseFloat(r.css("top")),n=!0,!1}),t(".design_front_zoom").on("mousedown",function(n){r=t(this).parent().find(".product_front_design");var u=n||window.event;return e=u.clientX,a=u.clientY,o=r.width(),c=r.height(),s=parseFloat(r.css("left")),d=parseFloat(r.css("top")),i=!0,!1}),t(document).on("mousemove",function(u){var l=u||window.event;t(u.target||u.srcElement);if(n){var f=l.clientX-e,g=l.clientY-a;r.css({left:f+"px",top:g+"px"})}else if(i){var p=l.clientX,m=(l.clientY,o+2*(p-e)),v=m/o,h=c*v;if(v>0){var w=s-(m-o)/2,b=d-(h-c)/2;r.css({width:m+"px",left:w+"px",top:b+"px"})}}}),t(document).on("mouseup",function(t){t||window.event;n=!1,i=!1})},mzoom:function(){var e,a,n,r,i,o,c,s,d;t(".product_front_design").on("touchstart",function(i){var o=i.targetTouches[0];return r=t(this),e=o.clientX-parseFloat(r.css("left")),a=o.clientY-parseFloat(r.css("top")),n=!0,!1}),t(document).on("touchmove",function(t){var u=t.targetTouches[0];if(n){var l=u.clientX-e,f=u.clientY-a;r.css({left:l+"px",top:f+"px"})}else if(i){var g=u.clientX,p=(u.clientY,o+2*(g-e)),m=p/o,v=c*m;if(m>0){var h=s-(p-o)/2,w=d-(v-c)/2;r.css({width:p+"px",left:h+"px",top:w+"px"})}}})}};"function"==typeof r.init&&t(function(){r.init()})});