 var mylayer = {};
mylayer.init = function(g){
    var self = this;
    //配置
    var option = {
        overlay: true,
        close: true,
        header: false,
        title: "",
        content: "",
        layerClose: true,
        position: 'center'
    };
    option = $.extend(option, g);
    var z_index = 0;
    //计算当前弹出层z-index
    $(".layerbox").each(function(){
        var index = parseInt($(this).css("z-index")) + 1;
        if(index > z_index){
            z_index = index;
        }
    });
    z_index = z_index <= 1001 ? 1001 : z_index;
    //生成随机id
    var id = this.random();
    option.id = id;
    //拼接内容
    var layerContent = this.layerContent(option);
    $("body").append(layerContent);
    //执行回调
    if (option.callback && typeof option.callback == "function") {
        option.callback();
    }
    var layerbox_id = "layerbox-" + id;
    var layerbox = $("#" + layerbox_id);
    layerbox.css({
        "z-index": z_index
    });
    self.initEvent(option);
    //遮盖层
    if (option.overlay) {
        $("body").append('<div class="overlay" data-id="' + id + '" id="overlay-' + id + '"></div>');
        var overlay = $("#overlay-" + id); 
        overlay.show().css("z-index", z_index -1);
        $(".overlay").on("click", function(){
            var id = $(this).attr('data-id');
            self.closeLayer(id);
        });
    }
    var layerbox_wrapper = layerbox.find(".layerbox-wrapper");
    layerbox.addClass('show');
    //空白点击关闭处理
    layerbox.off("click").on("click", function (e) {
        if((e.target.id == layerbox_id) && option.layerClose === true) {
            typeof option.closeEvent == "function" && option.closeEvent();
            self.closeLayer(id);
        }
    });
    if(option.position == 'center'){
        //居中处理
        $(window).on('resize', function(){
            self.resizeLayer(layerbox);
        })
        self.resizeLayer(layerbox);
    }
    $("body").addClass('overfixed');
    if(option.position == 'top'){
        $(".wrap-container").hide();
    }
    return layerbox;
};
//事件
mylayer.initEvent = function(c){
    var self = this;
    $(".layerbox .yes").off("click").on("click", function () {
        typeof c.yesBtn == "function" && c.yesBtn();
        var id = $(this).closest(".layerbox").attr("data-id");
        self.closeLayer(id);
    });
    $(".layerbox .no").off("click").on("click", function () {
        typeof c.noBtn == "function" && c.noBtn();
        var id = $(this).closest(".layerbox").attr("data-id");
        self.closeLayer(id);
    });
    $(".layerbox .js-close-layer").off("click").on("click", function () {
        typeof c.closeBtn == "function" && c.closeBtn();
        var id = $(this).closest(".layerbox").attr("data-id");
        if(typeof c.closeEvent == "function"){
            c.closeEvent()
        }
        self.closeLayer(id);
    });
};
//显示弹出层
mylayer.showLayer = function(layer){
    if(layer.size() > 0 && layer.hasClass('layerbox')){
        var id = layer.attr('data-id');
        layer.addClass('show');
        var overlay = $("#overlay-" + id); 
        overlay.show();
        $("body").addClass('overfixed');
    }
};
//隐藏弹出层
mylayer.hideLayer = function(layer){
    if(layer.size() > 0 && layer.hasClass('layerbox')){
        var id = layer.attr('data-id');
        this.closeLayer(id);
    }
};
//关闭弹出层
mylayer.closeLayer = function(id){
    var layerbox = $("#layerbox-" + id);
    if (layerbox.hasClass('no-remove')) {
        layerbox.removeClass('show');
        $("#overlay-" + id).hide();
    } else {
        if (layerbox.size() > 0) {
            layerbox.removeClass('show');
            setInterval(function(){
                layerbox.remove();
            },300);
        }
        $("#overlay-" + id).remove();
    }
    if(($(".layerbox").size() === 0 || $(".layerbox.show").length ==0)) {
        $(".wrap-container").show();
    }
    $("body").removeClass('overfixed');
};
//提示语
mylayer.showMessage = function(content, callback){
    var content_box = '<div class="layer-message clearfix"><div class="layer-message-content">' + content + '</div></div>';
    return this.init({
        content: content_box,
        yes: true,
        yesText: '确定',
        yesBtn: true,
        close: false,
        class_name: "show-layer-message",
        closeEvent: callback,
        yesBtn: function () {
            if (typeof callback== "function") {
                callback();
            }
        },
        closeBtn: function () {
            $("body").removeClass('overfixed');
            if (typeof callback== "function") {
                callback();
            }
        }
    });
};
//操作提示
mylayer.showConfirm = function(d, c){
    var content =
        '<div class="layer-message clearfix"><span class="layer-icon layer-icon-confirm"></span><span class="msg_content">' +
        d + '</span></div>';
    return this.init({
        content: content,
        header: true,
        title: '提示',
        close: false,
        yes: true,
        yesText: '确定',
        noText: '取消',
        class_name: "show-layer-message layer-confirm",
        yesBtn: function () {
            if (typeof c == "function") {
                c();
            }
        },
        no: true
    });
};
//加载层
mylayer.showLoad = function(layer){
    if($(".layer-load").size() == 0){
        $("body").append('<div class="layer-load"><div class="layer-loading"><span></span></div></div>');
    }
    $(".layer-load").show();
    if(layer){
        if($(".layer-cover").size() == 0){
            $("body").append('<div class="layer-cover"></div>');
        }
        $(".layer-cover").show();
    }
};
//隐藏加载层
mylayer.hideLoad = function(){
    $(".layer-load").hide();
    $(".layer-cover").hide();
};
//操作提示
mylayer.showLayerTop = function(content, callback){
    return this.init({
        content: content,
        close: false,
        position: 'top',
        callback: callback,
        class_name: 'layer-top bottom-to-top'
    });
};
//操作提示
mylayer.showLayerLeftTop = function(content, callback){
    return this.init({
        content: content,
        close: true,
        position: 'top',
        callback: callback,
        class_name: 'layer-top left-to-top'
    });
};
//操作提示
mylayer.showLayerBottom = function(content, callback){
    return this.init({
        content: content,
        close: true,
        class_name: "layer-bottom bottom-to-top",
        position: 'bottom',
        callback: callback
    });
};
//弹框内容处理
mylayer.layerContent = function(option){
    var id = option['id'] ? option['id'] : '';
    var content = option['content'] ? option['content'] : '';
    var content_header = '';
     if (option.header) {
        content_header = '<h4 class="layerbox-wrapper-title">';
        if (option.close) {
            content_header += '<a href="javascript:void(0)" title="close" class="layerbox-close-btn js-close-layer">×</a>';
        }
        content_header += option.title + "</h4>";
    } else {
       if (option.close) {
            content_header = '<a href="javascript:void(0)" title="close" class="layerbox-close-btn js-close-layer">×</a>';
        }  
    }
    var layerbox_footer = '';
    var footer_btn = '';
    if (option.no) {
        footer_btn += '<a class="layerbox-button no" href="javascript:void(0)">' + option.noText + "</a>";
    }
    if (option.yes) {
        footer_btn += '<a class="layerbox-button yes" href="javascript:void(0)">' + option.yesText + "</a>";
    }
    if(footer_btn){
        layerbox_footer = '<div class="layerbox-footer">' + footer_btn + '</div>';
    }
    var layerInfo  = 
    '<div class="layerbox ' + (option.class_name ? option.class_name : '') + '" id="layerbox-' + id + '" data-id="' + id +'">\
        <div class="layerbox-wrapper">\
            <div class="layerbox-content">' + content_header +
                '<div class="layerbox-wrapper-text">' + content +'</div>' + layerbox_footer +
            '</div>\
        </div>\
    </div>';
    return layerInfo;
};
//显示提示框
mylayer.showTip = function(content, time, tip_type, position){
    if($(".popover-inner").size() == 0){
        var tip = '<div class="popover-inner"><div class="popover-inner-content"></div></div>';
        $("body").append(tip);
    }
    if(tip_type == 'success'){
        $(".popover-inner").removeClass('error').addClass('success');
    } else {
        $(".popover-inner").removeClass('success').addClass('error');
    }
    if(position){
        $(".popover-inner").addClass(position);
    } else {
        $(".popover-inner").addClass('center');
    }
    $(".popover-inner").show();
    $(".popover-inner-content").html(content);
    setTimeout(function(){
        $(".popover-inner").hide();
    }, time ? time : 3000)
};
mylayer.hideTip = function(){
    $(".popover-inner").hide();
};
//生成当前时间戳+随机数
mylayer.random = function(){
    //当前时间戳
    var timestamp = parseInt(new Date().getTime()/1000);
    var r = Math.floor(Math.random()*100000); 
    return timestamp + "" + r;  
};
mylayer.getWinHeight = function () {
    if (window.innerHeight)
        winHeight = window.innerHeight;
    else if ((document.body) && (document.body.clientHeight))
        winHeight = document.body.clientHeight;
    if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth) {
        winHeight = document.documentElement.clientHeight;
    }
    return winHeight;
};
//居中处理
mylayer.resizeLayer= function(layer){
    var layerbox_wrapper = layer.find(".layerbox-wrapper");
    layerbox_wrapper.css({
        "left": "50%",
        "margin-left": "-" + layerbox_wrapper.width() / 2 + "px", //处理水平居中,
        "top": "50%",
        "margin-top": "-" + layerbox_wrapper.height() / 2 + "px"
    });
};