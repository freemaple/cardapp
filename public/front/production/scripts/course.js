require(["jquery","loginReg","lazyload","mylayer"],function(e,i,s,o){var n={};n.init=function(){var s=this;this.current_course_id=e("#current_course").attr("data-course-id"),this.buyEvent(),e(".course_wish").on("click",function(){var n=e(this),r={course_id:s.current_course_id};e.postForm(n,"/api/course/wish",r,function(s){"2xf"==s.code?(i.showLogin(),i.fevent=function(){e(n)[0].click()}):"200"==s.code?("1"==s.is_wish?n.find(".icon").addClass("is_wish"):n.find(".icon").removeClass("is_wish"),n.find(".wish_number").text(s.wish_number)):s.message&&o.showMessage("error",s.message)})})},n.buyEvent=function(){var s=this;e(".buy_it_now").on("click",function(){var n=e(this),r=n.attr("data-type"),t=e.trim(e(".course_number_input").val()),c={course_id:s.current_course_id,class_number:t,type:r};e.postForm(n,"/api/basket/course/add",c,function(s){"2xf"==s.code?(i.showLogin(),i.fevent=function(){e(n)[0].click()}):"200"==s.code?window.location.href="/checkout/"+s.basket_no:s.message&&o.showMessage("error",s.message)})})},e(function(){n.init()})});