require(['jquery', 'lazyload', 'jcarousellite'], function ($, lazyload, jcarousellite) {
    var app = {
        init: function () {
            //轮播图片加载
            $(".banner_image").attr("src", function () {
                return $(this).attr("data-original");
            });
            //banner轮播
            $(".banner_box").jCarouselLite({
                btnNext: ".b_next",
                btnPrev: ".b_prev",
                vertical: false,
                speed: 400,
                visible: 1,
                auto: 10000
            }).addClass('is_slide');
            //图片延迟加载
            $(".lazyload").lazyload({
                threshold: -100,
                effect : "fadeIn"  
            });
        }
    };
    $(function () {
        app.init();
    });
});