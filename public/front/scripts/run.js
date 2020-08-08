//站点设置
var siteConfig = {};
var runApp = {
    init: function(){
        //版本号
        var media_version_obj = document.getElementById('media_version');
        var media_version = media_version_obj ? media_version_obj.value : '';
        siteConfig.version = media_version;
        //静态资源地址
        var static_path_obj = document.getElementById('static_path');
        var static_path = static_path_obj ? static_path_obj.value : '';
        siteConfig.staticPath = static_path;
        //require配置
        var r_config = {
            baseUrl: static_path  + '/media/front/scripts/',
            urlArgs: media_version,
            paths: {
                'jquery': 'plugin/jquery.min',
                'validate': 'plugin/jquery.validate.min',
                'jcarousellite': 'plugin/jcarousellite',
                'lazyload': 'plugin/jquery.lazyload.min',
                'wangEditor': 'wangEditor/js/wangEditor.min',
                'moment': 'daterangepicker/moment.min',
                'daterangepicker': 'daterangepicker/jquery.daterangepicker.min',
                'base': 'module/base',
                'loginReg': 'module/loginReg',
                'search': 'module/search',
                'mylayer': 'module/mylayer',
                'AsyncForm': 'module/asyncForm',
                'ajaxForm': 'plugin/jquery.form.min',
                'tree': 'plugin/tree.jquery'
            },
            shim: {
                'lazyload': {
                    exports: 'lazyload',
                    deps: ['jquery']
                },
                'validate': {
                    exports: 'validate',
                    deps: ['jquery']
                },
                'wangEditor': {
                    exports: 'wangEditor',
                    deps: ['jquery']
                },
                'tree': {
                    exports: 'tree',
                    deps: ['jquery']
                }
            }
        };
        require.config(r_config);
    }
}
runApp.init();
//多语言配置
var lanConfig = {};
//验证提示多语言设置
lanConfig.validatorMessage = function(){
    $.extend(jQuery.validator.messages, {
        required: "这是必填字段",
        remote: "请修正该字段",
        email: "请输入正确格式的电子邮件",
        url: "请输入合法的网址",
        date: "请输入合法的日期",
        dateISO: "请输入合法的日期 (ISO).",
        number: "请输入合法的数字",
        digits: "只能输入整数",
        creditcard: "请输入合法的信用卡号",
        equalTo: "请再次输入相同的值",
        accept: "请输入拥有合法后缀名的字符串",
        maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
        minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
        rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
        range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
        max: jQuery.validator.format("请输入一个最大为{0} 的值"),
        min: jQuery.validator.format("请输入一个最小为{0} 的值")
    });
};
require(['jquery', 'base', 'loginReg'], function ($, md_base, md_loginReg) {
    var app = {};
    //公共事件
    app.init = function(){
        var self = this;
        //图片延迟加载
        if($(".lazyload").size() > 0){
            require(['lazyload'], function(lazyload){
                //图片延迟加载
                $(".lazyload").lazyload({
                    threshold : 200
                });
            })
        }
        //滚动到顶部
        if($(".btn_top").size() == 0){
            var html = '<div class="btn_top"><span class="to_top"></span></div>';
            $("body").append(html);
            //滚动到顶部
            $(".to_top").on("click", function () {
                $('html,body').animate({
                    scrollTop: '0px'
                }, 800);
            });
        }
        //轮播图片加载
        $(".banner_image").attr("src", function () {
            return $(this).attr("data-original");
        });
        //轮播
        if($(".banner_box").size() > 0) {
            //图片延迟加载
            $(".banner_box .lazyload").attr("src", function () {
                return $(this).attr("data-original");
            });
            require(['jcarousellite'], function () {
                //banner轮播
                $(".banner_box").jCarouselLite({
                    btnNext: ".b_next",
                    btnPrev: ".b_prev",
                    vertical: false,
                    speed: 400,
                    visible: 1,
                    auto: 10000
                }).addClass('is_slide');
            });
        }
        
        //滚动显示向上滚动按钮
        window.onscroll = function () {
            if($(".btn_top").size() > 0){
                if ($(window).scrollTop() > 400) {
                    //显示
                    $(".btn_top").show();
                } else {
                    //隐藏
                    $(".btn_top").hide();
                }
            }
        };
        //处理浏览器回退刷新
        var site_url = window.location.href;
        $(window).on('popstate', function (e) {
            if (window.location.href != site_url) {
                window.location.reload();
            }
        });
        //编辑框获取焦点
        $(".form_control").on("focus", function () {
            $(this).closest('.form_group').addClass('focus');
        });
        //编辑框失去焦点
        $(".form_control").on("blur", function () {
            $(this).closest('.form_group').removeClass('focus');
        });
        //页签切换
        $(".box_tab").on("click" ,function(event){
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            //判断是否页签点击
            if (elem.hasClass("tab_item")) {
                var tab_content = $(this).find(".tab_content>div");
                var index = elem.index();
                elem.addClass('current').siblings('.tab_item').removeClass('current');
                tab_content.eq(index).addClass('current').siblings('div').removeClass('current');
            }
        });
        //提示框的关闭事件
        $(".msg_alert_close").on("click", function(){
            $(this).parent().removeClass('show');
        });
        var current_search_keyword = $("#currentkeyword");
        //搜索框内容赋值
        if(current_search_keyword.size() > 0 && current_search_keyword.attr('data-type') == "search"){
            $(".search_area").val($.trim(current_search_keyword.val()));
        }
        //表单编辑框回车不自动提交表单
        $(".un_form_submit input[type='text']").on("keydown", function(){
            var elem = $(this);
            var e = event || window.event;    
            var code = e.keyCode || e.which || e.charCode;  
            if(code == 13){
                return false;
            }
        });
        //免费预约表单
        $(".free_reserve_form").validate(this.freeReserveValid);
        //登录表单事件
        $(".free_reserve_form").on("click", function (event) {
            var form = $(this);
            var e = event || window.event;
            var elem = $(e.target || e.srcElement);
            //判断是点击登录提交按钮
            if (elem.hasClass("submit_reserve")) {
                self.doFreeReserve(form, elem);
            }
        });
        //编辑器功能
        $.descriptionEditor = function(wangEditor, id){
            wangEditor.config.printLog = false;
            var ceditor = new wangEditor(id);
            // 自定义菜单
            ceditor.config.menus = ['image', 'link','unlink','bold','fontsize','forecolor','bgcolor', 'alignleft','aligncenter','alignright','undo','redo'];
            // 使用英语
            ceditor.config.lang = wangEditor.langs['en'];
            // 颜色
            ceditor.config.colors = {'#880000': 'Dark Red','#800080': 'Purple','#ff0000': 'Red','#ff00ff': 'Fresh pink','#000080': 'Navy Blue','#0000ff': 'Blue','#00ffff': 'Lake Blue','#008080': 'Blue-Green','#008000': 'Green','#808000': 'Olive','#00ff00': 'Light Green','#ffcc00': 'Orange','#808080': 'Gray','#c0c0c0': 'Silver','#000000': 'Black','#ffffff': 'White'};
            ceditor.create();
            return ceditor;
        };
        if(is_mobile){
            var slide_number = 2;
        } else {
            var slide_number = 4;
        }
        //轮播
        if($(".recommend_box").size() > 0) {
            //图片延迟加载
            $(".recommend_box .lazyload").attr("src", function () {
                return $(this).attr("data-original");
            });
            require(['jcarousellite'], function () {
                var size = $(".recommend_box li").size();
                if(size >= slide_number){
                    $(".recommend_box").jCarouselLite({
                        btnNext: ".recommend_next",
                        btnPrev: ".recommend_prev",
                        vertical: false,
                        speed: 400,
                        visible: size <slide_number ? size : slide_number,
                        scroll: size <slide_number ? size : slide_number,
                    });
                    $(".recommend_box").addClass('is_slide');
                }
            });
        }
        //轮播
        if($(".recommend_like_box").size() > 0) {
            //图片延迟加载
            $(".recommend_like_box .lazyload").attr("src", function () {
                return $(this).attr("data-original");
            });
            require(['jcarousellite'], function () {
                var size = $(".recommend_like_box li").size();
                if(size >= slide_number){
                     $(".recommend_like_box").jCarouselLite({
                        btnNext: ".like_next",
                        btnPrev: ".like_prev",
                        vertical: false,
                        speed: 400,
                        visible: size <slide_number ? size : slide_number,
                        scroll: size <slide_number ? size : slide_number,
                    });
                    $(".recommend_like_box").addClass('is_slide');
                }
            });
        }
        //轮播
        if($(".new_slide_box").size() > 0) {
            //图片延迟加载
            $(".new_slide_box .lazyload").attr("src", function () {
                return $(this).attr("data-original");
            });
            require(['jcarousellite'], function () {
                var size = $(".new_slide_box li").size();
                if(size >= slide_number){
                    $(".new_slide_box").jCarouselLite({
                        btnNext: ".new_next",
                        btnPrev: ".new_prev",
                        vertical: false,
                        speed: 400,
                        visible: size <slide_number ? size : slide_number,
                        scroll: size <slide_number ? size : slide_number,
                    });
                    $(".new_slide_box").addClass('is_slide');
                }
            });
        }
        //轮播
        if($(".recommend_organization_box").size() > 0) {
            //图片延迟加载
            $(".recommend_organization_box .lazyload").attr("src", function () {
                return $(this).attr("data-original");
            });
            require(['jcarousellite'], function () {
                var size = $(".recommend_organization_box li").size();
                if(size >= slide_number){
                    $(".recommend_organization_box").jCarouselLite({
                        btnNext: ".new_next",
                        btnPrev: ".new_prev",
                        vertical: false,
                        speed: 400,
                        visible: size <slide_number ? size : slide_number,
                        scroll: size <slide_number ? size : slide_number,
                    });
                    $(".recommend_organization_box").addClass('is_slide');
                }
            });
        }
        $(".course_item_wish").on("click", function(){
            var elem = $(this);
            var course_id = elem.attr("data-id");
            var data = {'course_id': course_id};
            $.postForm(elem, '/api/course/wish', data, function(result){
                if(result.code == '2xf'){
                    md_loginReg.showLogin();
                } else {
                    if(result.code == '200'){
                        if(result.is_wish == '1'){
                            elem.find(".icon").addClass('is_wish');
                        } else {
                            elem.find(".icon").removeClass('is_wish');
                        }
                        elem.find(".wish_number").text(result.wish_number);
                    } else if(result.message){
                        mylayer.showMessage("error", result.message);
                    }
                }
            });
        });
        $(".organization_item_wish").on("click", function(){
            var elem = $(this);
            var organization_id = elem.attr("data-id");
            var data = {'organization_id': organization_id};
            $.postForm(elem, '/api/organization/wish', data, function(result){
                if(result.code == '2xf'){
                    md_loginReg.showLogin();
                } else {
                    if(result.code == '200'){
                        if(result.is_wish == '1'){
                            elem.find(".icon").addClass('is_wish');
                        } else {
                            elem.find(".icon").removeClass('is_wish');
                        }
                        elem.find(".wish_number").text(result.wish_number);
                    } else if(result.message){
                        mylayer.showMessage("error", result.message);
                    }
                }
            });
        });
        //侧边栏遮盖层
        $('.mask_layer').click(function(){
            $(this).hide();
            $(".aside").removeClass('push');
        });
        //显示侧边栏
        $("#menu").click(function(){
            $('.mask_layer').show();
            $(".aside").addClass('push');
        });
        //单项变色
        var current_doc_menu = $(".doc_menu").attr("current_menu");
        $('.doc_menu .menu[data-url="'+ current_doc_menu +'"]').addClass('current');
        $(".site_search_select").on("change", function(){
            var option = $(this).val();
            if(option == 'course'){
                $('.search_area').attr('placeholder', '输入想要搜索的课程');
                $(".site_search_form").attr('action', '/course');
            } else if(option == 'org'){
                $('.search_area').attr('placeholder', '输入想要搜索的机构');
                $(".site_search_form").attr('action', '/organization');
            }
        });
        $(".course_list_sort").on("change", function(){
            var url = window.location.search;
            var sort = $(this).val();
            if(sort != null){
                url = $.changeURLArg(url, "sort", sort);
            } else {
                url = $.delParam(url, "sort")
            }
            window.location.href = window.location.pathname + url + window.location.hash;
        });
        $(".course_special_check").on("click", function(){
            var elem = $(this);
            var url = window.location.search;
            if(elem.is(":checked")){
                url = $.changeURLArg(url, "special", '1');
            } else {
                url = $.delParam(url, "special")
            }
            window.location.href = window.location.pathname + url + window.location.hash;
        });
        $(".course_self_check").on("click", function(){
            var elem = $(this);
            var url = window.location.search;
            if(elem.is(":checked")){
                url = $.changeURLArg(url, "self", '1');
            } else {
                url = $.delParam(url, "self")
            }
            window.location.href = window.location.pathname + url + window.location.hash;
        });
    };
    if(typeof app.init == 'function') {
        $(function () {
            app.init();
        });
    }
}); 

