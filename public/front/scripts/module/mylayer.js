define(['jquery'], function ($) {
    var b = {
        init: function (g) {
            var d = {
                yesText: $.tran("text.ok", '是'),
                noText: $.tran("text.cancel", '否'),
                overlay: true,
                close: true,
                title: "",
                header: true,
                content: "",
                closeBtn: "",
                layerClose: true
            };
            var c = $.extend(d, g);
            var i = this;
            var h = 0;
            $(".layerBox").each(function(){
                var index = parseInt($(this).attr("index")) +1;
                if(index>h){
                    h = index;
                }
            });
            var f = '<div class="layerBox mylayer ' + (c.no_remove ? "no_remove" : "") + ' ' + (c.class_name ? c.class_name :
                "") + ' ' + (c.phoneCover ? "phoneCover" : "layerbox_com") + '" index="' + h + '"  id="layerBox' + h +
                '">';
            if (c.closeFixed) {
                f +=
                    '<div class="layerBox_wrapper_fixed"><div class="layerBox_wrapper_fixed_content"><a href="javascript:void(0)" title="close" class="fixed_close close_btn close">×</a></div></div>';
            }
            f += '<div class="layerBox_wrapper"><div class="layerBox_content">';
            if (c.header) {
                f += '<h4 class="layerBox_title">';
                if (c.close) {
                    f += '<a href="javascript:void(0)" title="close" class="close_btn close">×</a>';
                }
                f += c.title + "</h4>";
            }
            f += '<div class="layerBox_text">' + c.content + '</div><div class="layerBox_footer" align="center">';
            if (c.yes) {
                f += '<a class="btn btn_primary yes" href="javascript:void(0)">' + c.yesText + "</a>";
            }
            if (c.no) {
                f += '<a class="btn btn_default no" href="javascript:void(0)">' + c.noText + "</a>";
            }
            f += "</div></div></div></div>";
            if (c.overlay) {
                $("body").append('<div class="overlay overlay_' + (c.class_name ? c.class_name : "") + '" id="overlay' +
                    h + '"></div>');
                $("#overlay" + h).show();
            }
            $("body").append(f);
            if (c.success && typeof c.success == "function") {
                c.success();
            }
            var z_index = 10000 + h + 1;
            if (z_index < $(".layer_login").css('z-index')) {
                z_index = $(".layer_login").css('z-index') + 2;
            }
            $("#layerBox" + h).css({
                "z-index": z_index
            });
            var j = $("#layerBox" + h).find(".layerBox_wrapper");
            j.css({
                left: "50%",
                "margin-left": "-" + j.width() / 2 + "px",
            });
            if (!d.isHeight) {
                var e = this.getWinHeight();
                if (j.height() < e - 150) {
                    j.css({
                        top: "50%",
                        "margin-top": "-" + j.height() / 2 + "px",
                    });
                } else {
                    j.css({
                        top: "10px"
                    });
                }
            }
            $("#layerBox" + h).addClass('show');
            i.initEvent(h, c);
            $("#overlay" + h).css("z-index", z_index);
            $("#layerBox" + h).off("click").on("click", function (e) {
                if ((e.target.id == "layerBox" + h) && c.layerClose === true) {
                    if (d.closeEvent && typeof d.closeEvent == "function") {
                        d.closeEvent();
                    }
                    i.closeLayer(h);
                }
            });
            return h;
        },
        initEvent: function (d, c) {
            var e = this;
            $(".layerBox .yes").off("click").on("click", function () {
                if (c.yesBtn) {
                    if (typeof c.yesBtn == "function") {
                        c.yesBtn();
                    }
                }
                var index = $(this).closest(".layerBox").attr("index");
                e.closeLayer(index);
            });
            $(".layerBox .no").off("click").on("click", function () {
                if (c.noBtn) {
                    if (typeof c.noBtn == "function") {
                        c.noBtn();
                    }
                }
                var index = $(this).closest(".layerBox").attr("index");
                e.closeLayer(index);
            });
            $(".layerBox .close").off("click").on("click", function () {
                if (c && c.closeBtn) {
                    if (typeof c.closeBtn == "function") {
                        c.closeBtn();
                    }
                }
                var index = $(this).closest(".layerBox").attr("index");
                if (c.closeEvent && typeof c.closeEvent == "function") {
                    c.closeEvent();
                }
                e.closeLayer(index);
            });
        },
        closeLayer: function (c) {
            var layerBox = $("#layerBox" + c);
            if (layerBox.hasClass('no_remove')) {
                layerBox.removeClass('show');
                $("#overlay" + c).hide();
            } else {
                if (layerBox.size() > 0) {
                    layerBox.removeClass('show');
                    setInterval(function(){
                        layerBox.remove();
                    },300);
                }
                $("#overlay" + c).remove();
            }
            if(($(".layerBox ").size() === 0 || $(".layerBox.show").length ==0)) {
                //$("html,body").css("overflow-y", "auto");
            }
        },
        showMessage: function (t, c, e, d) {
            var classname = t == "success" ? "layer_icon_success" : "layer_icon_error";
            var content = '<div class="layerMessage clearfix"><span class="layer_icon ' + classname +
                '"></span><span class="msg_content">' + c + '</span></div>';
            return this.init({
                content: content,
                yes: true,
                yesText: $.tran("text.ok", 'Ok'),
                success: d,
                yesBtn: e,
                class_name: "show_message",
                closeBtn: function () {
                    //$("html,body").css("overflow-y", "auto");
                },
                closeEvent: function(){
                    if(typeof e == 'function'){
                        e();
                    }
                }
            });
        },
        showConfirm: function (d, c) {
            var content =
                '<div class="layerMessage clearfix"><span class="layer_icon layer_icon_confirm"></span><span class="msg_content">' +
                d + '</span></div>';
            return this.init({
                content: content,
                yes: true,
                class_name: "show_message",
                yesBtn: function () {
                    if (typeof c == "function") {
                        c();
                    }
                },
                no: true
            });
        },
        showLoad: function (layerClose) {
            return this.init({
                title: "",
                close: false,
                header: false,
                layerClose: layerClose ? layerClose : false,
                content: '<div class="easy_load"><span class="easy_loading"></span></div>'
            });
        },
        getWinHeight: function () {
            if (window.innerHeight)
                winHeight = window.innerHeight;
            else if ((document.body) && (document.body.clientHeight))
                winHeight = document.body.clientHeight;
            if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth) {
                winHeight = document.documentElement.clientHeight;
            }
            return winHeight;
        },
        resizeLayer: function(layer){
            $("html,body").css("overflow-y", "hidden");
            var layerBox_wrapper = layer.find(".layerBox_wrapper");
            layerBox_wrapper.css({
                "left": "50%",
                "margin-left": "-" + layerBox_wrapper.width() / 2 + "px", //处理水平居中
            });
            var height = this.getWinHeight();
            if (layerBox_wrapper.height() < height) {
                layerBox_wrapper.css({
                    "top": "50%",
                    "margin-top": "-" + layerBox_wrapper.height() / 2 + "px",
                });
            } else {
                layerBox_wrapper.css({
                    "top": "10px",
                    "margin-top": "0px"
                });
            }
        }
    };
    return b;
});