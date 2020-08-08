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

Route::group(['namespace' => 'Front'], function()
{
	//首页
    Route::get('/', ['as'=> 'home','uses' => 'SiteController@index']);
    Route::get('/viewdhistory', ['as'=> 'viewdhistory','uses' => 'SiteController@viewdhistory']);

	/*文章页*/
	Route::get('/post/{number}',['as'=> 'post_view','uses' => 'PostController@view']);
	//转发
	Route::get('/post/reprint/{id}',['as'=> 'post_reprint_view','uses' => 'PostController@reprintview']);
	//文章浏览记录
	Route::post('/post/viewed', 'PostController@viewed');
	/*文章页结束*/

	/*搜索*/
	Route::get('/search/post',['as'=> 'search_post','uses' => 'SearchController@post']);
	/*搜索*/

	/*美文*/
	Route::get('/beauty/post',['as'=> 'beauty_post','uses' => 'ArticleController@beautyPost']);
	/*美文*/

	/*名片主页*/
	Route::get('/card/{number}',['as'=> 'card_view','uses' => 'CardController@view']);
	//名片浏览记录
	Route::post('/card/viewed', 'CardController@viewed');
	/*名片主页结束*/

	/*登录注册验证模块*/
	Route::get('/auth/{type}',['as'=> 'auth_login','uses' => 'AuthController@index']);
	Route::get('/auth/reset/{id}',['as'=> 'auth_reset','uses' => 'AuthController@reset']);
	Route::get('/logout', ['as'=> 'auth_logout','uses' => 'AuthController@logout']);
	/*登录注册验证模块结束*/

	/*帮助页面*/
	Route::get('/help/school',['as'=> 'help_school','uses' => 'HelpController@school']);
	Route::get('/help/catalog/doc/{id}',['as'=> 'help_catalog_doc','uses' => 'HelpController@catalogDoc']);
	Route::get('/help/{id}',['as'=> 'help_view','uses' => 'HelpController@view']);
	Route::get('/help',['as'=> 'help','uses' => 'HelpController@index']);
	Route::get('/feedback',['as'=> 'feedback','uses' => 'FeedbackController@index']);
	/*帮助页面*/

	/*文库*/
	Route::get('/article',['as'=> 'article','uses' => 'ArticleController@index']);
	/*文库*/

	/*分类文库*/
	Route::get('/category/article/{id}',['as'=> 'article_category_view','uses' => 'ArticleController@categorypost']);
	/*分类文库*/

	/*店铺主页*/
	Route::get('/store/{id}',['as'=> 'store_view','uses' => 'StoreController@view']);
	Route::post('/store/viewed', 'StoreController@viewed');
	Route::get('/store/info/{id}', ['as'=> 'store_info_view','uses' => 'StoreController@storeinfo']);
	/*店铺主页*/

	/*自营产品*/
	Route::get('/shop',['as'=> 'shop','uses' => 'ShopController@index']);
	/*自营产品*/

	/*详情页*/
	Route::post('/product/viewed', 'ProductController@viewed');
	Route::get('/product/{id}',['as'=> 'product_view','uses' => 'ProductController@view']);
	Route::get('/product/reviews/{id}',['as'=> 'product_reviews','uses' => 'ProductController@reviews']);
	/*详情页*/

	/*产品*/
	Route::get('/search',['as'=> 'search','uses' => 'SearchController@index']);
	/*产品*/

	/*分类文库*/
	Route::get('/category/{id?}',['as'=> 'category_view','uses' => 'CategoryController@view']);
	/*分类文库*/

	/*商家*/
	Route::get('/merchant',['as'=> 'merchant','uses' => 'MerchantController@index']);
	/*商家*/

	/*支付*/
	Route::get('/checkout/pay',['as'=> 'checkout','uses' => 'CheckoutController@index']);
	/*支付*/

	/*用户中心(需要用户登录验证的路由)*/
	Route::group(['middleware' => ['user_auth']], function()
	{
		/*账号*/
		Route::get('/account',['as'=> 'account_index','uses' => 'AccountController@index']);
		Route::get('/account/entry',['as'=> 'account_entry','uses' => 'AccountController@entry']);
	    Route::get('/account/setting',['as'=> 'account_setting','uses' => 'AccountController@setting']);
	    Route::get('/account/setting/tpwd',['as'=> 'account_setting_tpwd','uses' => 'AccountController@transactionPassword']);
	    Route::get('/account/center',['as'=> 'account_center','uses' => 'AccountController@center']);
	    /*海报分享*/
	    Route::get('/account/share',['as'=> 'account_share','uses' => 'AccountController@share']);
	    /*海报分享*/

	    Route::get('/account/statistics',['as'=> 'account_statistics','uses' => 'AccountController@statistics']);
	    /*账号*/

	    /*开通vip*/
	    Route::get('/account/vip',['as'=> 'account_vip','uses' => 'AccountController@vip']);
	    /*开通vip*/

	    /*开通vip*/
	    Route::get('/account/vipUpgrade',['as'=> 'account_vipUpgrade','uses' => 'AccountController@vipUpgrade']);
	    Route::get('/account/vipUpgradeDetail',['as'=> 'account_vipUpgradeDetail','uses' => 'AccountController@vipUpgradeDetail']);
	    /*开通vip*/

	    /*推荐人*/
	    Route::get('/account/u_referrer',['as'=> 'account_u_referrer','uses' => 'AccountController@u_referrer']);
		Route::get('/account/referrer',['as'=> 'account_referrer','uses' => 'AccountController@referrer']);
	    /*推荐人*/

	    /*赏金*/
		Route::get('/account/reward',['as'=> 'account_reward','uses' => 'RewardController@reward']);
	    /*赏金*/

	    /*积分*/
		Route::get('/account/integral',['as'=> 'account_integral','uses' => 'IntegralController@integral']);
		Route::get('/account/integral/transfer',['as'=> 'account_integral_transfer','uses' => 'IntegralController@transfer']);
	    /*积分*/

	    //vip支付
		Route::get('/checkout/vip', ['as'=> 'checkout_vip','uses' => 'CheckoutController@vip']);

		//vip支付成功页面
		Route::get('/checkout/vip/success/{order_no}', ['as'=> 'checkout_vip_success','uses' => 'CheckoutController@vipSuccess']);


		//vip升级支付
		Route::get('/checkout/vipUpgrade', ['as'=> 'checkout_vipUpgrade','uses' => 'CheckoutController@vipUpgrade']);

		//vip代购升级支付
		Route::get('/checkout/viprupgrade', ['as'=> 'checkout_viprupgrade','uses' => 'CheckoutController@viprupgrade']);

		//积分支付
		Route::get('/checkout/integral', ['as'=> 'checkout_integral','uses' => 'CheckoutController@integral']);

		//积分充值支付成功页面
		Route::get('/checkout/integral/success/{order_no}', ['as'=> 'checkout_integral_success','uses' => 'CheckoutController@integralSuccess']);

		//名片续费支付成功页面
		Route::get('/checkout/card/renewal/success/{order_no}', ['as'=> 'checkout_card_renewal_success','uses' => 'CheckoutController@cardRenewalSuccess']);

		//店铺支付
		Route::get('/checkout/store', ['as'=> 'checkout_store','uses' => 'CheckoutController@store']);
		Route::get('/checkout/store/success/{order_no}', ['as'=> 'checkout_store_success','uses' => 'CheckoutController@storeSuccess']);


	    Route::group(['middleware' => ['user_vip_auth']], function(){

	    	/*名片列表*/
			Route::get('/account/card',['as'=> 'account_card_index','uses' => 'UserCardController@index']);
			//添加名片
			Route::get('/account/card/add',['as'=> 'account_card_add','uses' => 'UserCardController@add']);
			//名片编辑
		    Route::get('/account/card/edit/{number}',['as'=> 'account_card_edit','uses' => 'UserCardController@edit']);
		    //名片自定义
		    Route::get('/account/card/custom/{number}',['as'=> 'account_card_custom','uses' => 'UserCardController@custom']);

		    Route::get('/account/card/album/{id}',['as'=> 'account_card_album','uses' => 'UserCardController@album']);
		    //名片屏保
		    Route::get('/account/card/screen',['as'=> 'account_card_screen','uses' => 'UserCardController@screen']);
		    /*名片设置*/

		    //名片屏保
		    Route::get('/account/card/renewal',['as'=> 'account_card_renewal','uses' => 'UserCardController@renewal']);
		    /*名片设置*/

		    //续费名片
			Route::get('/checkout/card/renewal', ['as'=> 'checkout_card_renewal','uses' => 'CheckoutController@cardRenewal']);

		    /*微链接*/
			Route::get('/account/microlink',['as'=> 'account_microlink_index','uses' => 'MicrolinkController@index']);
		    /*名片设置*/

		    /*文章列表*/
			Route::get('/account/post',['as'=> 'account_post_index','uses' => 'UserPageController@index']);
			/*添加文章*/
			Route::get('/account/post/add',['as'=> 'account_post_add','uses' => 'UserPageController@add']);
			/*编辑文章*/
			Route::get('/account/post/edit/{number}',['as'=> 'account_post_edit','uses' => 'UserPageController@edit']);


			//名片屏保
		    Route::get('/account/vip/rupgrade',['as'=> 'account_vip_rupgrade','uses' => 'AccountController@rupgrade']);
		    /*名片设置*/

	    });

	   /*收藏夹*/
		Route::get('/account/wish',['as'=> 'account_wish','uses' => 'WishController@index']);
	    /*收藏夹*/

	    /*地址*/
		Route::get('/account/address',['as'=> 'account_address','uses' => 'AddressController@index']);
	    /*地址*/

	    /*消息列表*/
		Route::get('/account/message',['as'=> 'account_message','uses' => 'AccountController@message']);
	    /*消息列表*/

	    /*提现*/
	    Route::get('/account/payout/index',['as'=> 'account_payout_index','uses' => 'PayoutController@index']);
		Route::get('/account/payout/apply',['as'=> 'account_payout_apply','uses' => 'PayoutController@apply']);
	    /*提现*/

	    //订单列表
    	Route::get('/account/orders', 'OrderController@index')->name('account_orders');

    	//订单详情
    	Route::get('/account/order/detail/{id}', 'OrderController@orderDetail')->name('account_order_detail');

    	//订单支付
    	Route::get('/checkout/order/pay/{id}', 'OrderController@orderPay')->name('account_order_pay');

    	//查看订单评论
    	Route::get('/account/order/reviews/{order_id}', 'ReviewsController@findOrderReviews')->name('account_order_reviews');

    	//添加订单评论
    	Route::get('/account/order/reviews/add/{order_id}', 'ReviewsController@addOrderReviews')->name('account_order_reviews_add');

    	//申请订单退款
    	Route::get('/account/order/refund/{order_id}', 'OrderController@orderRefund')->name('account_order_refund');

    	//订单退款申请列表
    	Route::get('/account/order/refundlist', 'OrderController@orderRefundList')->name('account_order_refund_list');


    	//支付成功页面
		Route::get('/account/order/pay/success/{order_no}', ['as'=> 'account_order_pay_success','uses' => 'OrderController@successPay']);

		/*店铺*/

	    Route::get('/account/store',['as'=> 'account_store','uses' => 'StoreController@index']);
		Route::get('/account/store/info',['as'=> 'account_store_info','uses' => 'StoreController@info']);
		Route::get('/account/store/products',['as'=> 'account_store_products','uses' => 'StoreController@products']);
		Route::get('/account/store/product/add',['as'=> 'account_store_product_add','uses' => 'StoreController@addProduct']);
		Route::get('/account/store/product/edit/{id}',['as'=> 'account_store_product_edit','uses' => 'StoreController@editProduct']);

		//订单列表
    	Route::get('/account/store/orders', 'StoreController@orders')->name('account_store_orders');

    	//订单详情
    	Route::get('/account/store/order/detail/{id}', 'StoreController@orderDetail')->name('account_store_order_detail');

    	//查看订单评论
    	Route::get('/account/store/order/reviews/{order_id}', 'StoreController@findOrderReviews')->name('account_store_order_reviews');

    	//订单退款
    	Route::get('/account/store/order/refundlist', 'StoreController@orderRefundList')->name('account_store_order_refundlist');

	    /*店铺*/

	    /*金麦*/
	    Route::get('/account/gold',['as'=> 'account_gold','uses' => 'GoldController@index']);
	});
	/*用户中心(需要用户登录验证的路由)*/

	/*微信授权*/
	Route::group(['middleware' => ['web', 'wechat.oauth']], function(){
		Route::any('/wx/oauth/auth', ['as'=> 'wx_oauth_auth','uses' => 'WechatController@oauthAuth']);
	});

	/*微信支付*/
	/*vip开通微信支付回调*/
	Route::any('/wx/vip/payment/callback', ['as'=> 'wx_vip_payment_back','uses' => 'WechatController@vipPaymentBack']);
	/*微信支付*/

	/*积分充值微信支付回调*/
	Route::any('/wx/integral/payment/callback', ['as'=> 'wx_integral_payment_back','uses' => 'WechatController@integralPaymentBack']);
	/*微信支付*/

	/*名片续费微信支付回调*/
	Route::any('/wx/card/renewal/payment/callback', ['as'=> 'wx_card_renewal_payment_back','uses' => 'WechatController@cardRenewalPaymentBack']);
	/*微信支付*/

	/*订单微信支付回调*/
	Route::any('/wx/order/payment/callback', ['as'=> 'wx_order_payment_back','uses' => 'WechatController@orderProductPaymentBack']);
	/*订单微信支付回调*/

	/*订单微信支付回调*/
	Route::any('/wx/store/payment/callback', ['as'=> 'wx_store_payment_back','uses' => 'WechatController@storePaymentBack']);
	/*订单微信支付回调*/

	/*微信支付测试*/
	Route::any('/wx/pay_checkout', ['as'=> 'wx_pay','uses' => 'PaymentController@pay']);
	Route::any('/wx/pay_callback', ['as'=> 'wx_pay_back','uses' => 'PaymentController@paymentcallback']);
	Route::post('/wx/pay_order', ['as'=> 'wx_pay_back','uses' => 'PaymentController@payOrder']);
	/*微信支付测试*/
});

/**公众号开发*/
Route::group(['namespace' => 'Wx', 'prefix' => 'wx'], function()
{

	Route::any('/buttons', ['as'=> 'wx_buttons','uses' => 'WechatController@buttons']);

    Route::any('/serve', ['as'=> 'wx_home','uses' => 'WechatController@serve']);

    Route::any('/pay', ['as'=> 'wx_pay','uses' => 'WechatController@pay']);

    Route::any('/token', ['as'=> 'wx_token','uses' => 'WechatController@token']);
});
/**公众号开发*/


/**api*/
Route::group(['namespace' => 'Api', 'prefix' => 'api'], function()
{
	/*app更新*/
	Route::get('/app/update', 'AppController@update');
});


