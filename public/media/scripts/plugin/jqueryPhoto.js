$(document).ready(function(){function e(e){return document.getElementById(e)}function t(e,t){return e.currentStyle?e.currentStyle[t]:getComputedStyle(e,!1)[t]}function n(e,n){e.timer&&clearInterval(e.timer),e.timer=setInterval(function(){for(var o in n){var l=parseInt(t(e,o));l=l?l:0;var r=(n[o]-l)/5;r=r>0?Math.ceil(r):Math.floor(r),e.style[o]=l+r+"px",l==n[o]&&clearInterval(e.timer)}},30)}function o(){n(y,{left:-x*h}),x<B?n(d,{left:0}):x+B<=g?n(d,{left:-(x-B+1)*p}):n(d,{left:-(g-I)*p});for(var e=0;e<g;e++)f[e].className="",e==x&&(f[e].className="on")}function l(){x++,x=x==g?0:x,o()}var r=e("picBox"),i=e("listBox"),a=e("prev"),c=e("next"),u=e("prevTop"),s=e("nextTop"),m=r.getElementsByTagName("li"),f=i.getElementsByTagName("li"),v=m.length,g=f.length,y=r.getElementsByTagName("ul")[0],d=i.getElementsByTagName("ul")[0],h=m[0].offsetWidth,p=f[0].offsetWidth;y.style.width=h*v+"px",d.style.width=p*g+"px";var x=0,I=5,B=Math.ceil(I/2);s.onclick=c.onclick=function(){x++,x=x==g?0:x,o()},a.onmouseover=c.onmouseover=u.onmouseover=s.onmouseover=function(){clearInterval(N)},a.onmouseout=c.onmouseout=u.onmouseout=s.onmouseout=function(){N=setInterval(l,4e3)},u.onclick=a.onclick=function(){x--,x=x==-1?g-1:x,o()};var N=null;N=setInterval(l,4e3);for(var T=0;T<g;T++)f[T].index=T,f[T].onclick=function(){x=this.index,o()}});