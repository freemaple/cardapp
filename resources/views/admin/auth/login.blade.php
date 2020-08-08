<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width" />
        <title>@yield('title')</title>
        @yield('styles')
        @foreach(\App\Assets\Admin::styles() as $style)
        <link type="text/css" href="{{ $style }}" rel="stylesheet" />
        @endforeach
        <!--[if lt IE 8]>
          {!! \App\Assets\Admin::style('styles/ie.css') !!}
        <![endif]-->
    </head>
    <body>
        <div class="panel panel-info site_login_panel">
            <div class="panel-heading">
                <h3 class="panel-title text-center">登录系统</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" id="login_form" name="login_form"  method="post">
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">用户名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="username" value="{{ old('username') }}" name="username" placeholder="请输入用户名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">密码</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="user_pwd" name="pwd" placeholder="请输入密码">
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" id="btn_login" class="btn btn-primary">
                            登录
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
        @foreach(\App\Assets\Admin::scripts() as $script)
        <script type="text/javascript" src="{{ $script }}"></script>
        @endforeach
    </body>
</html>


