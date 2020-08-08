<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', isset($title) ? $title : '')-人人有赏</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="description" content="@yield('description', isset($description) ? $description : '人人有赏个人名片网页 自媒体 新零售 让创业更简单！')-人人有赏" />
    <meta name="keywords" content="@yield('keywords', isset($keywords) ? $keywords : '人人有赏个人名片网页  自媒体 新零售 分享 创富 融合 感恩')" /> 
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="full-screen" content="yes" />
    <meta name="x5-fullscreen" content="true" />
    <meta name="format-detection" content="telephone=no" />
    @yield('meta')
    <link href="{{ asset('apple-touch-icon.png?v=1') }}" rel="apple-touch-icon" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <!-- Styles -->
    <link type="text/css" href="https://at.alicdn.com/t/font_934736_762k9jl61nr.css" rel="stylesheet" />
    <link href="{{ Helper::asset_url('/media/styles/app.css') }}" rel="stylesheet">
    <link href="{{ Helper::asset_url('/media/scripts/wangEditor/css/wangEditor.min.css') }}" rel="stylesheet">
    <link href="{{ Helper::asset_url('/media/styles/swiper.min.css') }}" rel="stylesheet">
    <!-- Styles -->
    <link rel="manifest" href="/manifest.json">
    @yield('styles')
    <script type="text/javascript">
        (function (n, e) {
            var t = n.documentElement, i = "orientationchange" in window ? "orientationchange" : "resize", d = function () {
                var n = t.clientWidth;
                n = n > 414 ? 414 : n;
                n && (t.style.fontSize = n / 7.5 + "px")
            };
            n.addEventListener && (e.addEventListener(i, d, !1), n.addEventListener("DOMContentLoaded", d, !1))
        
        })(document, window);
        var siteConfig = {
            'asset_path': "{{ $static_path }}",
            'asset_version': "{{ $version or '' }}",
        };
        is_qq = false;
        var u = navigator.userAgent;
        if(navigator && navigator.userAgent){
            var ua = navigator.userAgent.toLowerCase(); 
            if(ua.match(/QQ/i) == "qq"){
                is_qq = true;
            }
        }
        var is_weixin = {{ Helper::isWeixin() ? 1 : 0 }};
        var isIphone = {{ Helper::isIphone() ? 1 : 0 }};
        var isSafari = {{ Helper::isSafari() ? 1 : 0 }};
        @if(!empty($site_config['baidu_key']))
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "https://hm.baidu.com/hm.js?{{ config('site.baidu_key') }}";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();
        @endif
    </script>
    <script type="text/javascript">
        window.onerror = function(errorMessage, scriptURI, lineNumber,columnNumber, errorObj) {
            try{
                var data = {
                    'href': window.location.href,
                    'time': new Date(),
                    'errorMessage': errorMessage,
                    'scriptURI': scriptURI,
                    'lineNumber': lineNumber,
                    'columnNumber': columnNumber
                };
                var query = [];
                for(var key in data){
                    query.push(key +"=" + data[key]);
                }
                query = query.join('&');
                if(_hmt){
                    _hmt.push(['_trackEvent', 'jserror', 'error', query]);
                }
            } catch(e){}
        }
    </script>
</head>
<body>
    <div id="wrap" class="wrap-container"> 
        @yield('header', view('template.header'))
        <div class="wrap-content">
            @if(!empty(session('message')))
            <div class="site_msg_art msg_alert error show" style="position: fixed;top:50%;width: 100%;transform: translateY(-50%);max-width: 640px;z-index: 2">
                <a href="javascript:void(0)" class="msg_alert_close">×</a>
                <div class="msg_alert_content">{!! session('message') !!}</div>
            </div>
            @endif
            @yield('content')
            @yield('copyright')
        </div>
        
        @yield('footer', view('template.footer'))
        <span id="sf" data-sf="{{ Helper::encryStr(csrf_token()) }}"></span>
    </div>
    <input type="hidden" id="is_login_flag" value="{{ !empty(Auth::user()) ? 1 : 0 }}" />

    @include('template.share', ['share_data' => isset($share_data) ? $share_data : []])
    
    @if(Helper::isWeixin())
    <script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
    @else
    <script src="{{ Helper::asset_url('/media/scripts/plugin/plusShare.js') }}" type="text/javascript" charset="utf-8"></script>  
    @endif
    <!-- Analytics -->
    <script src="{{ Helper::asset_url('/media/scripts/plugin/require.js') }}"></script>
    <script src="{{ Helper::asset_url('/media/scripts/view/common.js') }}"></script>
    <!-- Scripts -->

    @yield('scripts')
    <script type="text/javascript">
        // 检测浏览器是否支持SW
        if(navigator.serviceWorker != null){
            navigator.serviceWorker.register('/sw.js')
            .then(function(registartion){
                //console.log('支持sw:', registartion.scope)
            })
        }
    </script>
    @if(!Helper::isApp())
    <div style="position: fixed;right: 5px;bottom: 70px;z-index: 80px" class="download-app-box">
        @if(Helper::isWeixin())
        <a href="javascript:void(0)" class="download_app" id="download_app"><span  style="padding: 4px;border-radius: 25px;-moz-border-radius: 25px;-webkit-border-radius: 25px;background-color: #fe5430;color: #fff;opacity: 0.7"><span class="t_shadow">APP 下载>></span></span></a>
        @elseif(Helper::isIphone())
        <a href="javascript:void(0)" class="download_app" id="download_app"><span  style="padding: 4px;border-radius: 25px;-moz-border-radius: 25px;-webkit-border-radius: 25px;background-color: #fe5430;color: #fff;opacity: 0.7"><span class="t_shadow">APP 下载>></span></span></a>
        @else
        <a href="{{ Helper::asset_url('/app/renrenyoushang.apk') }}" id="download_app"><span  style="padding: 4px;border-radius: 25px;-moz-border-radius: 25px;-webkit-border-radius: 25px;background-color: #fe5430;color: #fff;opacity: 0.7"><span class="t_shadow">APP 下载>></span></span></a>
        @endif
    </div>
    @endif
    <script type="text/javascript">
        if ('standalone' in navigator && navigator.standalone) {
           document.getElementById('download_app').style.display = 'none';
        }
    </script>
</body>
</html>
