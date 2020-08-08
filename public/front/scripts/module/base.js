define(['jquery'], function ($) {
    var app = {
        //公共事件
        init: function () {
            //Ajax CSRF-Token验证
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $("#_token").val()
                }
            });
            //设置cookie
            $.setCookie = function (c_name, value, expiredays) {
                var exdate = new Date();
                exdate.setDate(exdate.getDate() + expiredays);
                document.cookie = c_name + "=" + escape(value) +
                    ((expiredays === null) ? "" : ";expires=" + exdate.toGMTString());
            };
            //获取cookie
            $.getCookie = function (c_name) {
                if (document.cookie.length > 0) {
                    c_start = document.cookie.indexOf(c_name + "=");
                    if (c_start != -1) {
                        c_start = c_start + c_name.length + 1;
                        c_end = document.cookie.indexOf(";", c_start);
                        if (c_end == -1) c_end = document.cookie.length;
                        return unescape(document.cookie.substring(c_start, c_end));
                    }
                }
                return "";
            };
            //post提交
            $.postForm = function (elem, url, data, success, error) {
                if (elem) {
                    //判断是否已禁用
                    if (elem.hasClass('disabled')) {
                        return false;
                    }
                    //提交后禁用,防止多次提交
                    elem.addClass('disabled');
                }
                //ajax登录
                $.post(url, data, function (result) {
                    if (elem) {
                        //提交完成,启用按钮
                        elem.removeClass('disabled');
                    }
                    //执行登录成功回调函数
                    if (typeof success == "function") {
                        success(result);
                    }
                }, 'json').error(function (result) {
                    if (elem) {
                        elem.removeClass('disabled');
                    }
                    if (typeof error == "function") {
                        error(result);
                    } else {
                        require(['mylayer'], function (mylayer) {
                            mylayer.showMessage("error", $.tran("common.requestError", "Oh~ damn it,Please be patient and I'm trying to speed up."));
                        });
                    }
                });
            };
            //判断是否存在
            $.isInArray = function (data, i, keyword) {
                var result = -1; //不存在返回-1
                keyword = keyword.toLowerCase(); //小写
                $.each(data, function (key, value) {
                    if (value[i].toLowerCase() == keyword) {
                        result = key; //存在返回索引
                        return false;
                    }
                });
                return result;
            };
            //表单数据序列化
            $.fn.serializeObject = function() {
              var o = {};
              var a = this.serializeArray();
              $.each(a, function() {
                  if (o[this.name] !== undefined) {
                      if (!o[this.name].push) {
                          o[this.name] = [o[this.name]];
                      }
                      o[this.name].push($.trim(this.value) || '');
                  } else {
                      o[this.name] = $.trim(this.value) || '';
                  }
              });
              return o;
            };
            //设置本地缓存
            $.setLocalStorage = function(key, value){
                try{
                   var storage = window.localStorage;
                    if(!storage){
                        return false;
                    } 
                    storage.setItem(key, value);
                    return true;
                } catch(e) {
                    return false;
                }
            };
            //获取本地缓存
            $.getLocalStorage = function(key){
                try{
                   var storage = window.localStorage;
                    if(!storage){
                        return null;
                    } 
                    return storage.getItem(key);
                } catch(e) {
                    return null;
                }
            };
            //删除本地缓存项
            $.removeLocalStorage = function(key){
                try{
                   var storage = window.localStorage;
                    if(!storage){
                        return false;
                    } 
                    storage.removeItem(key);
                    return true;
                } catch(e) {
                    return false;
                }
            };
            //获取缓存
            $.cacheGet = function(key){
                var cache_data = $.getLocalStorage(key);
                if(cache_data == null){
                    cache_data = $.getCookie(key);
                }
                return cache_data;
            };
            //设置缓存
            $.cachePut = function(key, value){
                var set_flag = $.setLocalStorage(key, value);
                if(!set_flag){
                    $.setCookie(key, value);

                }
                return true;
            };
            //删除缓存
            $.cacheRemove = function(key){
                var remove_flag = $.removeLocalStorage(key);
                if($.getCookie(key) != ''){
                    $.setCookie(key, '');
                }
                return true;
            };
            //处理url参数值
            $.changeURLArg = function(url, ref, value){
                var str = "";
                if (url.indexOf('?') != -1)
                    str = url.substr(url.indexOf('?') + 1);
                else
                    return url + "?" + ref + "=" + value;
                var returnurl = "";
                var setparam = "";
                var arr;
                var modify = "0";
                if (str.indexOf('&') != -1) {
                    arr = str.split('&');
                    for (i in arr) {
                        if (arr[i].split('=')[0] == ref) {
                            setparam = value;
                            modify = "1";
                        }
                        else {
                            setparam = arr[i].split('=')[1];
                        }
                        returnurl = returnurl + arr[i].split('=')[0] + "=" + setparam + "&";
                    }
                    returnurl = returnurl.substr(0, returnurl.length - 1);
                    if (modify == "0")
                        if (returnurl == str)
                            returnurl = returnurl + "&" + ref + "=" + value;
                }
                else {
                    if (str.indexOf('=') != -1) {
                        arr = str.split('=');
                        if (arr[0] == ref) {
                            setparam = value;
                            modify = "1";
                        }
                        else {
                            setparam = arr[1];
                        }
                        returnurl = arr[0] + "=" + setparam;
                        if (modify == "0")
                            if (returnurl == str)
                                returnurl = returnurl + "&" + ref + "=" + value;
                    }
                    else
                        returnurl = ref + "=" + value;
                }
                return url.substr(0, url.indexOf('?')) + "?" + returnurl;
            };
            //删除参数
            $.delParam = function(url, paramKey){
                var urlParam = url.substr(url.indexOf("?") + 1);
                var beforeUrl = url.substr(0, url.indexOf("?"));
                var nextUrl = "";
                var arr = new Array();
                if(urlParam != ""){
                    var urlParamArr = urlParam.split("&");
                    for(var i = 0; i < urlParamArr.length; i++){
                        var paramArr = urlParamArr[i].split("=");
                        if(paramArr[0] != paramKey){
                            arr.push(urlParamArr[i]);
                        }
                    }
                }
                if(arr.length>0){
                    nextUrl = "?"+arr.join("&");
                }
                url = beforeUrl + nextUrl;
                return url;
            };
            //获取语言
            $.tran = function(key, default_value){
                if(key == null){
                    return null;
                }
                var key_array = key.split(".");
                var value = '';
                if(typeof lanConfig == "object"){
                    $.each(key_array, function(key, key_value){
                        if(value == '' && typeof lanConfig[key_value] != "undefined"){
                            value = lanConfig[key_value];
                        }
                        else {
                            if(typeof value[key_value] != "undefined"){
                                value = value[key_value];
                            }
                        }
                    });
                }
                if(typeof value != "string" || value == ""){
                    return default_value;
                }
                return value;
            };
            //验证提示多语言设置
            $.validatorMessageConfig = function(){
               if(typeof lanConfig == "object" && typeof lanConfig.validatorMessage == "function"){
                    //验证提示多语言设置
                    lanConfig.validatorMessage();
                } 
            }
            $(".panel").on('click', function(){
                var panel = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                //判断是点击注册提交按钮
                if (elem.hasClass("close_btn")) {
                    panel.fadeOut();
                }
            });
        }
    };
    if (typeof app !== undefined) {
        app.init();
    }
    return app;
});