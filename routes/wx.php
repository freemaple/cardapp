<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['namespace' => 'Wx', 'prefix' => 'wx'], function()
{

	Route::any('/buttons', ['as'=> 'wx_buttons','uses' => 'WechatController@buttons']);

    Route::any('/serve', ['as'=> 'wx_home','uses' => 'WechatController@serve']);

    Route::any('/pay', ['as'=> 'wx_pay','uses' => 'WechatController@pay']);

    Route::any('/token', ['as'=> 'wx_token','uses' => 'WechatController@token']);

});