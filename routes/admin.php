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

Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function()
{

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth.admin:admin');
    
    Route::get('/', ['as'=> 'admin_home','uses' => 'HomeController@index']);
    //后台用户管理
    Route::get('auth', ['as'=> 'admin_auth','uses' => 'AuthController@index']);
    Route::post('auth/login', ['as'=> 'admin_auth_login','uses' => 'AuthController@login']);
    Route::get('user', ['as'=> 'admin_user','uses' => 'UserController@index']);
    Route::post('user/remove', ['as'=> 'admin_user_remove','uses' => 'UserController@remove']);
    Route::get('user/add', ['as'=> 'admin_user_add','uses' => 'UserController@add']);
    Route::get('user/edit/{id}',['as'=> 'admin_user_edit','uses' => 'UserController@edit']);
    Route::post('user/save', ['as'=> 'admin_user_save','uses' => 'UserController@save']);
    Route::match(['get', 'post'], 'user/alertpwd', ['as'=> 'admin_user_alertpwd','uses' => 'UserController@alertpwd']);
    Route::get('user/logout', ['as'=> 'admin_user_logout','uses' => 'UserController@logout']);

    //角色管理
    Route::get('role', ['as'=> 'admin_role','uses' => 'RoleController@index']);
    Route::post('role/remove', ['as'=> 'admin_role_remove','uses' => 'RoleController@remove']);
    Route::match(['get', 'post'], 'role/add', ['as'=> 'admin_role_add','uses' => 'RoleController@add']);
    Route::match(['get', 'post'], 'role/edit/{id}',['as'=> 'admin_role_edit','uses' => 'RoleController@edit']);

    //会员
    Route::match(['get', 'post'], 'customer/openvip', ['as'=> 'admin_openvip','uses' => 'CustomerController@openvip']);
    Route::match(['get', 'post'], 'customer/openstore', ['as'=> 'admin_openstore','uses' => 'CustomerController@openStore']);
    Route::match(['get', 'post'], 'customer/index', ['as'=> 'admin_customer','uses' => 'CustomerController@index']);
    Route::match(['get', 'post'], 'customer/statistics', ['as'=> 'admin_customer_score','uses' => 'CustomerController@statistics']);
    Route::match(['get', 'post'], 'customer/integralSend', ['as'=> 'admin_customer_integralsend','uses' => 'CustomerController@userIntegralSend']);
    Route::match(['get', 'post'], 'customer/loadUserIntegral', ['as'=> 'admin_customer_loadIntegral','uses' => 'CustomerController@loadUserIntegral']);
    Route::match(['get', 'post'], 'customer/setUserType', ['as'=> 'admin_customer_setUserType','uses' => 'CustomerController@setUserType']);
    Route::match(['get', 'post'], 'customer/addUserSubIntegral', ['as'=> 'admin_customer_addUserSubIntegral','uses' => 'CustomerController@addUserSubIntegral']);


    Route::match(['get', 'post'], 'customer/level', ['as'=> 'admin_customer_level','uses' => 'CustomerController@level']);
    
    Route::match(['get', 'post'], 'customer/{id}', ['as'=> 'admin_customer_info','uses' => 'CustomerController@info']);
    

    Route::match(['get', 'post'], 'customer/integral/{id}', ['as'=> 'admin_customer_integral','uses' => 'CustomerController@integral']);

    Route::match(['get', 'post'], 'customer/reward/{id}', ['as'=> 'admin_customer_reward','uses' => 'CustomerController@reward']);



    //文章
    Route::match(['get', 'post'], 'post', ['as'=> 'admin_post','uses' => 'PostController@index']);
    Route::match(['get', 'post'], 'post/in_article', ['as'=> 'admin_in_article','uses' => 'PostController@in_article']);
    Route::match(['get', 'post'], 'post/remove', ['as'=> 'admin_post_remove','uses' => 'PostController@remove']);

     //名片
    Route::match(['get', 'post'], 'card', ['as'=> 'admin_card','uses' => 'CardController@index']);
    Route::match(['get', 'post'], 'card/set_syn', ['as'=> 'admin_set_syn','uses' => 'CardController@setSyn']);



    Route::match(['get', 'post'], 'doc', ['as'=> 'admin_doc','uses' => 'DocController@index']);

    Route::match(['get', 'post'], 'doc/add', ['as'=> 'admin_doc_add','uses' => 'DocController@add']);

    Route::match(['get', 'post'], 'doc/edit/{id}', ['as'=> 'admin_doc_edit','uses' => 'DocController@edit']);

    Route::match(['get', 'post'], 'feedback', ['as'=> 'admin_feedback','uses' => 'FeedbackController@index']);
    Route::match(['get', 'post'], 'feedback/hander', ['as'=> 'admin_feedback_hander','uses' => 'FeedbackController@hander']);

     //banner
    Route::match(['get', 'post'], 'banner', ['as'=> 'admin_banner','uses' => 'BannerController@index']);
    Route::match(['get', 'post'], 'banner/add', ['as'=> 'admin_banner_add','uses' => 'BannerController@add']);
    Route::match(['get', 'post'], 'banner/edit/{id}', ['as'=> 'admin_banner_edit','uses' => 'BannerController@edit']);
    Route::match(['get', 'post'], 'banner/remove/{id}', ['as'=> 'admin_banner_remove','uses' => 'BannerController@remove']);

    //验证码
    Route::match(['get', 'post'], 'phonecode', ['as'=> 'admin_phonecode','uses' => 'SiteController@phonecode']);


    //站点配置
    Route::match(['get', 'post'], 'site/config', ['as'=> 'admin_siteconfig','uses' => 'SiteController@siteConfig']);
    Route::match(['get', 'post'], 'site/saveConfig', ['as'=> 'admin_siteSaveConfig','uses' => 'SiteController@saveConfig']);

    //每天红利设置
    Route::match(['get', 'post'], 'site/goldconfig', ['as'=> 'admin_goldconfig','uses' => 'SiteController@goldconfig']);
    Route::match(['get', 'post'], 'site/saveGoldConfig', ['as'=> 'admin_saveGoldConfig','uses' => 'SiteController@saveGoldConfig']);
    

    //通知
    Route::match(['get', 'post'], 'notice', ['as'=> 'admin_notice','uses' => 'SiteController@notice']);

    Route::match(['get', 'post'], 'notice/load', ['as'=> 'admin_load_notice','uses' => 'SiteController@loadNotice']);

    Route::match(['get', 'post'], 'notice/save', ['as'=> 'admin_save_notice','uses' => 'SiteController@saveNotice']);

    //股权
    Route::match(['get', 'post'], 'equity', ['as'=> 'admin_equity','uses' => 'EquityController@index']);
    Route::match(['get', 'post'], 'equity/config', ['as'=> 'admin_equityconfig','uses' => 'EquityController@config']);
    Route::match(['get', 'post'], 'equity/saveConfig', ['as'=> 'admin_equitySaveConfig','uses' => 'EquityController@saveConfig']);
    Route::match(['get', 'post'], 'equity/record', ['as'=> 'admin_equityrecord','uses' => 'EquityController@record']);


    //提现
    Route::match(['get', 'post'], 'payout/apply', ['as'=> 'admin_payout_apply','uses' => 'PayoutController@apply']);

    Route::match(['get', 'post'], 'payout/handerApply', ['as'=> 'admin_payout_handerApply','uses' => 'PayoutController@handerApply']);

    //产品分类管理
    Route::match(['get', 'post'], 'productcategory', ['as'=> 'admin_product_category','uses' => 'ProductCategoryController@index']);
    Route::match(['get', 'post'], 'productcategory/load', ['as'=> 'admin_product_category_load','uses' => 'ProductCategoryController@load']);
    Route::match(['get', 'post'], 'productcategory/save', ['as'=> 'admin_product_category_load','uses' => 'ProductCategoryController@save']);

    //产品管理
    Route::match(['get', 'post'], 'product', ['as'=> 'admin_product','uses' => 'ProductController@index']);
    Route::match(['get', 'post'], 'product/add', ['as'=> 'admin_product_add','uses' => 'ProductController@add']);
    Route::match(['get', 'post'], 'product/edit/{id}', ['as'=> 'admin_product_edit','uses' => 'ProductController@edit']);
    Route::match(['get', 'post'], 'product/{id}/sku', ['as'=> 'admin_product_sku','uses' => 'ProductController@sku']);
    Route::match(['get', 'post'], 'product/{id}/sku/add', ['as'=> 'admin_product_sku_add','uses' => 'ProductController@skuAdd']);
    Route::match(['get', 'post'], 'product/sku/edit/{id}', ['as'=> 'admin_product_sku_edit','uses' => 'ProductController@skuEdit']);
    Route::match(['get', 'post'], 'product/sku/makeimage/{id}', ['as'=> 'admin_product_sku_makeimage','uses' => 'ProductController@makeImage']);
    Route::match(['get', 'post'], 'product/option', ['as'=> 'admin_product_option','uses' => 'ProductController@option']);
    Route::match(['get', 'post'], 'product/shareApply', ['as'=> 'admin_product_shareApply','uses' => 'ProductController@shareApply']);
     Route::match(['get', 'post'], 'product/shareApplyApproval', ['as'=> 'admin_product_shareApplyApproval','uses' => 'ProductController@shareApplyApproval']);


     //产品管理
    Route::match(['get', 'post'], 'activitycategory', ['as'=> 'admin_activitycategory','uses' => 'ActivityController@index']);
    Route::match(['get', 'post'], 'activitycategory/product/{id}', ['as'=> 'admin_activitycategory_product','uses' => 'ActivityController@product']);
    Route::match(['get', 'post'], 'activitycategory/addProduct', ['as'=> 'admin_activitycategory_addProduct','uses' => 'ActivityController@addProduct']);
    Route::match(['get', 'post'], 'activitycategory/removeProduct', ['as'=> 'admin_activitycategory_removeProduct','uses' => 'ActivityController@removeProduct']);



    //礼包
    Route::match(['get', 'post'], 'product/gift/add', ['as'=> 'admin_product_gift_add','uses' => 'ProductController@addgift']);
     Route::match(['get', 'post'], 'product/gift/edit', ['as'=> 'admin_product_gift_edit','uses' => 'ProductController@editgift']);
    Route::match(['get', 'post'], 'product/gift', ['as'=> 'admin_product_gift','uses' => 'ProductController@gift']);



    Route::match(['get', 'post'], 'store', ['as'=> 'admin_store','uses' => 'StoreController@index']);

    Route::match(['get', 'post'], 'store/handerApply', ['as'=> 'admin_store_handerApply','uses' => 'StoreController@handerApply']);


    Route::match(['get', 'post'], 'order/recharge', ['as'=> 'admin_order_recharge','uses' => 'OrderRechargeController@index']);

    Route::match(['get', 'post'], 'order/recharge/pay/{order_id}', ['as'=> 'admin_order_recharge_pay','uses' => 'OrderRechargeController@pay']);


    Route::match(['get', 'post'], 'orders', ['as'=> 'admin_orders','uses' => 'OrderController@index']);

    Route::match(['get', 'post'], 'order/detail/{id}', ['as'=> 'admin_order_detail','uses' => 'OrderController@detail']);

    Route::match(['get', 'post'], 'order/shipOrder', ['as'=> 'admin_order_shipOrder','uses' => 'OrderController@shipOrder']);

    Route::match(['get', 'post'], 'order/refund', ['as'=> 'admin_order_refund','uses' => 'OrderController@refund']);



    //统计
    Route::match(['get', 'post'], 'statistics/index', ['as'=> 'admin_statistics_index','uses' => 'StatisticsController@index']);

    Route::match(['get', 'post'], 'statistics/getUserCount', ['as'=> 'admin_statistics_usercount','uses' => 'StatisticsController@getUserCount']);

    Route::match(['get', 'post'], 'statistics/getStoreCount', ['as'=> 'admin_statistics_rechargecount','uses' => 'StatisticsController@getStoreCount']);

    Route::match(['get', 'post'], 'statistics/getOrderAmount', ['as'=> 'admin_statistics_orderAmount','uses' => 'StatisticsController@getOrderAmount']);

    Route::match(['get', 'post'], 'statistics/getOrderCount', ['as'=> 'admin_statistics_orderCount','uses' => 'StatisticsController@getOrderCount']);

    Route::match(['get', 'post'], 'statistics/getAmount', ['as'=> 'admin_statistics_getAmount','uses' => 'StatisticsController@getAmount']);


    Route::match(['get', 'post'], 'gold/index', ['as'=> 'admin_gold_index','uses' => 'GoldController@index']);
    Route::match(['get', 'post'], 'gold/day', ['as'=> 'admin_gold_day','uses' => 'GoldController@day']);


    Route::group(['namespace' => 'Api', 'prefix' => 'api'], function(){

        Route::post('common/image2base64', ['as' => 'admin_api_image2base64', 'uses' => 'CommonController@image2base64']);

        /*产品属性管理*/
        Route::post('option/add', ['as' => 'admin_api_option_add', 'uses' => 'OptionController@addOption']);
        Route::post('option/edit', ['as' => 'admin_api_option_edit', 'uses' => 'OptionController@editOption']);
        Route::post('option/load', ['as' => 'admin_api_option_load', 'uses' => 'OptionController@loadOption']);

        //产品管理
        Route::post('product/load', ['as' => 'admin_api_product_load', 'uses' => 'ProductController@loadProduct']);
        Route::post('product/upload', ['as' => 'admin_api_image2base64', 'uses' => 'ProductController@uploadProduct']);
        Route::post('product/add', ['as' => 'admin_api_add_product', 'uses' => 'ProductController@addProduct']);
        Route::post('product/update', ['as' => 'admin_api_product_update', 'uses' => 'ProductController@editProduct']);
        Route::post('product/spu/load/selectimage', ['as' => 'admin_api_load_product_spu_image_select', 'uses' => 'ProductController@loadProductSpuImageSelect']);
        Route::post('product/spu/saveimage', ['as' => 'admin_api_product_spu_saveimage', 'uses' => 'ProductController@saveSpuimage']);
        Route::post('product/sku/saveimage', ['as' => 'admin_api_product_sku_saveimage', 'uses' => 'ProductController@saveSkuimage']);
        Route::post('product/sku/edit/load', ['as' => 'admin_api_product_sku_edit_load', 'uses' => 'ProductController@loadSkuEdit']);
        Route::post('product/sku/add', ['as' => 'admin_api_product_sku_add', 'uses' => 'ProductController@addProductSku']);
        Route::post('product/sku/edit', ['as' => 'admin_api_product_sku_edit', 'uses' => 'ProductController@editProductSku']);

        Route::post('product/addProductImage', ['as' => 'admin_api_addProductImage', 'uses' => 'ProductController@addProductImage']);

        Route::match(['get', 'post'], 'product/uploadvideo', ['as'=> 'admin_product_uploadvideo','uses' => 'ProductController@editProductVideo']);
    });
});