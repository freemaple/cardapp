define(["zepto","bc"],function(e,n){function t(e){for(var n,t=[],i=/@#([\s\S]*?)@#/g,o=0;n=i.exec(e);){r(t,e.substring(o,n.index));var a={type:1,txt:n[1]};"="==n[1].substr(0,1)&&(a.type=2,a.txt=a.txt.substr(1)),t.push(a),o=n.index+n[0].length}return r(t,e.substr(o)),t}function r(e,n){n=n.replace(/\r?\n/g,"\\n"),e.push({txt:n})}var o=function(e){for(var t=new n,r=t.decode(e),i=[],o=r.length,a=o;a>=0;a--)i.push(r[a]);return i.join("")};e.reverse=function(e){return e.split("").reverse().join("")},e.password=function(t){t=e.reverse(t);var r=new n,i=r.encode(t);return i},e.setCookie=function(e,n,t){var r=new Date;document.domain;r.setTime(r.getTime()+6e4*t),document.cookie=e+"="+escape(n)+(null===t?"":";expires="+r.toGMTString())},e.getCookie=function(e){if(document.cookie.length>0){for(var n=e+"=",t=document.cookie.split(";"),r=0;r<t.length;r++){var i=t[r].trim();if(0==i.indexOf(n))return i.substring(n.length,i.length)}return""}return""},e.fn.prevAll=function(n){var t=[],r=this[0];if(!r)return e([]);for(;r.previousElementSibling;){var i=r.previousElementSibling;n?e(i).is(n)&&t.push(i):t.push(i),r=i}return e(t)},e.fn.nextAll=function(n){var t=[],r=this[0];if(!r)return e([]);for(;r.nextElementSibling;){var i=r.nextElementSibling;n?e(i).is(n)&&t.push(i):t.push(i),r=i}return e(t)},e.fn.serializeObject=function(){var n={},t=this.serializeArray();return e.each(t,function(){void 0!==n[this.name]?(n[this.name].push||(n[this.name]=[n[this.name]]),n[this.name].push(e.trim(this.value)||"")):n[this.name]=e.trim(this.value)||""}),n};var a=o(e("#sf").attr("data-sf"));e.ajaxSettings=e.extend(e.ajaxSettings,{headers:{xt:a},error:function(e){u(e,null)}});var u=function(n,t){if(n.responseText){var r=n.responseText,i=e.parseJSON(r);"function"==typeof t&&t(i),s(i)}else require(["mylayer"],function(e){e.showTip("哎呀，发生错误了，等会再试下!",3e3,"error")})},s=function(n){n&&n.code?"TOKEN_MISMATCH"==n.code?require(["mylayer"],function(e){e.showMessage(n.message,function(){window.location.reload()})}):"UNAUTH"==n.code&&require(["loginReg"],function(e){e.showLoginAction(null,!1)}):e(".layer-load:visible").size()>0&&require(["mylayer"],function(e){e.hideLoad(),e.showTip("哎呀，发生错误了，等会再试下!",3e3,"error")})};e.showRequestError=function(n,t,r){var i="";n&&("undefined"!=typeof n.errors&&n.errors?e.each(n.errors,function(e,n){i+=n}):"undefined"!=typeof n.exception&&n.exception||"undefined"!=typeof n.message&&""!=n.message&&(i=n.message)),""==i&&(i="哎呀，发生错误了，等会再试下!"),require(["mylayer"],function(e){e.hideLoad(),"msg"==t?e.showMessage(i,function(){"function"==typeof r&&r()}):(e.showTip(i,3e3,"error"),"function"==typeof r&&r())})},e.ajaxGet=function(n,t,r,i,o){e.ajaxRequest(n,"Get",t,r,i,o)},e.ajaxPost=function(n,t,r,i,o){e.ajaxRequest(n,"POST",t,r,i,o)},e.ajaxRequest=function(n,t,r,i,o,s){var l={xt:a};s&&(l=e.extend(l,s));var f={type:t?t:"POST",url:n,data:r,dataType:"json",headers:l,success:function(e){"function"==typeof i&&i(e)},error:function(e){u(e,o)}};e.ajax(f)},e.getQueryVariable=function(e){for(var n=window.location.search.substring(1),t=n.split("&"),r=0;r<t.length;r++){var i=t[r].split("=");if(i[0]==e)return i[1]}return!1},e.changeURLArg=function(e,n,t){var r="";if(e.indexOf("?")==-1)return e+"?"+n+"="+t;r=e.substr(e.indexOf("?")+1);var o,a="",u="",s="0";if(r.indexOf("&")!=-1){o=r.split("&");for(i in o)o[i].split("=")[0]==n?(u=t,s="1"):u=o[i].split("=")[1],a=a+o[i].split("=")[0]+"="+u+"&";a=a.substr(0,a.length-1),"0"==s&&a==r&&(a=a+"&"+n+"="+t)}else r.indexOf("=")!=-1?(o=r.split("="),o[0]==n?(u=t,s="1"):u=o[1],a=o[0]+"="+u,"0"==s&&a==r&&(a=a+"&"+n+"="+t)):a=n+"="+t;return e.substr(0,e.indexOf("?"))+"?"+a},e.delParam=function(e,n){var t=e.substr(e.indexOf("?")+1),r=e.substr(0,e.indexOf("?")),i="",o=new Array;if(""!=t)for(var a=t.split("&"),u=0;u<a.length;u++){var s=a[u].split("=");s[0]!=n&&o.push(a[u])}return o.length>0&&(i="?"+o.join("&")),e=r+i},e.tran=function(n,t){if(null==n)return null;var r=n.split("."),i="";return"object"==typeof lanConfig&&e.each(r,function(e,n){""==i&&"undefined"!=typeof lanConfig[n]?i=lanConfig[n]:"undefined"!=typeof i[n]&&(i=i[n])}),"string"!=typeof i||""==i?"undefined"!=typeof t?t:"":i},e.animateScroll=function(e,n,t){var r=e.scrollTop,i=n-r,o=0,a=20,u=function(e,n,t,r){return e/=r/2,e<1?t/2*e*e+n:(e--,-t/2*(e*(e-2)-1)+n)},s=function(){o+=a;var n=u(o,r,i,t);e.scrollTop=n,o<t&&setTimeout(s,a)};s()},e.saveFile=function(e,n,t){if("undefined"!=typeof plus){quality=100;var r=new plus.nativeObj.Bitmap("test");r.loadBase64Data(e,function(){r.save("/sdcard/"+n,{overwrite:!0,quality:quality},function(e){plus.gallery.save(e.target,function(n){r.clear(),plus.nativeUI.closeWaiting(),"function"==typeof t&&t(e.target)},function(){plus.nativeUI.closeWaiting(),alert("保存失败，请重试！")})},function(e){alertg("保存图片失败："+JSON.stringify(e))})},function(e){alert("加载图片失败："+JSON.stringify(e))})}},e.saveUrlFile=function(e,n,t){if("undefined"!=typeof plus){var r=plus.downloader.createDownload(e,{},function(e,n){if("200"==n){var r=e.filename;plus.gallery.save(r,function(e){plus.nativeUI.closeWaiting(),"function"==typeof t&&t(r)},function(){plus.nativeUI.closeWaiting(),alert("保存失败，请重试！")})}});r.start()}},e.onmarked=function(e,n,t){switch(e){case plus.barcode.QR:e="QR";break;case plus.barcode.EAN13:e="EAN13";break;case plus.barcode.EAN8:e="EAN8";break;default:e="其它"+e}n=n.replace(/\n/g,""),n=n.toLowerCase(),n=n.replace(/\"/g,""),plus.runtime.openURL(n)},e.barcode=function(n,t){if("undefined"!=typeof plus){var r=plus.downloader.createDownload(n,{},function(n,t){200==t&&plus.barcode.scan(n.filename,e.onmarked,function(e){plus.nativeUI.alert("无法识别此图片")})});r.start()}},e.tmeplate=function(n,t){var r=/\{([^\x00-\xff]*\w*[:]*[=]*)\}(?!})/g,i=n.html(),o=(n[0],"");if(t.length>0)return e.each(t,function(e,n){var t=i.replace(r,function(e,t,r){return"undefined"!=typeof n[t]?n[t]:""});o+=t}),o},e.tpl=function(e,n){n=n||{};for(var r=['var tpl = "";'],i=t(e),o=0,a=i.length;o<a;o++){var u=i[o];if(1==u.type)r.push(u.txt);else if(2==u.type){var s="tpl+="+u.txt+";";r.push(s)}else{var s='tpl+="'+u.txt.replace(/"/g,'\\"')+'";';r.push(s)}}return r.push("return tpl;"),new Function("data",r.join("\n"))(n)},e.fn.longPress=function(e){for(var n=void 0,t=this,r=0;r<t.length;r++)t[r].addEventListener("touchstart",function(r){n=setTimeout(function(){e(t)},400)},!1),t[r].addEventListener("touchend",function(e){clearTimeout(n)},!1)},e.setlocalStorage=function(e,n){var t=(new Date).getTime();window.localStorage.setItem(e,JSON.stringify({data:n,time:t}))},e.getlocalStorage=function(e,n){try{var t=window.localStorage.getItem(e);if(t){var r=JSON.parse(t);if((new Date).getTime()-r.time>n)return null;var i=JSON.parse(r.data);return i}}catch(e){return null}return null}});