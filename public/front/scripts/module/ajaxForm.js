define(['jquery'], function ($){
    var ajaxForm = function (cfg) {
        if (!window.FormData) {
            return false;
        }
        /* null or undefined 返回true,否则false*/
        this.isNullOrUndefined = function (v, errMsg) {
            if (!v) {
                return true;
            }
            return false;
        };
        var cfg = cfg || {};
        if (this.isNullOrUndefined(cfg.url, "url can't be empty")) return;
        if (!cfg.form) return;
        this.form = cfg.form; // 表单
        this.method = cfg.method || "POST"; //默认POST方法
        this.url = cfg.url;
        this.async = !cfg.sync; //同步否
        this.resultType = cfg.resultType || "json"; //返回结果类型 json对象或text
        this.formData = new FormData(this.form); //form数据
        this.xhr = new XMLHttpRequest(); //当前请求对象
        /*超时事件*/
        if (cfg.timeout) {
            this.xhr.timeout = cfg.timeout;
            this.xhr.ontimeout = cfg.onTimeout;
        }
        /*发送过程事件*/
        if (cfg.onProgress) { //发送数据过程
            this.xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    cfg.onProgress(e.loaded, e.total);
                }
            };
        }
        var _this = this;
        /*上传完成事件*/
        if (cfg.onComplete) {
            this.xhr.onload = function (event) {
                var xmlhttp = event.target;
                var res = event.target.responseText;
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    if (_this.resultType == 'json') {
                        if ((typeof JSON) === 'undefined') {
                            res = eval("(" + res + ")");
                        } else {
                            res = JSON.parse(res);
                        }
                        cfg.onComplete(res);
                    }
                } else {
                    if (cfg.onError) {
                        cfg.onError();
                    }
                }
            };
        }
        /*上传error事件*/
        if (cfg.onError) {
            this.xhr.onerror = function (e) {
                cfg.onError(e);
            };
        }
        /*发出请求*/
        this.request = function () {
            this.xhr.open(this.method, this.url, this.async);
            this.xhr.send(this.formData);
        };
    };
    return ajaxForm;
});
