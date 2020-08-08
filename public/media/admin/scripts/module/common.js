var md_common = (function ($) {
    //CSR验证
    $.ajaxSetup({headers: {'X-CSRF-Token': $("#_token").val()}
    });
    //显示提示信息
    $.showMessage =  function (message, success) {
        if ($("#messageModal").size() == 0) {
            var html = '<div class="modal fade" id="messageModal" tabindex="-1" role="dialog"' +
                'aria-labelledby="myModalLabel" aria-hidden="true">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close"' +
                'data-dismiss="modal" aria-hidden="true">×' +
                '</button> <h4 class="modal-title" id="myModalLabel">提示信息</h4></div>' +
                '<div class="modal-body message">' + message + '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-default"' +
                'data-dismiss="modal">确定</button></div></div></div>';
            $("body").append(html);
        } else {
            $("#messageModal").find(".message").html(message);
        }
        $("#messageModal").modal();
        $('#messageModal').off("hidden.bs.modal").on('hidden.bs.modal', function () {
            if(typeof success == "function"){
                success();
            }
        });
    };
    //显示提示框
    $.showConfirm =  function (message, success) {
        if ($("#confirmeModal").size() == 0) {
            var html = '<div class="modal fade" id="confirmeModal" tabindex="-1" role="dialog"' +
                'aria-labelledby="myModalLabel" aria-hidden="true">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close"' +
                'data-dismiss="modal" aria-hidden="true">×' +
                '</button> <h4 class="modal-title" id="myModalLabel">提示信息</h4></div>' +
                '<div class="modal-body message">' + message + '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-default"' +
                'data-dismiss="modal">取消</button><button type="button" class="btn btn-primary success_confirm">确定</button></div></div></div>';
            $("body").append(html);
        } else {
            $("#confirmeModal").find(".message").html(message);
        }
        $("#confirmeModal").find(".success_confirm").off("click").on("click", function () {
            if (typeof success == "function") {
                $("#confirmeModal").modal('hide');
                success();
            }
        });
        $("#confirmeModal").modal();
    };
      //显示提示框
    $.showLoad =  function (layer) {
        if($("layer_load").size() == 0){
            $("body").append('<div class="layer_load"><div class="loading"><span></span></div></div>');
        }
        $(".layer_load").show();
        if(layer){
            $.showLayerCover();
        }
    };
    //隐藏加载块
    $.hideLoad = function(layer){
        $(".layer_load").hide();
        if(layer){
            $(".layer_cover").hide();
        }
    };
    //隐藏加载块
    $.showLayerCover = function(){
        if($(".layer_cover").size() == 0){
            $("body").append('<div class="layer_cover"></div>');
        }
        $(".layer_cover").show();
    };
    //隐藏加载块
    $.hideLayerCover = function(){
        $(".layer_cover").hide();
    };
    $.postAjax = function(url, data, success, error) {
        //data._csrf = yii.getCsrfToken();
        $.ajax({
            url: url,
            data: data,
            type: "post",
            dataType: "json",
            success: function(result){
                if(typeof success =="function"){
                    success(result);
                }
            },
            error: function(){
                if(typeof error =="function"){
                    error();
                }
            }
        });
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
    $.loadForm = function(form, data){
        var obj = form.find(".form-control");
        obj.each(function(key, o){
            var name = $(this).attr("name");
            if(data[name]){
                $(this).val(data[name]);
            }
            if($(this).is('select')){
                $(this).find('option[value="' + data[name] + '"]').prop('selected', 'selected');
            }
        });
    };
    $.clearForm = function(form){
        form.find(".form-control").val("");
        form.find("label.error").remove();
    };
    $.tmeplate = function(item, json) {
        var pattern = /\{([^\x00-\xff]*\w*[:]*[=]*)\}(?!})/g;   //匹配{}
        var template = item.html();
        var thisob = item[0];
        var temphtml = "";
        if (json.length > 0) {   //数组处理
            $.each(json, function(i, v) { 
                var temp = template.replace(pattern, function(match, key, value) {   
                    return typeof v[key] != 'undefined' ? v[key] : "";
                });
                temphtml += temp;
            })
            return temphtml;
        }
    };
    /*模板加载*/
    $.tpl = function(content, data){
        data = data || {};
        var list = ['var tpl = "";'];
        var codeArr = transform(content);  // 代码分割项数组
        for (var i = 0, len = codeArr.length; i < len; i++) {
            var item = codeArr[i]; // 当前分割项

            if (item.type == 1) {  // js逻辑
                list.push(item.txt);
            }
            else if (item.type == 2) {  // js占位
                var txt = 'tpl+=' + item.txt + ';';
                list.push(txt);
            }
            else {  //文本
                var txt = 'tpl+="' +
                    item.txt.replace(/"/g, '\\"') +
                    '";';
                list.push(txt);
            }
        }
        list.push('return tpl;');
        return new Function('data', list.join('\n'))(data);
    };
    /*从原始模板中提取 文本/js 部分*/
    function transform(content) {
        var arr = [];                 //返回的数组，用于保存匹配结果
        var reg = /<%([\s\S]*?)%>/g;  //用于匹配js代码的正则
        var match;                    //当前匹配到的match
        var nowIndex = 0;             //当前匹配到的索引     

        while (match = reg.exec(content)) {
            // 保存当前匹配项之前的普通文本/占位
            appendTxt(arr, content.substring(nowIndex, match.index));
            //保存当前匹配项
            var item = {
                type: 1,      // 类型  1- js逻辑 2- js 占位 null- 文本
                txt: match[1] // 内容
            };
            if (match[1].substr(0,1) == '=') {  // 如果是js占位
                item.type = 2;
                item.txt = item.txt.substr(1);
            }
            arr.push(item);
            //更新当前匹配索引
            nowIndex = match.index + match[0].length;
        }
        //保存文本尾部
        appendTxt(arr, content.substr(nowIndex));
        return arr;
    }
    /*普通文本添加到数组，对换行部分进行转义*/
    function appendTxt(list, content) {
        content = content.replace(/\r?\n/g, "\\n");
        list.push({ txt: content });
    }
    /*模板加载*/
    var app = {};
    app.init = function(){
        $(".main_content").css("margin-top", $(".site_nav_top").height() + 20);
        $(window).resize(function(){
            $(".main_content").css("margin-top", $(".site_nav_top").height() + 20);
        });
        //时间控件选择
        if($(".laydate-icon").size() > 0){
            if(typeof laydate != undefined){
                laydate.skin('molv');
                //开始时间
                var start = {
                    format: 'YYYY-MM-DD hh:mm:ss',
                    istime: true,
                    istoday: false,
                    isclear: false
                };
                //结束时间
                var end = {
                    format: 'YYYY-MM-DD hh:mm:ss',
                    istime: true,
                    istoday: false,
                    isclear: false
                };
                $(".start_date").on("click", function(){
                    laydate(start);
                });
                $(".end_date").on("click", function(){
                    laydate(end);
                });
                //开始时间
                var start1 = {
                    format: 'YYYY-MM-DD',
                    istime: false,
                    istoday: false,
                    isclear: false
                };
                $(".start_date1").on("click", function(){
                    laydate(start1);
                });
            }
        }
        //选择下拉过滤查询框
        $(".select_group_filter").on("change", function(){
            var form_group= $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            //判断是选择下拉过滤查询框
            if (elem.hasClass("select_group_option")) {
                var query = $(elem).val();
                form_group.siblings(".form-group.field_query").addClass('hide').find(".form-control").attr("disabled", 'disabled');
                form_group.siblings(".form-group."+query).removeClass('hide').find(".form-control").removeAttr("disabled");
            }
        });
        if($(".select_group_filter").size() > 0){
            $(".select_group_filter").each(function(){
                var select_group_filter = $(this).find("option:selected").val();
                $(".form-group." + select_group_filter).siblings(".form-group.field_query").addClass('hide').find(".form-control").attr("disabled", 'disabled');
                $(".form-group." + select_group_filter).removeClass('hide').find(".form-control").removeAttr("disabled");
            })
        }
        this.descriptionEditor('edit_description');
        this.listEvent();
        $(".j_modal_link").on("click", function(){
            var modal_id = $(this).attr('data-modal-id');
            $("#" + modal_id).modal();
        });
        $(".j_edit_modal_link").on("click", function(){
            var modal_id = $(this).attr('data-modal-id');
            var action = $(this).attr('data-load-action');
            var id = $(this).attr('data-id');

            $.post(action, {'id': id}, function(result){
                if(result.code == '200'){
                    var data = result.data;
                    var modal = $("#" + modal_id)
                    var form = modal.find('form');
                    $.each(data, function(key, value){
                        form.find('[name="' + key +'"]').val(value);
                        form.find('[data-name="' + key +'"]').text(value);
                    });
                    modal.modal();
                }
            }, 'json')
        });
        $(".ajax_form").on("submit", function(){
            var form = $(this);
            if(form.valid()){
                var action = $(this).attr('action');
                var data = form.serializeObject();
                $.post(action, data, function(result){
                    if(result.code == '200'){
                        window.location.reload();
                    } else if(result.message != ''){
                        $.showMessage(result.message);
                    }
                }, 'json')
            }
        });
    };
    app.listEvent = function(){
        //删除数据
        $(".remove_item").on("click", function(){
            var id = $(this).attr("data-id");
            var action = $(this).attr("data-action");
            if(action && id){
                $.showConfirm('确认删除数据！', function(){
                    $.post(action, {'id':id} ,function(result){
                        if(result.code == "0x00000"){
                            window.location.reload();
                        }
                    }, 'json').error(function(){
                        $.showMessage("网络问题或者系统错误，请联系管理员");
                    });
                })
            }
        });
        //选择全部
        $(".rows_check").on("click", function(){
            if($(this).is(":checked")){
                $(".row_check").prop("checked", true);
            }
            else{
                $(".row_check").removeAttr("checked");
            }
        });
        //批量删除
        $("#remove_items").on("click", function(){
            if($(".row_check:checked").size() == 0){
                $.showMessage("对不起，请先选择记录!");
                return false;
            }
            else{
                var action = $(this).attr("data-action");
                if(action){
                    $.showConfirm('确认删除数据！', function(){
                        var id = "";
                        $(".row_check:checked").each(function(){
                            id = id + $(this).attr("data-id") + ",";
                        });
                        if(id.length > 0){
                            id = id.substring(0, id.length-1);
                        }
                        $.post(action, {'ids' : id}, function(result){
                            if(result.code == "0x00000"){
                                window.location.reload();
                            }
                        }, 'json').error(function(){
                            $.showMessage("网络问题或者系统错误，请联系管理员");
                        });
                    });
                }
            }
        });
    };
    //编辑器功能
    app.descriptionEditor = function(name){
        if(typeof wangEditor == "undefined" ){
            return null;
        }
        if($("#" + name).size() == 0){
            return null;
        }
        wangEditor.config.printLog = false;
        var ceditor = new wangEditor(name);
        // 自定义菜单
        ceditor.config.menus = ['image', 'link','unlink','bold','fontsize','forecolor','bgcolor', 'alignleft','aligncenter','alignright','undo','redo'];
        // 使用英语
        ceditor.config.lang = wangEditor.langs['en'];
        // 颜色
        ceditor.config.colors = {'#880000': 'Dark Red','#800080': 'Purple','#ff0000': 'Red','#ff00ff': 'Fresh pink','#000080': 'Navy Blue','#0000ff': 'Blue','#00ffff': 'Lake Blue','#008080': 'Blue-Green','#008000': 'Green','#808000': 'Olive','#00ff00': 'Light Green','#ffcc00': 'Orange','#808080': 'Gray','#c0c0c0': 'Silver','#000000': 'Black','#ffffff': 'White'};
        ceditor.create();
        return ceditor;
    };
    return app;
})(jQuery);
if (typeof md_common.init == "function") {
    $(function () {
        md_common.init();
    });
}