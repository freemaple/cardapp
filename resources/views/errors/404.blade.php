<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>page_not_found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="description" content="@yield('description', trans('meta.site.description'))" />
    <meta name="keywords" content="@yield('keywords', trans('meta.site.keywords'))" /> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="full-screen" content="yes" />
    <meta name="x5-fullscreen" content="true" />
    @yield('meta')
    <link href="{{ asset('apple-touch-icon.png') }}" rel="apple-touch-icon" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <!-- Styles -->
    <link href="{{ Helper::asset_url('/media/styles/app.css') }}" rel="stylesheet">
    <!-- Styles -->
    @yield('styles')
</head>
<body>
    <div id="wrap" class="wrap-container"> 
        <div class="wrap-content">
            <div class="no-results">
                <div class="result-img" style="margin-top: 100px">
                    <div class="rote_wrap">
                        <div class="box1 rote_box">1</div>
                        <div class="box2 rote_box">2</div>
                        <div class="box3 rote_box">3</div>
                        <div class="box4 rote_box">4</div>
                        <div class="box5 rote_box">5</div>
                        <div class="box6 rote_box">6</div>
                    </div>
                </div>
                <div class="result-content" style="margin-top: 200px">
                    @if(!empty($message))
                        <p>{{ $message }}</p>
                    @else
                        <p>您的能量太强大了，该页面不存在都被你找到了</p>
                    @endif
                </div>
                <div class="control-group">
                    <a class="btn btn-primary" href="/">浏览其他</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
