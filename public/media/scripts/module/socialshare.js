define([],function(){var t={};return t.init=function(t,i,e,r){this.url=t,this.title=i,this.image=e,this.description=r},t.socialurl=function(t){var i=this.url;return i=i.indexOf("?")==-1?i+"?share_source="+t:i+"&share_source="+t},t.fackbook=function(){var t=this.socialurl("fackbook"),i="https://www.facebook.com/sharer/sharer.php?u="+t;return i},t.google=function(){var t=this.socialurl("google"),i="https://plus.google.com/share?url="+t;return i},t.twitter=function(){var t=this.socialurl("twitter"),i="https://twitter.com/intent/tweet?url="+t+"&text="+this.title;return i},t.pinterest=function(){var t=this.socialurl("pinterest"),i="https://www.pinterest.com/pin/create/button/?url="+t+"&media="+this.image;return i},t.vk=function(){var t=this.socialurl("vk"),i="https://vk.com/share.php?url="+t+"&title="+this.title+"&description="+this.title+"&image="+this.image;return i},t.linkedin=function(){var t=this.socialurl("linkedin"),i="https://www.linkedin.com/shareArticle?mini=true&url="+t+"&title="+this.title+"&summary=";return i},t.tumblr=function(){var t=this.socialurl("tumblr"),i="http://www.tumblr.com/share/link?url="+t;return i},t.messenger=function(){var t=this.socialurl("messenger");return"fb-messenger://share?link="+encodeURIComponent(t)},t.open=function(t){if(this[t]&&"function"==typeof this[t]){var i=this[t]();"messenger"==t?window.open(i):window.open(i,"facebookpluswindow","height=550,width=600,left=300")}},t});