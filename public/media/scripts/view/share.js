define(["zepto"],function(t){var e={init:function(e,n){var i=this;if(t(document).on("click",".wx,.wxline,.QQ,.wb",function(){(uc||qq&&!wx)&&mshare.init(+t(this).data("mshare"))}),i.share_data={title:e.title,link:e.url,imgUrl:e.image,desc:e.content,fail:function(t){},success:function(t){},cancel:function(){}},is_weixin)wx.showOptionMenu();else if(navigator.userAgent&&navigator.userAgent.indexOf("Html5Plus")>-1)window.plusShare({title:e.title,content:e.content,href:e.url,image:e.image,thumbs:[e.image]},function(t){n(t)});else{({title:e.title,desc:e.content,image_url:e.image,share_url:e.url});var a=t("#share-box-template").html();require(["mylayer"],function(t){t.init({content:a,close:!1,class_name:"layer-share layer-bottom bottom-to-top",position:"bottom",callback:function(){}})})}}};return e});