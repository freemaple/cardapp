require(['jquery', 'loginReg', 'lazyload', 'mylayer'], function ($,  md_loginReg, lazyload, mylayer){
    var app = {};
    //页面初始化事件
    app.init = function(){
        var _this = this;
        this.current_course_id = $("#current_course").attr("data-course-id");
        this.buyEvent();
        //收藏
        $(".course_wish").on("click", function(){
            var elem = $(this);
            var data = {'course_id': _this.current_course_id};
            $.postForm(elem, '/api/course/wish', data, function(result){
                if(result.code == '2xf'){
                    md_loginReg.showLogin();
                    md_loginReg.fevent = function(){
                        $(elem)[0].click();
                    }
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
    };
    //购买相关事件
    app.buyEvent = function(){
        var _this = this;
        //弹出购买
        $(".buy_it_now").on("click", function(){
            var elem = $(this);
            var type = elem.attr('data-type');
            var class_number = $.trim($(".course_number_input").val());
            var data = {'course_id': _this.current_course_id, 'class_number': class_number, 'type': type};
            $.postForm(elem, '/api/basket/course/add', data, function(result){
                if(result.code == '2xf'){
                    md_loginReg.showLogin();
                    md_loginReg.fevent = function(){
                        $(elem)[0].click();
                    }
                } else {
                    if(result.code == '200'){
                        window.location.href = '/checkout/' + result.basket_no;
                    } else if(result.message){
                        mylayer.showMessage("error", result.message);
                    }
                }
            });
        });
    };
    $(function(){
        app.init();
    });
});
