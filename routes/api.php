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

Route::group(['namespace' => 'Api', 'prefix' => 'api'], function(){

    /*登录注册验证模块*/
	Route::post('/auth/register', 'AuthController@register');
	Route::post('/auth/login', 'AuthController@login');
	Route::post('/auth/forget_password', 'AuthController@forget_password');
	Route::post('/auth/reset/{id}','AuthController@reset');
	Route::post('/password/reset','AuthController@reset');
	Route::post('/auth/phone/code','AuthController@phoneCode');
	Route::post('/auth/logout','AuthController@logout');
	/*登录注册验证模块结束*/

	/*首页*/
	Route::get('/home', 'SiteController@home');
	Route::get('/homescreen', 'SiteController@homescreen');
	Route::get('/shop', 'SiteController@shop');

	/*文库*/
	Route::get('/article', 'ArticleController@index');

	/*名片微链接*/
	Route::get('/card/microlink', 'CardController@getMicrolink');
	/*名片文章*/
	Route::get('/card/post/{number}',['as'=> 'card_post','uses' => 'CardController@post']);

	/*搜索文章*/
	Route::get('/search/post',['as'=> 'search_post_list','uses' => 'SearchController@getPostList']);

	/*分类文章*/
	Route::get('/category/getPost',['as'=> 'category_getpost','uses' => 'CategoryController@getPost']);

	/*文章所有分类*/
	Route::get('/category/getAllCategory',['as'=> 'category_getAllCategory','uses' => 'CategoryController@getAllCategory']);


	/*分类文章*/
	Route::get('/category/post',['as'=> 'category_post_list','uses' => 'CategoryController@getPostList']);

	/*美文*/
	Route::get('/beauty_post',['as'=> 'beauty_post_list','uses' => 'ArticleController@getBeautyPost']);
	/*美文*/

	//文章转载
	Route::post('/post/reprint','PostController@reprint');
	//文章浏览
	Route::post('post/viewed', ['as'=> 'post_viewed','uses' => 'PostController@view']);

	/*首页产品列表*/
	Route::get('/products', 'SiteController@products');

	Route::get('/product/detail', 'ProductController@detail');

	Route::post('product/viewed', ['as'=> 'product_viewed','uses' => 'ProductController@viewed']);

	/*自营产品列表*/
	Route::get('/selfProducts','SiteController@selfProducts');

	/*商家*/
	Route::get('/merchant/list', 'MerchantController@getList');
	/*商家*/

	//同店铺产品
	Route::post('/product/getStoreProduct','ProductController@getStoreProduct');

	Route::post('/product/getSelfProduct','ProductController@getSelfProduct');

	/*搜索产品列表*/
	Route::get('/search/products',['as'=> 'search_products_list','uses' => 'SearchController@getProductList']);

	/*分类产品*/
	Route::get('/category/products/{id}',['as'=> 'category_products','uses' => 'CategoryController@getProductList']);


	//产品评论
	Route::get('/product/reviewsBox','ProductController@reviewsBox');
	Route::get('/product/reviews','ProductController@reviews');
	Route::post('/product/codeImage','ProductController@productCodeImage');


	//产品浏览记录
	Route::get('/getViewdProducts','SiteController@getViewdProducts');


	/*检查vip订单是否支付*/
	Route::post('/order/checkVipOrderPay', 'OrderRechargeController@checVipkOrderPay');

	/*积分订单是否已支付*/
	Route::post('/order/checkIntegralOrderPay', 'OrderRechargeController@checkIntegralOrderPay');

	/*名片续费是否支付*/
	Route::post('/order/checkCardRenewalOrderPay', 'OrderRechargeController@checkCardRenewalOrderPay');

	/*名片续费*/
	Route::post('/order/checkStoreOrderPay', 'OrderRechargeController@checkStoreOrderPay');

	/*分享*/
	Route::get('/common/wxshare', 'CommonController@wxshare');

	Route::get('/position/getCity','PositionController@getCity');
    Route::get('/position/getCounty','PositionController@getCounty');
    Route::get('/position/getTown','PositionController@getTown');
    Route::get('/position/getVillage','PositionController@getVillage');
    Route::get('/position/getAddress','PositionController@getAddress');

     //地址
	Route::get('/address/region', ['as' => 'user_address_region', 'uses' => 'AddressController@region']);


	Route::get('/account/center','AccountController@center');


	Route::get('/store/view','StoreController@view');

	Route::get('/store/products','StoreController@products');

	Route::post('/message/noReadNumber','MessageController@noReadNumber');


	/*用户中心(需要用户登录验证的路由)*/
	Route::group(['middleware' => ['user_auth']], function(){

		/*产品*/
	    Route::post('/product/wish','ProductController@wish');
	    Route::post('/product/removewish','ProductController@removewish');
	    Route::get('/wish/list','WishController@getList');
	    /*产品*/

	    /*检查vip订单是否支付*/
		Route::post('/checkout/check', 'CheckoutController@check');
		Route::post('/checkout/index', 'CheckoutController@index');

		/*账号设置*/
		Route::get('/account/entry','AccountController@entry');
		
	    Route::post('/account/changeinfo','AccountController@changeInfo');
	    Route::post('/account/changepwd','AccountController@changePwd');
	    Route::post('/account/changeTransactionPwd','AccountController@changeTransactionPwd');
	    Route::post('/account/changeavatar','AccountController@changeavatar');
	    Route::get('/account/info','AccountController@info');
	    Route::get('/account/userinfo','AccountController@userinfo');
	    Route::get('/account/accountinfo','AccountController@accountinfo');
	    Route::post('/account/changeWeixin','AccountController@changeWeixin');
	    Route::post('/message/setRead','MessageController@setMessageAllRead');
	    Route::get('/messages/list','MessageController@messageList');

	    Route::get('/account/orderCount','AccountController@orderCount');

	    Route::get('/account/vipUpgrade','AccountController@vipUpgrade');
	    Route::get('/account/vipUpgradeDetail','AccountController@vipUpgradeDetail');
	    Route::get('/checkout/vipUpgrade','CheckoutController@vipUpgrade');


	    Route::get('/account/shareInfo','AccountController@shareInfo');
	    Route::post('/account/share',['as'=> 'api_account_share','uses' => 'AccountController@share']);
	    Route::post('/share/taskIntegral',['as'=> 'api_shareTaskIntegral','uses' => 'AccountController@shareTaskIntegral']);
	    Route::post('/account/checkuser','AccountController@checkUser');
	    /*账号设置结束*/

	    /*名片*/
	    Route::get('/account/card/index','CardController@index');
	    Route::post('/card/saveInfo','CardController@saveInfo');
	    Route::post('/card/custom/save','CardController@saveCustom');
	    Route::post('/card/setting','CardController@setting');
	    Route::post('/card/screen',['as'=> 'card_screen','uses' => 'CardController@screen']);
	    Route::post('/card/setdefault',['as'=> 'card_setdefault','uses' => 'CardController@setdefault']);
	    Route::get('/card/cardAlbum',['as'=> 'get_card_album','uses' => 'CardController@cardAlbum']);
	    Route::post('/card/addCardAlbum',['as'=> 'add_card_album','uses' => 'CardController@addCardAlbum']);
	    Route::post('card/album/remove',['as'=> 'remove_card_album','uses' => 'CardController@removeCardAlbum']);
	    Route::post('card/syn',['as'=> 'syn_card','uses' => 'CardController@synCard']);
	    Route::post('card/syn/cancel',['as'=> 'syn_card','uses' => 'CardController@cancelSynCard']);
	    Route::post('card/contribute',['as'=> 'card_contribute','uses' => 'CardController@contributeCard']);
	    /*名片*/

	    Route::post('music/list',['as'=> 'music_list','uses' => 'MusicController@getMusic']);

	    /*文章*/
	    Route::get('/user/post/list','PostController@getList');
	    Route::post('/post/saveInfo','PostController@saveInfo');
	    Route::post('/post/delete','PostController@deletePost');
	    Route::post('/post/deleteReprint','PostController@deletePostReprint');
	    /*文章*/

	    /*微链接*/
	    Route::get('/microlink/load','MicrolinkController@load');
	    Route::post('/microlink/add','MicrolinkController@add');
	    Route::post('/microlink/edit','MicrolinkController@edit');
	    Route::post('/microlink/icons','MicrolinkController@icons');
	    /*微链接*/

	    /*推荐人*/
	    Route::get('/account/u_referrer','AccountController@u_referrer');
	    Route::get('/account/referrer','AccountController@referrer');
	    /*推荐人*/

	    /*赏金*/
		Route::post('/account/reward/tointegral',['as'=> 'account_reward_tointegral','uses' => 'RewardController@toIntegral']);
		Route::get('/reward/record',['as'=> 'account_reward_record','uses' => 'RewardController@record']);
		Route::get('/reward',['as'=> 'account_reward_index','uses' => 'RewardController@index']);
	    /*赏金*/

	    /*vip订单*/
		Route::post('/order/vip',['as'=> 'vip_order','uses' => 'OrderRechargeController@vipOrder']);
	    /*vip订单*/

	    /*vip订单*/
		Route::post('/order/vipUpgrade',['as'=> 'vip_upgrade_order','uses' => 'OrderRechargeController@vipUpgradeOrder']);
	    /*vip订单*/

	     /*积分订单*/
		Route::post('/order/integral',['as'=> 'integral_order','uses' => 'OrderRechargeController@integralOrder']);
	    /*积分订单*/

	    /*名片续费订单*/
		Route::post('/order/cardRenewalOrder', 'OrderRechargeController@cardRenewalOrder');
		/*名片续费订单*/

	    /*提现申请*/
		Route::post('/payout/apply',['as'=> 'payout_apply','uses' => 'PayoutController@apply']);
		Route::get('/payout/apply/list',['as'=> 'payout_apply_list','uses' => 'PayoutController@applyList']);
		Route::get('/payout/info',['as'=> 'payout_info','uses' => 'PayoutController@info']);
	    /*提现申请*/

	    /*积分*/
	    Route::get('/integral',['as'=> 'account_integral_index','uses' => 'IntegralController@index']);
		Route::get('/integral/record',['as'=> 'account_integral_record','uses' => 'IntegralController@record']);
		Route::post('/integral/transfer',['as'=> 'account_transfer','uses' => 'IntegralController@transfer']);
		Route::post('/integral/payer/check',['as'=> 'account_transfer_payer_check','uses' => 'IntegralController@checkPayer']);
		Route::post('/integral/toreward',['as'=> 'account_integral_toreward','uses' => 'IntegralController@toReward']);
	    /*积分*/

	    /*礼包佣金*/
	    Route::post('/account/giftComtoReward',['as'=> 'account_giftComtoReward','uses' => 'AccountController@giftComtoReward']);
	    Route::post('/account/giftComtoGold',['as'=> 'account_giftComtoGold','uses' => 'AccountController@giftComtoGold']);
	    /*礼包佣金*/


	    /*金麦*/
	    Route::get('/account/gold_info',['as'=> 'account_gold_info','uses' => 'GoldController@info']);
	    Route::post('/account/bonusToReward',['as'=> 'account_bonusToReward','uses' => 'GoldController@bonusToReward']);
	    Route::post('/account/goldComtoReward',['as'=> 'account_goldComtoReward','uses' => 'GoldController@goldComtoReward']);
	    /*金麦*/


	     //地址
	    Route::get('/address/list', ['as' => 'user_address_list', 'uses' => 'AddressController@getAddressList']);

	    Route::get('/address/load', ['as' => 'user_loadAddress', 'uses' => 'AddressController@loadAddress']);

	    //添加地址
		Route::post('/address/add', ['as' => 'user_add_address', 'uses' => 'AddressController@addAddress']);

		//编辑地址
		Route::post('/address/edit', ['as' => 'user_edit_address', 'uses' => 'AddressController@editAddress']);

		//删除地址
		Route::post('/address/delete', ['as' => 'user_delete_address', 'uses' => 'AddressController@deleteAddress']);

		//设置默认地址
		Route::post('/address/setdefault', ['as' => 'user_set_default_address', 'uses' => 'AddressController@setDefaultAddress']);

		 /*订单*/
	    Route::post('/order/create','OrderController@create');
	    Route::post('/order/pay','OrderController@pay');
	    Route::post('/order/checkOrderPay','OrderController@checkOrderPay');
	    Route::post('/order/cancel','OrderController@cancel');
	    Route::post('/order/finished','OrderController@orderFinished');
	    Route::post('/order/reviews/submit','ReviewsController@addAppOrderReviews');
	    Route::post('/order/reviews/add/{id}','ReviewsController@addOrderReviews');
	    Route::get('/order/getOrderReviews','ReviewsController@getOrderReviews');
	    
	    Route::post('/order/refund/apply','OrderController@refundApply');

	     /*订单*/
	     Route::get('/order/list','OrderController@getOrderList');
	     Route::get('/order/detail','OrderController@getOrderDetail');
	    /*订单*/

	    /*订单*/

		/*店铺*/
		Route::get('/store/getMyStoreInfo', ['as' => 'store_info', 'uses' => 'StoreController@getMyStoreInfo']);
		Route::post('/store/saveInfo', ['as' => 'store_saveInfo', 'uses' => 'StoreController@saveInfo']);
		Route::post('/store/changebanner','StoreController@changeBanner');
		Route::post('/store/addProduct','StoreController@addProduct');
		Route::post('/store/saveProduct','StoreController@saveProduct');
		Route::post('/store/addProductImage','StoreController@addProductImage');
		Route::post('/store/removeProductImage','StoreController@removeProductImage');
		Route::post('/store/deleteProductSku','StoreController@deleteProductSku');
        Route::post('/store/editProductVideo','StoreController@editProductVideo');
        Route::post('/store/enableProduct','ProductController@enableProduct');
        Route::post('/store/deleteProduct','ProductController@deleteProduct');
        Route::post('/store/orderShipped','StoreController@orderShipped');
        Route::post('/store/order/refundhandel','StoreController@orderRefundHandel');
        Route::post('/store/orderCount','StoreController@orderCount');
        Route::post('/store/productToShare','StoreController@productToShare');
        /*店铺*/

        /*店铺订单量*/
		Route::post('/order/store',['as'=> 'store_order','uses' => 'OrderRechargeController@storeOrder']);
	    /*店铺订单量*/
	});

	Route::post('/feedback','FeedbackController@submit');

	//帮助页
	Route::get('/help/school','HelpController@school');
	Route::get('/help/catalogDoc','HelpController@catalogDoc');
	Route::get('/help/help/{id}','HelpController@help');
});
