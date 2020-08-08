//app运行
var runApp = {};
runApp.init = function(){
    //版本号
    var static_version = siteConfig.asset_version;
    //静态资源地址
    var static_path = siteConfig.asset_path;
    //require配置
    var r_config = {
        baseUrl: static_path  + '/media/scripts/',
        urlArgs: 'v=' + static_version,
        paths: {
            'zepto': 'plugin/zepto.min',
            'zepto_touch': 'plugin/zepto.touch',
            'jquery': 'plugin/jquery.min',
            'validate': 'plugin/validate',
            'echo': 'plugin/echo.min',
            'slider': 'plugin/zepto.slider',
            'mylayer': 'module/mylayer',
            'cache': 'module/cache',
            'base': 'module/base',
            'search': 'module/search',
            'loginReg': 'module/loginReg',
            'asyncForm': 'module/asyncForm',
            'ajaxForm': 'module/ajaxForm',
            'scrollComponent': 'module/scrollComponent',
            'md': 'module/md5.min',
            'wangEditor': 'wangEditor/js/wangEditor.min',
            'distpicker': 'plugin/distpicker.min',
            'social': 'plugin/social-share.min',
            'colpick': 'plugin/colpick',
            'carousel': 'plugin/jquery.carousel',
            'swiper': 'plugin/swiper.min',
            'jcarousellite': 'plugin/jcarousellite',
            'video': 'plugin/video.min',
            'bc': 'plugin/bc',
            'lazyload': 'plugin/lazyload.min',
            'share': 'view/share'
        },
        shim: {
            'zepto_touch': {
                exports: 'zepto_touch',
                deps: ['zepto']
            },
            'slider': {
                exports: 'slider',
                deps: ['zepto']
            },
            'carousel': {
                exports: 'carousel',
                deps: ['jquery']
            },
            'lazyload': {
                exports: 'lazyload',
                deps: ['zepto']
            },
        }
    };
    //加载require配置
    require.config(r_config);
};
runApp.init();
//基础加载
require(['zepto', 'base', 'echo', 'lazyload'], function ($, md_base, echo, Lazyload) {
   var app = {};
    //公共事件
    app.init = function(){
        $.imgLazyLoad();
        var self = this;
        this.commonEvent();
        this.positionEvent();
        this.share();
        $(document).on('click', ".js-copy-link", function(){
            var link = share_data['url'] ? share_data['url'] : window.location.href;
            self.copy(link);
            require(['mylayer'], function(mylayer){
                mylayer.showTip("链接已复制，请粘贴发送给朋友", 4000, 'error');
                var layer = $(".layer-share");
                mylayer.closeLayer(layer.attr('data-id'))
                return false;
            });
        });
        $(".js_to_link_href").on("click", function(event){
            var e = event || window.event;
            event.preventDefault();
            var link = $(this).attr('data-href');
            if(link){
                window.location.href = link;
            }
        });
        $(".js-show-help-qr").on("click", function(){
            $(".self_help_qr").show();
        });
        $(".download_app").on("click", function(){
            if(is_weixin){
                require(['mylayer'], function(mylayer){
                    mylayer.showTip("请点击右上角...，选择在浏览器打开，再点击APP下载！", 4000, 'success');
                });
            } else if(isIphone){
                require(['mylayer'], function(mylayer){
                    if(isSafari){
                        mylayer.showTip("点击底部的分享按钮，再点击添加到主屏幕即可", 4000, 'success');
                    } else {
                        mylayer.showTip("请用Safari浏览器打开，点击底部的分享按钮，再点击添加到主屏幕即可！", 4000, 'success');
                    }
                });
            }
            return true;
        });
        if(navigator.userAgent && navigator.userAgent.indexOf("Html5Plus") > -1){
            document.addEventListener("plusready", function() {
                //二维码图片扫描事件  
                $('.wx_qr_image').longPress(function(e){
                    var src = e[0].src;
                    $.saveUrlFile(src, self.random() + '.jpg', function(){
                        require(['mylayer'], function(mylayer){
                            mylayer.showTip('二维码图片已保存到手机相册，请用微信扫一扫关注！', 3000, 'success');
                        });
                    });
                });
            });
        }
    };
    //生成当前时间戳+随机数
    app. random = function(){
        //当前时间戳
        var timestamp = parseInt(new Date().getTime()/1000);
        var r = Math.floor(Math.random()*100000); 
        return timestamp + "" + r;  
    };
    app.copy = function(text){
        var oInput = document.createElement('input');
        oInput.value = text;
        document.body.appendChild(oInput);
        oInput.select(); // 选择对象
        document.execCommand("Copy"); // 执行浏览器复制命令
        oInput.className = 'oInput';
        oInput.style.display='none';
        document.body.removeChild(oInput);
    };
    app.plusEvent = function(){
         //处理Android回退事件
        document.addEventListener('plusready',()=> {
            const plus = window.plus || '';
            var firstBack = 0;
            plus && plus.key.addEventListener('backbutton', (e)=> {
                const mui = window.mui || '' ;
                var currentWebview = plus.webview.currentWebview();
                var now = Date.now || function () {
                    return new Date().getTime();
                };
                currentWebview.canBack(function (evt) {
                    if (evt.canBack) {
                        //有回退 返回上一个页面
                        currentWebview.back();
                    } else {
                        if (!firstBack) {
                            //第一次回退
                            firstBack = now();
                            plus.nativeUI.toast('再按一次退出app');
                            setTimeout(function () {
                                firstBack = 0;
                            }, 2000);
                        } else if (now() - firstBack < 2000) {
                            //2秒内连续两次退出
                            plus.runtime.quit();
                        }
                    }
                })
             //停止  不弹出再按一次退出应用
            }, false);
        });
    }
    //公共事件
    app.commonEvent = function(){
        var self = this;
        echo.init();
        //this.plusEvent();
        $(".lazy img").on("load", function(){
            $(this).closest('.lazy').removeClass('lazy');
        });
        //图片延迟加载
        $("img").each(function(){
            if($(this).attr("data-src")){
                $(this).attr("src", $(this).attr("data-src"))
            }
        });
        require(['swiper'], function(Swiper){
            var swiper = new Swiper('.swiper-container', {
                autoplay: true,
                loop: true, // 循环模式选项
            });
        });
        //banner轮播
        if($(".js-site-slider").size() > 0){
            require(['slider', 'zepto_touch'], function(){
                $(".js-site-slider").slider({direction: "left"});
            });
        }
        //滚动到顶部
        if($(".js-btn-scroll-top").size() == 0){
            var html = '<div class="btn-scroll-top js-btn-scroll-top"><span class="btn-scroll-to-top js-scroll-to-top">↑</span></div>';
            $("body").append(html);
            //滚动到顶部
            $(".js-scroll-to-top").on("click", function () {
                var element = (document.body && document.body.scrollTop) ? document.body: document.documentElement;
                $.animateScroll(element, 0, 800);
            });
        }
        //滚动显示向上滚动按钮
        window.onscroll = function () {
            var top = $(window).scrollTop()
            if($(".js-btn-scroll-top").size() > 0){
                if (top > 400) {
                    //显示
                    $(".js-btn-scroll-top").show();
                } else {
                    //隐藏
                    $(".js-btn-scroll-top").hide();
                }
            }
        };
        //处理浏览器回退刷新
        var site_url = window.location.href;
        /*$(window).on('popstate', function (e) {
            if (window.location.href != site_url) {
                window.location.reload();
            }
        });*/
        //弹出搜索
        $(".js-show-search").on("click", function(e){
            var content = $(".site-search-panel").html();
            require(['mylayer', 'search'], function(mylayer, md_search){
                mylayer.showLayerTop(content);
                md_search.init();
            });
            e.preventDefault();
        });
        //底部菜单样式处理
        var location = window.location.href;
        var pathname = window.location.pathname;
        if(pathname == '/'){
            $(".foot-home-nav").addClass('current');
        }
        $(".foot-nav-info a").each(function(){
            var href = $(this).attr('href');
            if(location == href){
                $(this).parent().addClass('current');
            }
        });
        //显示登录
        $(".js-show-login").on("click", function(){
            require(['loginReg'], function(md_loginReg){
                md_loginReg.is_redirect_default = true;
                md_loginReg.showLogin(function(){
                }, 'login-panel');
            });
        });
        //显示注册
        $(".js-show-sign").on("click", function(){
            require(['loginReg'], function(md_loginReg){
                md_loginReg.is_redirect_default = true;
                md_loginReg.showLogin(function(){
                }, 'reg-panel');
            });
        });
        //登录注册事件
        if($(".js-entry-box").size() > 0){
            require(['loginReg'], function(md_loginReg){
                md_loginReg.init();
            });
        }
        //返回
        $(".js-link-back").on("click", function(){
            var referrer = document.referrer;
            var hostname = window.location.hostname;
            if(referrer == '' || referrer == window.location.href || referrer.indexOf(hostname) == -1){
                window.location.href = '/';
            } else {
                window.history.go(-1);
            }
            
        });
        $(".menu-box-list").each(function(){
            //设置滚动位置
            var cate_nav  = $(this);
            var cate_item = cate_nav.find('.menu-box-item.current');
            var left = cate_item.offset().left;
            var parent_left = cate_nav.offset().left;
            var width = cate_nav.width();
            var scrollLeft = cate_nav.scrollLeft();
            cate_nav.scrollLeft(scrollLeft + left - parent_left - width / 2);
        });
        $(".site-cate-nav").each(function(){
            //设置滚动位置
            var cate_nav  = $(this);
            var cate_item = cate_nav.find('.cate-nav-item.current');
            if(cate_item.size() > 0){
                var left = cate_item.offset().left;
                var parent_left = cate_nav.offset().left;
                var width = cate_nav.width();
                var scrollLeft = cate_nav.scrollLeft();
                cate_nav.scrollLeft(scrollLeft + left - parent_left - width / 2);
            }
        });
        //页签切换
        $(document).on("click", ".tab-item", function(event){
            var elem = $(this);
            var tab_content = $(this).closest(".box-tab").find('.tab-content>div');
            var index = elem.index();
            elem.addClass('current').siblings('.tab-item').removeClass('current');
            tab_content.eq(index).addClass('current').siblings('div').removeClass('current');
        });
        var islogin = $("#is_login_flag").val();
        if(islogin == '1' && $(".message_number").size() > 0){
            $.ajaxPost('/api/message/noReadNumber', {}, function(result){
                if(result.code == 'Success'){
                    if(result.data.count > 0){
                        $(".message_number").text(result.data.count).show();
                    }
                    if(result.data.store_shipping_order_count > 0){
                        $(".store_order_number").text(result.data.store_shipping_order_count).show();
                    }
                }
            });
        }
        //提示框的关闭事件
        $(".msg_alert_close").on("click", function(){
            $(this).parent().removeClass('show');
        });
        $(document).on("click", ".js-share-link", function(event){
            var is_q_share = false;
            if(is_qq || is_weixin){
                is_q_share = true;
            }
            if(is_q_share){
                require(['mylayer'], function(mylayer){
                    mylayer.showTip("点击右上角分享 . . . 通过发送给朋友！", 4000, 'error');
                    return false;
                });
                return false;
            }
            var elem = $(this);
            var data = share_data;
            require(['share'], function(md_share){
                md_share.init(share_data);
            });
        });
    };
    app.positionEvent = function(){
        var self = this;
        $(document).on("change", ".province_select", function(event){
            var position_box = $(this).closest('.js-position-select-box');
            var province_id = $(this).val();
            position_box.find(".city_select").html('');
            position_box.find(".county_select").html('');
            position_box.find(".town_select").html('');
            position_box.find(".village_select").html('');
            $.ajaxGet('/api/position/getCity', {'province_id': province_id}, function(result){
                if(result.data){
                    var options = '<option value="">请选择</option>';
                    $.each(result.data, function(key, item){
                        options += '<option value="' + item['city_id'] + '">' + item['city_name'] +'</option>';
                    });
                    position_box.find(".city_select").html(options);
                    position_box.find(".county_select").html('');
                    position_box.find(".town_select").html('');
                    position_box.find(".village_select").html('');
                }
            });
        });
        $(document).on("change", ".city_select", function(event){
            var position_box = $(this).closest('.js-position-select-box');
            var city_id = $(this).val();
            position_box.find(".county_select").html('');
            position_box.find(".town_select").html('');
            position_box.find(".village_select").html('');
            $.ajaxGet('/api/position/getCounty', {'city_id': city_id}, function(result){
                if(result.data){
                    var options = '<option value="">请选择</option>';
                    $.each(result.data, function(key, item){
                        options += '<option value="' + item['county_id'] + '">' + item['county_name'] +'</option>';
                    });
                    position_box.find(".county_select").html(options);
                    position_box.find(".town_select").html('');
                    position_box.find(".village_select").html('');
                }
            });
        });
        $(document).on("change", ".county_select", function(event){
            var position_box = $(this).closest('.js-position-select-box');
            var county_id = $(this).val();
            position_box.find(".town_select").html('');
            position_box.find(".village_select").html('');
            $.ajaxGet('/api/position/getTown', {'county_id': county_id}, function(result){
                if(result.data){
                    var options = '<option value="">请选择</option>';
                    $.each(result.data, function(key, item){
                        options += '<option value="' + item['town_id'] + '">' + item['town_name'] +'</option>';
                    });
                    position_box.find(".town_select").html(options);
                }
            });
        });
        $(document).on("change", ".town_select", function(event){
            var position_box = $(this).closest('.js-position-select-box');
            var town_id = $(this).val();
            position_box.find(".village_select").html('');
            $.ajaxGet('/api/position/getVillage', {'town_id': town_id}, function(result){
                if(result.data){
                    var options = '<option value="">请选择</option>';
                    $.each(result.data, function(key, item){
                        options += '<option value="' + item['village_id'] + '">' + item['village_name'] +'</option>';
                    });
                    position_box.find(".village_select").html(options);
                }
            });
        });
    };
    app.share = function(){
        if(!is_weixin){
            return false;
        }
        var wx_share_data = {
            title: share_data['title'], // 商品名
            link: share_data['url'], // 商品购买地址
            imgUrl: share_data['image'], // 分享的图标
            desc: share_data['content'], // 商品名
            fail: function (res) {
                
            },
            success: function (res) { 
            },
            cancel: function () { 
            }
        }
        var wxShare = function(data){
            wx.config(data);
            wx.ready(function(){
                // 分享给朋友
                wx.updateAppMessageShareData(wx_share_data);
                wx.onMenuShareQQ(wx_share_data);
                // 分享到朋友圈
                wx.updateTimelineShareData(wx_share_data);
                wx.onMenuShareQZone(wx_share_data);
                wx.showOptionMenu();
            });
        }
        //微信分享
        $.ajaxGet('/api/common/wxshare', {"url": window.location.href}, function(result){
            wxShare(result.data);
        });
    }
    if(typeof app.init == 'function') {
        $(function () {
            app.init();
        });
    }
}); 
