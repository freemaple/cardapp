require(["zepto","base","mylayer","scrollComponent","lazyload"],function(t,r,a,n,o){var i={};i.init=function(){var r=this,a=t.trim(t("#currentkeyword").val());if(a){var o=t("#currentkeyword").attr("data-type");"search"==o&&require(["search"],function(t){t.addHistory(a)})}var i=t(".js-product-scroll-container");n.init(),n.setScrollItem(i),n.setCallback(function(t,a){r.scrollLoadCallback(t,a)})},i.scrollLoadCallback=function(r,a){a.find(".js-product-list").append(r),t.imgLazyLoad()},"function"==typeof i.init&&t(function(){i.init()})});