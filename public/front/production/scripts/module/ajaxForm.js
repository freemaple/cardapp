define(["jquery"],function($){var ajaxForm=function(cfg){if(!window.FormData)return!1;this.isNullOrUndefined=function(t,r){return!t};var cfg=cfg||{};if(!this.isNullOrUndefined(cfg.url,"url can't be empty")&&cfg.form){this.form=cfg.form,this.method=cfg.method||"POST",this.url=cfg.url,this.async=!cfg.sync,this.resultType=cfg.resultType||"json",this.formData=new FormData(this.form),this.xhr=new XMLHttpRequest,cfg.timeout&&(this.xhr.timeout=cfg.timeout,this.xhr.ontimeout=cfg.onTimeout),cfg.onProgress&&(this.xhr.upload.onprogress=function(t){t.lengthComputable&&cfg.onProgress(t.loaded,t.total)});var _this=this;cfg.onComplete&&(this.xhr.onload=function(event){var xmlhttp=event.target,res=event.target.responseText;4==xmlhttp.readyState&&200==xmlhttp.status?"json"==_this.resultType&&(res="undefined"==typeof JSON?eval("("+res+")"):JSON.parse(res),cfg.onComplete(res)):cfg.onError&&cfg.onError()}),cfg.onError&&(this.xhr.onerror=function(t){cfg.onError(t)}),this.request=function(){this.xhr.open(this.method,this.url,this.async),this.xhr.send(this.formData)}}};return ajaxForm});