<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Libs\Service\MessageService;
use App\Libs\Service\UserService;
use App\Libs\Service\CardService;
use App\Models\User\User as UserModel;
use App\Models\User\VipPackage as VipPackageModel;
use App\Models\Store\StorePackage as StorePackageModel;
use App\Cache\Notice as NoticeCache;
use App\Cache\Theme as ThemeCache;
use Auth;
use Session;
use Helper;
use App\Models\User\Statistics as UserStatisticsModel;
use App\Models\User\StatisticsDate as UserStatisticsDateModel;
use App\Models\Equity\Equity as EquityModel;
use App\Models\Gift\Gift as GiftModel;
use App\Models\Product\Product as ProductModel;
use App\Models\User\ShareDate as ShareDateModel;
use App\Cache\Product as ProductCache;
use App\Cache\Home as HomeCache;


class AccountController extends BaseController
{

    /**
     * 我的帐户管理
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        //vip到期时间
        $date = date('Y-m-d H:i:s');
        $vip_end_day = Helper::diffBetweenTwoDays($date, $user->vip_end_date);

        //邀请二维码
        $link_qrcode = $this->registerLinkQR(300);
        
        //积分二维码
        $integral_qrcode = $this->integralTransferQrCode($user->id, 300);
        //vip个数
        $honor_value = UserModel::where('referrer_user_id', $user->id)
        ->where('is_vip', '=', '1')
        ->count();

        //vip金个数
        $honor_vip_value = UserModel::where('referrer_user_id', $user->id)
        ->where('level_status', '>=', '2')
        ->where('is_vip', '=', '1')
        ->count();
        $vip_color = [
            '1' => '#ff9800',
            '2' => '#f00',
            '3' => '#b506e1'
        ];

        //vip状态
        $level_status = config('user.level_status');

        //显示在前端的状态
        $order_status_list = config('order.account_show_status');

        $referrer_user = UserModel::where('id', '=', $user->referrer_user_id)->first();

        if($referrer_user != null){
            $referrer_user = $referrer_user->toArray();
        }

        $serviceList = [
            [
                'name'=> 'address',
                'icon' => 'icon-address',
                'desc'=> '收货地址',
                'url'=> \Helper::route('account_address')
            ],
            [
                'name'=> 'wish',
                'icon' => 'icon-wish',
                'desc'=> '我的收藏',
                'url'=> \Helper::route('account_wish')
            ],
            [
                'name'=> 'huoban',
                'icon' => 'icon-huoban',
                'desc'=> '我的战友',
                 'is_vip' => '1',
                'url'=> \Helper::route('account_referrer')
            ],
            [
                'name'=> 'wish',
                'icon' => 'icon-fensi',
                'desc'=> '我的粉丝',
                'is_vip' => '1',
                'url'=> \Helper::route('account_u_referrer')
            ],
            [
                'name'=> 'xuexi',
                'icon' => 'icon-xuexi',
                'desc'=> '商学院',
                'url'=> \Helper::route('help_school')
            ],
            [
                'name'=> 'help',
                'icon' => 'icon-edit',
                'desc'=> '关于我们',
                'url'=> \Helper::route('help')
            ],
            [
                'name'=> 'wx',
                'icon' => 'icon-weixingongzhonghao',
                'desc'=> '关注公众号',
                'url'=> \Helper::route('help_view', 'wx-accounts')
            ],
            [
                'name'=> 'setting',
                'icon'=> 'icon-setting',
                'desc'=> '帐号设置',
                'url'=> \Helper::route('account_setting')
            ],
            [
                'name'=> 'logout',
                'icon'=> 'icon-logout',
                'desc'=> '退出登录',
                'a_class' => 'js_logout_tip',
                'url'=> "javascript:void(0)",
                'data_url'=> "/logout"
            ]
        ];

        if(in_array($user['user_type'], ['manager', 'director'])){
            /*$serviceList[] =  [
                'name'=> 'statistics',
                'icon'=> 'icon-statistics',
                'desc'=> '我的统计',
                'url'=> \Helper::route('account_statistics', 'wx-accounts')
            ];*/
        }

        $gift_commission = 0;

        $user_commission = $user->commission()->first();

        $manager_commission = 0;

        if(!empty($user_commission)){
            $gift_commission = $user_commission->gift_commission;
            $manager_commission = $user_commission->manager_commission;
        }

        $user_gold = UserService::getInstance()->getUserGold($user);

        $today_date = date('Y-m-d');

        $today_share_data = ShareDateModel::where('date', $today_date)->where('user_id', $user->id)->first();

        $today_task_status = '0';

        $today_task_status_text = '未完成';

        if(!empty($today_share_data) && $today_share_data['status'] == '1'){
            $today_task_status = 1;
            $today_task_status_text = '已完成';
        }

        $products = HomeCache::shareProduct();

        $share_product_link = '';

        if(!empty($products)){
            $share_product_link = Helper::route('product_view', [$products[0]['id']]);
        }

        $view = view('account.index',[
            'user' => $user,
            'serviceList' => $serviceList,
            'referrer_user' => $referrer_user,
            'title' => '我的帐户管理',
            'vip_end_day' => $vip_end_day,
            'level_status' => $level_status,
            'honor_value' => $honor_value,
            'honor_vip_value' => $honor_vip_value,
            'link_qrcode' => $link_qrcode,
            'integral_qrcode' => $integral_qrcode,
            'order_status_list' => $order_status_list,
            'vip_color' => $vip_color,
            'gift_commission' => $gift_commission,
            'manager_commission' => $manager_commission,
            'user_gold' => $user_gold,
            'today_task_status' => $today_task_status,
            'today_task_status_text' => $today_task_status_text,
            'share_product_link' => $share_product_link
        ]);
        return $view;
    }

    /**
     * 我的帐户管理
     *
     * @return \Illuminate\Http\Response
     */
    public function entry()
    {
        $user = Auth::user();
        $vippackage = VipPackageModel::where('enable', '=', '1')->where('year', '=', '1')->first();
        $store_package = StorePackageModel::where('enable', '=', '1')->first();
        $view = view('account.entry',[
            'user' => $user,
            'title' => '开通vip',
            'vippackage' => $vippackage,
            'store_package' => $store_package
        ]);
        return $view;
    }

    /**
     * 我的积分收款码
     *
     * @return \Illuminate\Http\Response
     */
    public static function integralTransferQrCode($user_id, $size){
        $link = Helper::route('account_integral_transfer', ['u_id' => $user_id]);
        $link_qrcode = Helper::qrcode1($link, $size);
        return  'data:image/png;base64,' . base64_encode($link_qrcode);
    }

    /**
     * 邀请二维码
     *
     * @return \Illuminate\Http\Response
     */
    public static function registerLinkQR($size){
        $user = Auth::user();
        $user_id = $user->id;
        $link = Helper::route('auth_login', ['register', 'rid' => $user_id, 'is_share' => '1']);
        $link_qrcode = Helper::qrcode1($link, $size);
        return  'data:image/png;base64,' . base64_encode($link_qrcode);
    }

    /**
     * 开通vip
     *
     * @return \Illuminate\Http\Response
     */
    public function vip()
    {
        $user = Auth::user();
        $date = date('Y-m-d H:i:s');
        $vip_end_day = Helper::diffBetweenTwoDays($date, $user->vip_end_date);
        $vippackage = VipPackageModel::where('enable', '=', '1')->where('year', '=', '1')->first();
        $view = view('account.vip',[
            'user' => $user,
            'title' => '开通vip',
            'vip_end_day' => $vip_end_day,
            'vippackage' => $vippackage
        ]);
        return $view;
    }


     /**
     * 升级vip
     *
     * @return \Illuminate\Http\Response
     */
    public function vipUpgrade()
    {
        $user = Auth::user();
        $date = date('Y-m-d H:i:s');
        $vip_end_day = Helper::diffBetweenTwoDays($date, $user->vip_end_date);
        $gifts = GiftModel::where('gift_type', 'vip')->where('enable', '1')->where('deleted', '!=', '1')->get();
        foreach ($gifts as $key => $gift) {
            if($gift != null){
                $product = ProductModel::where('id', $gift->product_id)->first();
                if($product != null){
                    $product_sku = ProductCache::defaultSKU($product);
                    $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                    $product['sku'] = $product_sku;
                    $product = $product->toArray();
                }
                $gifts[$key]['product'] = $product;
            }
        }
        $view = view('account.vipUpgrade',[
            'user' => $user,
            'title' => '开通vip',
            'vip_end_day' => $vip_end_day,
            'gifts' => $gifts
        ]);
        return $view;
    }

    /**
     * 升级vip
     *
     * @return \Illuminate\Http\Response
     */
    public function vipUpgradeDetail(Request $request)
    {
        $user = Auth::user();
        $date = date('Y-m-d H:i:s');
        $vip_end_day = Helper::diffBetweenTwoDays($date, $user->vip_end_date);
        $gift_id = $request->gift_id;
        $gift = GiftModel::where('gift_type', 'vip')->where('id', $gift_id)->where('enable', '1')->where('deleted', '!=', '1')->first();
        if(empty($gift)){
            $view = view('product.unfound',[
                'goods_detail' => []
            ]);
            return $view;
        }
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $skus_stock = $product->skus()->where('deleted', '!=', '1')->sum('stock');
                $product_sku = ProductCache::defaultSKU($product);
                $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                $product['sku'] = $product_sku;
                $product = $product->toArray();
            }
            $gift['product'] = $product;

            $goods_detail = ProductCache::productView($gift->product_id);

            $goods_detail['skus_stock'] = $skus_stock;
        }
        $uid = $request->uid;
        $query = ['gift_id' => $gift['id']];
        if(isset($request->uid)){
           $query['uid'] = $uid;
            $checkoutUrl = \Helper::route('checkout_viprupgrade', $query);
        } else {
            $checkoutUrl = \Helper::route('checkout_vipUpgrade', $query);
        }
        
        $view = view('account.vipUpgradeDetail',[
            'user' => $user,
            'title' => '开通vip',
            'vip_end_day' => $vip_end_day,
            'gift' => $gift,
            'goods_detail' => $goods_detail,
            'checkoutUrl' => $checkoutUrl
        ]);
        return $view;
    }

    /**
     * 管理中心
     *
     * @return \Illuminate\Http\Response
     */
    public function center()
    {
        $user = Auth::user();
        $card_qrcode = '';
        if($user->is_vip){
            $card = CardService::getDefaultCard($user->id);
            $card_qrcode = CardService::qrcode($card, 280);
        }
        $view = view('account.center', [
            'user' => $user,
            'title' => '应用管理中心',
            'card_qrcode' => $card_qrcode
        ]);
        return $view;
    }


    /**
     * 帐号设置页面
     *
     * @return \Illuminate\Http\Response
     */
    public function setting(Request $request)
    {
        $user = Auth::user();
        $tab = $request->tab;
        $view = view('account.setting',[
            'user' => $user,
            'title' => '账户设置',
            'tab' => $tab
        ]);
        return $view;
    }

    /**
     * 统计
     *
     * @return \Illuminate\Http\Response
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $user_statistics = [];

        $year = trim($request->year);

        $month = trim($request->month);

        if($year == null && $month == null){
            $user_statistics = UserStatisticsModel::where('user_id', $user_id)->first();
            if($user_statistics != null){
                $user_statistics = $user_statistics->toArray();
            }
        } else {
            $user_statistics_date_first = UserStatisticsDateModel::where('user_id', $user_id)->first();
            if($user_statistics_date_first != null){
                $user_statistics_date = UserStatisticsDateModel::where('user_id', $user_id);
                if($year != null){
                    $user_statistics_date = $user_statistics_date->where('year', $year);
                }
                if($year != null && $month != null){
                    $user_statistics_date = $user_statistics_date->where('month', $month);
                }
                $user_statistics['vip_open_number'] = $user_statistics_date->sum('vip_open_number');
                $user_statistics['vip_renewal_number'] = $user_statistics_date->sum('vip_renewal_number');
                $user_statistics['store_number'] = $user_statistics_date->sum('store_number');
            }
        }

        $referrer_user = UserModel::where('referrer_user_id', '=', $user_id);

        if($year != null){
            if($month != null){
                $f_date = $year ."-" .$month ."-01";
                $last_year = $year;
                if($month == '12'){
                    $last_year = intval($year + 1);
                    $last_month = '01';
                } else {
                    $last_month = intval($month + 1);
                    if($last_month < 10){
                        $last_month = '0'. $last_month;
                    }
                }
                $l_date = $last_year ."-" .$last_month. '-01';
            } else {
                $f_date = $year .'-01-01';
                $l_date = intval($year + 1) . '-01-01';
            }
            $referrer_user = $referrer_user->where('created_at', '>=', $f_date)
            ->where('created_at', '<', $l_date);
        }
        

        $referrer_user_count = $referrer_user->count();

        $user_statistics['referrer_user_count'] = $referrer_user_count;

        $user_statistics = $user_statistics;

        $months = [];

        for($m=1; $m<=12; $m++){
            $months[] = $m < 10 ? '0' . $m : $m;
        }

        $view = view('account.statistics', [
            'user' => $user,
            'user_statistics' => $user_statistics,
            'months' => $months,
            'year' => $year,
            'month' => $month,
            'title' => '统计'
        ]);
        return $view;
    }

    /**
     * 设置交易密码
     *
     * @return \Illuminate\Http\Response
     */
    public function transactionPassword()
    {
        $user = Auth::user();
        $view = view('account.transaction_password',[
            'user' => $user,
            'title' => '设置交易密码'
        ]);
        return $view;
    }

    /**
     * 消息
     *
     * @return void
    */
    public function message(Request $request)
    {
        $form = $request->all();

        $user = Auth::user();

        $user_id = $user->id;

        $pageSize = config('paginate.message', 100);

        $status = trim($request->status);

        //消息列表
        $message_list = MessageService::getUserMessage($user_id, $pageSize);

        $view = View('account.message.index', [
            'title' => '我的奏折',
            'message_list' => $message_list
        ]);

        return $view;

    }

    /**
     * 我的潜在推荐人
     *
     * @return void
    */
    public function u_referrer(Request $request)
    {
        $form = $request->all();
        $user = Auth::user();
        $pageSize = config('paginate.referrer', 50);
        $referrers = UserModel::where('is_vip', '=', '0')->where('vip_end_date', '=', null)
        ->where('referrer_user_id', '=', $user->id)
        ->paginate($pageSize);
        foreach ($referrers as $key => $referrer) {
            $referrers[$key]['rupgrade'] = 1;
            $u_id = $referrer['u_id'];
            $link = \Helper::route('account_vip_rupgrade', ['uid' => $u_id]);
            $referrers[$key]['rupgrade_link'] = $link;
        }
        $referrers = $referrers->toArray();
        $view = View('account.u_referrer.index', [
            'title' => '我的粉丝',
            'referrers' => $referrers,
            'form' => $form,
            'user' => $user
        ]);
        return $view;

    }


    /**
     * 我的推荐人
     *
     * @return void
    */
    public function referrer(Request $request)
    {
        $form = $request->all();
        $user = Auth::user();
        $pageSize = config('paginate.referrer', 50);
        $user_id = $user->id;
        $referrers = UserService::getInstance()->referrer($user, $pageSize);
        //显示在前端的状态
        $store_level_text = config('store.level_text');
        $notice = NoticeCache::notice('referrer');
        $view = View('account.referrer.index', [
            'title' => '我的战友列表',
            'referrers' => $referrers,
            'form' => $form,
            'user' => $user,
            'notice' => $notice,
            'store_level_text' => $store_level_text
        ]);
        return $view;

    }

     /**
     * 名片屏保保存
     *
     * @return \Illuminate\Http\Response
     */
    public function share(Request $request)
    {
        $user = Auth::user();
        $backgrounds = ThemeCache::shareBackgrounds();
        //$backgrounds = ThemeCache::backgrounds();
        //邀请二维码
        $link_qrcode = $this->registerLinkQR(60);
        $view = view('account.share',[
            'user' => $user,
            'title' => '选择分享海报图片',
            'description' => '',
            'keywords' => '',
            'backgrounds' => $backgrounds,
            'link_qrcode' => $link_qrcode
        ]);
        return $view;
    }

    /**
     * 代购开通vip
     *
     * @return \Illuminate\Http\Response
     */
    public function rupgrade(Request $request)
    {
        $user = Auth::user();
        $uid = $request->uid;
        $u_user = UserModel::where('u_id', '=', $uid)->first();
        $gifts = GiftModel::where('gift_type', 'vip')->where('enable', '1')->where('deleted', '!=', '1')->get();
        foreach ($gifts as $key => $gift) {
            if($gift != null){
                $product = ProductModel::where('id', $gift->product_id)->first();
                if($product != null){
                    $product_sku = ProductCache::defaultSKU($product);
                    $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                    $product['sku'] = $product_sku;
                    $product = $product->toArray();
                }
                $gifts[$key]['product'] = $product;
            }
        }
        $view = view('account.rupgrade',[
            'user' => $user,
            'uid' => $uid,
            'u_user' => $u_user,
            'title' => '代购开通',
            'description' => '',
            'keywords' => '',
            'gifts' => $gifts
        ]);
        return $view;
    }
}
