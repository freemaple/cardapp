@extends('layouts.app')

@section('header_title')  @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ route('user_index') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">@lang('view.words.reset_password')</div>
    </div>
</div>
@endsection

@section('content')
    <div class="reset-password-box">
        <form method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="form-group-label">@lang('view.words.email')</label>
                <div class="col-md-6">
                    <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
                    @if ($errors->has('email'))
                        <span class="help-block text-red">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="form-group-label">@lang('view.words.password')</label>
                <input id="password" type="password" class="form-control" name="password" maxlength="50" required>
                @if ($errors->has('password'))
                    <span class="help-block text-red">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label for="password-confirm" class="form-group-label">@lang('view.words.confirm_password')</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block text-red">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary btn-block">
                        @lang('view.words.reset_password')
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
