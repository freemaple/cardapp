define([],function(){var asyncForm=function(){function AsyncForm(t,e){that.op=e;var n=AsyncForm.uuid++;this.state=0,this.form=t,this.file=t;var o="upload_file_"+n;this.iframe=iframe({name:o,src:"javascript:;",cssText:"display:none;"}),document.body.appendChild(this.iframe),this.form.target=o}var that=this,iframe=function(t){t=t||{id:"iframe"+Math.random(),name:"iframe",src:""};var e;try{e=document.createElement("<iframe name="+t.name+">")}catch(n){e=document.createElement("iframe"),e.name=t.name}return t.id&&(e.id=t.id),e.src=t.src,e.style.cssText=t.cssText,e},getDoc=function(t){var e=t.contentWindow?t.contentWindow.document:t.contentDocument?t.contentDocument:t.document;return e},callbackFunction,cb=function(e){var doc=getDoc(this),docRoot=doc.body?doc.body:doc.documentElement,json=docRoot.innerHTML;try{var responseText=eval("("+json+")")}catch(t){var responseText=new Function("","return "+json)(),responseText=JSON.parse(json)}callbackFunction(responseText,e),document.body.removeChild(this)};return AsyncForm.uuid=0,AsyncForm.prototype={checkState:function(){var t=this,e=getDoc(this.iframe),n=e.readyState;n&&"uninitialized"==n.toLowerCase()&&setTimeout(function(){t.checkState.apply(t)},50)},submit:function(t){callbackFunction=t;var e=this;this.iframe.attachEvent?this.iframe.attachEvent("onload",function(t){cb.apply(e.iframe,[t])}):this.iframe.addEventListener("load",cb,!1),setTimeout(function(){e.checkState.apply(e)},15),this.form.submit()},readyState:function(){return this.state},cancel:function(){}},AsyncForm}();return asyncForm});