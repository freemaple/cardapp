<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        if(\App::environment() === 'production') {
            $url->forceSchema('https');
        }
        //
        //密码格式验证 密码必须由字母和数字组成
        Validator::extend('letter', function($attribute, $value, $parameters) {
            return preg_match('/^[%()\[\]!.,，A-Za-z0-9_\s\x{4e00}-\x{9fa5}]+$/u', $value);
        });
        //手机号格式验证
        Validator::extend('phone', function($attribute, $value, $parameters) {
            return preg_match('/^1[0-9][0-9]{9}$/', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
