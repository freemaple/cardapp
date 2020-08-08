<style type="text/css">
    body {
       background-image: url("{{ Helper::asset_url('/media/images/sign_bg.jpg') }}");
       background-color: #f5f5f5;
       color: #fff;
    }
    .form-group {
        margin-bottom: 10px;
    }
</style>
<div class="entry-box js-entry-box clearfix">
    <div class="entry-box-item entry-login-box">
        <div class="login-panel entry-panel" @if(isset($type) && $type != 'login') style="display: none"  @endif>
            <div class="sign-panel-content">
                <div class="entry-box-logo text-center">
                    <img src="{{ asset('/media/images/logo.png') }}">
                </div>
                <form class="login-form" name="login-form" method="POST" onsubmit="return false" data-action="/api/auth/login">
                    <div class="form-group form-icon-text">
                        <span class="iconfont icon-user"></span>
                        <div>
                            <input type="text" class="form-control" name="phone" placeholder="用户名/手机号码" value="{{ old('phone') }}" />
                        </div>
                    </div>
                    <div class="form-group form-icon-text">
                        <span class="iconfont icon-password"></span>
                        <div>
                            <input type="password" class="form-control" name="password" value="" placeholder="密码"  />
                        </div>
                        <div class="forget-box">
                            <a class="forget-password js-show-entry-forget" href="javascript:void(0)">
                                忘记密码?
                            </a>
                        </div>
                    </div>
                    <div class="form-group form-group-btn">
                        <input type="submit" class="btn btn-primary btn-block" value="登录" data-login-link="{{ $login_link or '' }}" />
                    </div>
                </form>
                <div class="sign-text"> 
                    还不是用户？
                    <a class="text-primary js-show-entry-sign" href="javascript:void(0)">立即注册</a>
                </div>
            </div>
        </div>
        <div class="reg-panel entry-panel" @if(isset($type) && $type != 'register') style="display: none" @endif>
            <div class="sign-panel-content">
                <div class="entry-box-logo text-center" >
                    <img src="{{ asset('/media/images/logo.png') }}">
                </div>
                <form class="reg-form" name="reg-form" method="POST" data-action="/api/auth/register" data-previous="{{ $previous_link or '' }}" onsubmit="return false">
                    <div class="form-group form-icon-text">
                        <div>
                            <span class="iconfont icon-user"></span>
                            <input type="text" required="required" class="form-control" name="user_name" maxlength="20"  placeholder="用户名"  />
                        </div>
                        <div class="tip_info" style="color: #444;font-size: 0.22rem;color: #fe7589;margin-top: 5px"> 
                            用户名：字母和数字,下划线组合
                        </div>
                    </div>
                    <div class="form-group form-icon-text">
                        <div>
                            <span class="iconfont icon-phone"></span>
                            <input type="text" class="form-control phone_input" name="phone" maxlength="20"  placeholder="手机号码"  />
                        </div>
                    </div>
                    <div class="form-group form-icon-text">
                        <span class="iconfont icon-username"></span>
                        <input type="text" class="form-control" name="fullname" maxlength="8"  placeholder="真实姓名"  />
                    </div>
                    <div class="form-group form-icon-text">
                        <span class="iconfont icon-password"></span>
                        <input type="password" class="form-control" name="password" value="" maxlength="50" placeholder="登录密码"  />
                    </div>
                    <div class="form-group form-icon-text" style="display: none">
                        <span class="iconfont icon-transaction" style="color: #888"></span>
                        <input type="password" class="form-control" name="t_password1" value="" maxlength="50" placeholder="交易密码"  />
                    </div>
                    <div class="form-group">
                        <div style="padding-right: 110px;position: relative">
                            <input type="text" class="form-control" name="verification_code" placeholder="验证码" />
                            <a href="javascript:void(0)" class="btn btn-primary js-send-phonecode" data-type="sign_up" style="position: absolute;right: 0px;top: 0px">获取验证码</a>
                        </div>
                        <div class="code_send_tip_info" style="display: none">
                            <span class="code_send_tip text-info"></span>
                            <span>如若未收到，请<span class="verificate_code_time">60</span>秒后再发送</span>
                        </div>
                    </div>
                    @if(!empty($r_user))
                    <div class="form-group">
                       <span style="color: #00f">客服： {{ $r_user_name }}</span>
                    </div>
                    @endif
                    <div class="form-group form-group-btn">
                        <input type="submit" class="btn btn-primary btn-block" value="注册" data-login-link="{{ $login_link or '' }}" />
                    </div>
                    <input type="hidden" name="rid" value="{{ $rid or '' }}" />
                    <input type="hidden" name="s_type" value="{{ $s_type or '' }}" />
                    <div class="form-group">
                        <span class="pub-check">
                            <input type="checkbox" checked="">
                        </span>
                        <span class="flex">注册代表您同意人人有赏 <a class="text-primary" href="/help/terms-conditions">用户服务协议</a></span>
                    </div>
                </form>
                <div class="sign-text"> 
                    已有用户帐号？
                    <a class="transition js-show-entry-login" href="javascript:void(0)">请登录</a> 
                </div>
            </div>
        </div>
        <div class="forget-panel entry-panel" @if(isset($type) && $type != 'forget') style="display: none" @endif>
            <div class="entry-box-logo text-center">
                <img src="{{ asset('/media/images/logo.png') }}">
            </div>
            <div class="sign-panel-content">
                <form class="forget-form" name="forget-form" method="POST" data-action="/api/password/reset" onsubmit="return false">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <input type="text" class="form-control" name="user_name" placeholder="用户名" />
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control phone_input reset_phone" name="phone" placeholder="手机号码" />
                    </div>
                    <div class="form-group">
                        <div style="padding-right: 110px;position: relative">
                            <input type="text" class="form-control" name="code" placeholder="验证码" />
                            <a href="javascript:void(0)" class="btn btn-primary js-send-phonecode" data-type="password" style="position: absolute;right: 0px;top: 0px">获取验证码</a>
                        </div>
                        <div class="code_send_tip_info" style="display: none">
                            <span class="code_send_tip text-info"></span>
                            <span>如若未收到，请<span class="verificate_code_time">60</span>秒后再发送</span>
                        </div>
                    </div>
                    <div class="form-group form-icon-text">
                        <span class="iconfont icon-password"></span>
                        <input type="password" class="form-control" name="new_password" value="" maxlength="50" placeholder="新密码"  />
                    </div>
                    <div class="form-group form-icon-text">
                        <span class="iconfont icon-password"></span>
                        <input type="password" class="form-control" name="new_password_confirmation" value="" maxlength="50" placeholder="新密码确认"  />
                    </div>
                    <div class="form-group">
                        <span class="text-red">江山国库钥匙,请我王务必牢记！</span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-block" value="重置" />
                    </div>
                </form>
                <div class="sign-text"> 
                    已有用户帐号？
                    <a class="transition js-show-entry-login" href="javascript:void(0)">请登录</a> 
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="previous_link" name="previous_link" value="{{ $login_redirect_link or '' }}">
     <input type="hidden" id="register_redirect_link" name="register_redirect_link" value="{{ $register_redirect_link or '' }}">
</div>