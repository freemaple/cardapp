<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\UserService;
use App\Libs\Service\CardService;
use Auth;
use Helper;
use App\Models\User\Student;
use App\Models\User\User as UserModel;
use App\Models\User\PhoneCode as PhoneCodeModel;
use App\Models\Order\Order as OrderModel;
use App\Models\Order\OrderRefund as OrderRefundModel;
use App\Models\User\TaskIntegralRecord;
use App\Models\User\ShareDate as ShareDateModel;
use App\Models\Equity\Equity as EquityModel;
use App\Models\Product\Wish as ProductWishModel;
use App\Models\Gold\Config as GoldConfigModel;
use App\Models\Gift\Gift as GiftModel;
use App\Models\Product\Product as ProductModel;
use App\Cache\Product as ProductCache;

use App\Libraries\Storage\User as UserStorage;

use App\Cache\User as UserCache;
use App\Cache\Theme as ThemeCache;
use App\Cache\Home as HomeCache;



class AccountController extends BaseController
{

    /**
     * 我的应用
     * @param  Request $request 
     * @return string           
     */
    public function entry(Request $request){

        $result = ['code' => '2x1', 'message' => ''];

        $user = Auth::user();

        $result['data']['user_info'] = $user;
        $result['data']['listItem'] = [
            [
                'text' => '1、赠送店铺',
            ],[
                'text' => '2、个人电子名片 + 制作宣传文章',
            ],[
                'text' => '3、礼包代购金',
            ],[
                'text' => '4、金麦蕙资产',
            ],[
                'text' => '5、分享/自购省',
            ]
        ];
        $result['data']['vip_gift_image'] = \Helper::asset_url('/media/images/vip_gift.jpg');
        $result['code'] = 'Success';

        return response()->json($result);
    }


    /**
     * 我的应用
     * @param  Request $request 
     * @return string           
     */
    public function center(Request $request){

        $result = ['code' => '2x1', 'message' => ''];

        $user = Auth::user();

        $application = [
            [
                'text' => '我的名片',
                'route' => '/pages/account/card/index',
                'icon' => 'idcard',
                'icon_bf' => '#1f8ff3'
            ],[
                'text' => '我的文章',
                'route' => '/pages/account/post/index',
                'icon' => 'article',
                'icon_bf' => '#8891eb'
            ],[
                'text' => '我的扫码购',
                'route' => '/pages/account/store/index?to_product=1',
                'icon' => 'share',
                'name' => 'store_to_product',
                'icon_bf' => '#f69369'
            ]
        ];
        
        if(!empty($user) && $user['is_vip']){
            $application[] = [
                'text' => '我要卖货',
                'route' => '/pages/shop/index',
                'icon' => 'share1',
                'icon_bf' => '#f69369'
            ];
        }

        $application[] = [
            'text' => '屏保名片',
            'route' => '/pages/account/card/screen',
            'icon' => 'setting',
            'icon_bf' => '#f69369'
        ];

        $card_qrcode = '';
        if(!empty($user) && $user->is_vip){
            $card = CardService::getDefaultCard($user->id);
            $card_qrcode = CardService::qrcode($card, 280);
        }

        $result['data']['application'] = $application;
        $result['data']['user_info'] = [
            'card_qrcode' => $card_qrcode
        ];
        $result['data']['logo'] = \Helper::asset_url('/media/images/logo.png');
        $result['code'] = 'Success';

        return response()->json($result);
    }

	
    /**
     * 用户基本信息修改
     * @param  Request $request 
     * @return string           
     */
    public function changeInfo(Request $request){

        $result = ['code' => '2x1', 'message' => ''];

        $user = Auth::user();

    	//姓名
    	$fullname = trim($request->fullname);

        //邮箱
        $email = trim($request->email);

        if ($email != '' && !preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
            $result['message'] = "无效的 email 格式！";
            return response()->json($result);
        }

        if(isset($request->gender)){
             //性别
            $gender = trim($request->gender);
            if (!in_array($gender, ['0', '1', '2'])) {
                $result['message'] = "性别无效！";
                return response()->json($result);
            }
        }

        //数据校验
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|max:20',
            'email' => 'max:64',
            'weixin' => 'max:100',
            'signature_title' => 'max:255',
            'signature_content' => 'max:255'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['message'] = "提交数据错误！";
            return $result;
        }

        //微信
        $weixin = trim($request->weixin);

        //自定义内容
        $signature_title = trim($request->signature_title);

        $signature_content = trim($request->signature_content);

    	$data = [
    		'fullname' => $fullname,
            'email' => $email,
            'weixin' => $weixin,
            'signature_title' => $signature_title,
            'signature_content' => $signature_content
    	];

        if(isset($request->gender)){
            //性别
            $gender = trim($request->gender);
            $data['gender'] = $gender;
        }

        if(isset($request->weixin_qr)){
            $weixin_qr = $request->weixin_qr;
            $qr = $this->base64upload($weixin_qr, '/weixin');
            if($qr && $qr['status']){
                $data['weixin_qr'] = $qr['filepath'];
                $old_weixin_qr = $user->weixin_qr;
            }
        }

        foreach ($data as $key => $value) {
            $user->$key = $value;
        }
         //保存基本信息
        $user->save();

        if(!empty($old_weixin_qr)){
            $user = Auth::user();
            if($old_weixin_qr != $user->$weixin_qr){
                $UserStorage = new UserStorage('weixin');
                $UserStorage->deleteFile($old_weixin_qr);
            }
        }

        UserCache::clearCache($user->id);

        $result['code'] = 'Success';
        $result['message'] = '保存成功';

    	return response()->json($result);
    }

    /**
     * 修改密码
     * @param  Request $request 
     * @return string
     */
    public function changePwd(Request $request){
        //当前密码
    	$current_password = trim($request->password_old);
        //新密码
    	$new_password = trim($request->password);
        //新密码确认
    	$confirm_new_password = trim($request->confirm_password);
    	$user = Auth::user();
        //修改密码
    	$result = UserService::getInstance()->changePwd($user, $current_password, $new_password, $confirm_new_password);
    	return response()->json($result);
    }

    /**
     * 修改交易密码
     * @param  Request $request 
     * @return string
     */
    public function changeTransactionPwd(Request $request){
        $user = Auth::user();
        $phone = $user['phone'];
        //code
        $code = trim($request->code);

        $environment = \App::environment();

        if($environment == 'local' && $code == env('test_phone_code')){

            $PhoneCodeModel = null;

        } else {
            $PhoneCodeModel = PhoneCodeModel::where('phone', $phone)->where('code', $code)
            ->where('type', 'transaction_password')
            ->first();
            if($PhoneCodeModel == null){
                $result['code'] = 'code_error';
                $result['message'] = '验证码错误！';
                return response()->json($result);
            }
            if($PhoneCodeModel['is_use'] == '1'){
                $result['code'] = 'code_error';
                $result['message'] = '验证码已使用！';
                return response()->json($result);
            }

            $created_at = $PhoneCodeModel['created_at'];

            $new_date = date('Y-m-d H:i:s', strtotime("-1hour", time()));

            if($new_date > $created_at){
                $result['code'] = 'code_error';
                $result['message'] = '验证码已过期！';
                return $result;
            }
        
        }
        
        

        //新密码
        $new_password = trim($request->password);
        //新密码确认
        $confirm_new_password = trim($request->confirm_password);
        $user = Auth::user();
        //修改交易密码
        $result = UserService::getInstance()->changeTransactionPwd($user, $new_password, $confirm_new_password);
        if($result['code'] == 'Success'){
            if(!empty($PhoneCodeModel)){
                $PhoneCodeModel->is_use = '1';
                $PhoneCodeModel->save();
            }
        }
        return response()->json($result);
    }

    /**
     * 修改头像
     * @param  Request $request 
     * @return string
     */
    public function changeavatar(Request $request){
        $user = Auth::user();
        $result = ['code' => 'Error'];
        //检查文件上传
        $file = $request->file('image');
        if(!$file || !$file->isValid()){
            $result['message'] = 'This is not a valid image.';
            return response()->json($result);
        }
        //获取上传文件的大小
        $size = $file->getSize();
        //这里可根据配置文件的设置，做得更灵活一点
        if($size > 10*1024*1024){
            $result['message'] = '上传文件不能超过10M';
            return response()->json($result);
        }
        $path = $file->path();
        $type = $file->getClientMimeType();
        list($width, $height, $type, $attr) = getimagesize($path);
        $UserStorage = new UserStorage('avatar');
        if($width > 300){
            $h = 300 / $width * $height;
            $img = \Image::make($file);
            $filepath = 'avatar/' . md5(time()) . '.jpg';
            $img = $img->resize(300, $h)->save(storage_path() . '/app/static/' . $filepath);
        } else {
            $filepath = $UserStorage->saveUpload($file);
        }
        $old_avatar = $user->avatar;
        $user->avatar = $filepath;
        $user->save();
        if(!empty($old_avatar)){
            $user = Auth::user();
            if($user->avatar != $old_avatar && strpos($user->avatar, 'default') == false){
                $UserStorage->deleteFile($old_avatar);
            }
        }
        UserCache::clearCache($user->id);
        $result['code'] = 'Success';
        $result['data']['avatar'] = \HelperImage::getavatar($user->avatar);
        $result['message'] = '保存成功';
        return response()->json($result);
    }

    /**
     * 微信修改
     * @param  Request $request 
     * @return string           
     */
    public function changeWeixin(Request $request){
        $result = ['code' => 'Error', 'message' => ''];
        //姓名
        $weixin = trim($request->weixin);
        $weixin_qr = trim($request->weixin_qr);
        $signature_title = trim($request->signature_title);
        $signature_content = trim($request->signature_content);
        $qr = $this->base64upload($weixin_qr, '/weixin');
        $user = Auth::user();
        $user->weixin = $weixin;
        $user->signature_title = $signature_title;
        $user->signature_content = $signature_content;
        if($qr && $qr['status']){
            $old_weixin_qr = $user->weixin_qr;
            $user->weixin_qr = $qr['filepath'];
        }
        $user->save();
        if(!empty($old_weixin_qr)){
            $UserStorage = new UserStorage('weixin');
            $UserStorage->deleteFile($old_avatar);
        }
        UserCache::clearCache($user->id);
        $result['code'] = 'Success';

        $result['message'] = '保存成功';
       
        return response()->json($result);
    }

    /**
     * 上传产品图片
     */
    public function base64upload($base64_image_content, $directory) {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $matches)){
            //图片路径地址    
            $fullpath = 'storage/' . $directory;
            if(!is_dir($fullpath)){
                mkdir($fullpath, 0777, true);
            }
            $image_file = \Image::make($base64_image_content);
            $width = $image_file->width();
            $height = $image_file->height();
            if($width > 1024){
                $h = 1024 / $width * $height;
                $image_file = $image_file->resize(1024, $h);
                $base64_image_content = $image_file->encode('data-url');
            }
            $type = $matches[2];
            $content_arr = explode($matches[0], $base64_image_content);
            $img = base64_decode($content_arr[1]);
            $filename = md5(date('YmdHis').rand(1000, 999999)). '.jpg';
            $filepath = 'weixin/' . $filename;
            $savepath = storage_path() . '/app/static/' . $filepath;
            //服务器文件存储路径
            if (file_put_contents($savepath, $img)){
                $result['status'] = 1;
                $result['filename'] = $filename;
                $result['filepath'] = $filepath;
                $result['filelink'] = \HelperImage::storagePath($filepath);
                return $result;
            }else{
                $result['status'] = 0;
                $result['message'] = '保存失败';
                return $result;
            }
        } else {
            $result['status'] = 0;
            $result['message'] = '不是有效的图片';
            return $result;
        }
    }

    /**
     * 帐号信息
     * @param  Request $request 
     * @return string
     */
    public function info(Request $request){
        $user = Auth::user();
        $data = [];
        $reward_amount = 0;
        //赏金
        $reward = $user->reward()->first();
        if($reward != null){
           $reward = $reward->toArray();
           $reward_amount = $reward['amount'];
        }
        $reward_amount = $reward['amount'];
        $freeze_amount = $reward['freeze_amount'];
        if($freeze_amount <=0){
            $freeze_amount = 0;
        }
        $reward_amount = $reward_amount - $freeze_amount;
        if($reward_amount <=0){
            $reward_amount = 0;
        }
        $data['reward'] = $reward_amount;
        $integral_amount = 0;
        //积分
        $integral = $user->integral()->first();
        if($integral != null){
           $integral = $integral->toArray();
           $integral_amount = $integral['point'];
        }
        $data['integral'] = $integral_amount;
        $data['currency'] = '￥';

        $rf_count = UserModel::where('referrer_user_id', '=', $user->id)
        ->where('level_status', '>=', '1')
        ->where('is_vip', '=', '1')
        ->count();
        $data['rf_count'] = $rf_count;

        $date = date("Y/m/d");
        $today = date("Y-m-d");
        $day = \Helper::getthemonth($today);
        $rf_month_count = UserModel::where('referrer_user_id', '=', $user->id)
        ->where('created_at', '>=', $day[0])
        ->where('created_at', '<=', $day[1])
        ->where('level_status', '>=', '1')
        ->where('is_vip', '=', '1')
        ->count();
        $data['rf_count'] = $rf_count;
        $data['rf_month_count'] = $rf_month_count;


        $rf_second_count = UserModel::where('second_referrer_user_id', '=', $user->id)
        ->where('level_status', '>=', '1')
        ->where('is_vip', '=', '1')
        ->count();

        $data['$rf_second_count'] = $rf_second_count;

        $rf_second_month_count = UserModel::where('second_referrer_user_id', '=', $user->id)
        ->where('created_at', '>=', $day[0])
        ->where('created_at', '<=', $day[1])
        ->where('level_status', '>=', '1')
        ->where('is_vip', '=', '1')
        ->count();

        $data['rf_second_count'] = $rf_second_count;
        $data['rf_second_month_count'] = $rf_second_month_count;


        $rf_s_count = UserModel::where('source_referrer_user_id', '=', $user->id)
        ->where('level_status', '>=', '1')
        ->where('is_vip', '=', '1')->count();
        $data['rf_s_count'] = $rf_s_count;
        $rf_s_month_count = UserModel::where('source_referrer_user_id', '=', $user->id)
        ->where('created_at', '>=', $day[0])
        ->where('created_at', '<=', $day[1])
        ->where('is_vip', '=', '1')
        ->where('level_status', '>=', '1')
        ->count();
        $data['rf_s_month_count'] = $rf_s_month_count;
        $result['data'] = $data;
        $result['code'] = 'Success';
        $result['message'] = '保存成功';
        return response()->json($result);
    }

     /**
     * 帐号信息
     * @param  Request $request 
     * @return string
     */
    public function accountinfo(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
    
        //vip到期时间
        $date = date('Y-m-d H:i:s');
        $vip_end_day = \Helper::diffBetweenTwoDays($date, $user->vip_end_date);


         //vip状态
        $level_status = config('user.level_status');

        $referrer_user = UserModel::where('id', '=', $user->referrer_user_id)->first();

        $referrer_user_name = '';

        if($referrer_user != null){
            $referrer_user = $referrer_user->toArray();
            $referrer_user_name = $referrer_user['fullname']. '(' . \Helper::hideStar($referrer_user['phone']) . ')';
        }

        $result['data']['user_info'] = [
            'phone' =>  $user['phone'],
            'phone_text' =>  \Helper::hideStar($user['phone']),
            'user_name' => $user['user_name'],
            'fullname' => $user['fullname'],
            'nickname' => $user['nickname'],
            'weixin' => $user['weixin'],
            'created_at' => (String)$user->created_at,
            'vip_end_day' => $vip_end_day,
            'email' => $user['email'],
            'avatar' => \HelperImage::getavatar($user['avatar']),
            'level_status' => $level_status[$user['level_status']],
            'referrer_user_name' => $referrer_user_name
        ];
        $result['code'] = 'Success';
        $result['message'] = '保存成功';
        return response()->json($result);
    }

     /**
     * 帐号信息
     * @param  Request $request 
     * @return string
     */
    public function userinfo(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        $data = [];
        $reward_amount = 0;
        //赏金
        $reward = $user->reward()->first();
        if($reward != null){
           $reward = $reward->toArray();
           $reward_amount = $reward['amount'];
        }
        $reward_amount = $reward['amount'];
        $freeze_amount = $reward['freeze_amount'];
        if($freeze_amount <=0){
            $freeze_amount = 0;
        }
        $reward_amount = $reward_amount - $freeze_amount;
        if($reward_amount <=0){
            $reward_amount = 0;
        }
        $data['reward'] = $reward_amount;
        $integral_amount = 0;
        //积分
        $integral = $user->integral()->first();
        if($integral != null){
           $integral = $integral->toArray();
           $integral_amount = $integral['point'];
        }
        $data['integral'] = $integral_amount;
        $data['currency'] = '￥';

        $equity_value = 0;

        $equity = EquityModel::where('user_id', $user->id)->first();

        if($equity != null){
            $equity_value = $equity->equity_value;
        }

        $data['equity_value'] = $equity_value;

        $status_count = OrderModel::select('order_status_code', \DB::raw('COUNT(id) AS total'))
        ->where('user_id', $user_id)
        ->whereIn('order_status_code', ['pending', 'shipping', 'shipped'])
        ->groupBy('order_status_code')
        ->get();


        $order_status_count = [];
        foreach($status_count as $key => $val){
            $status_code = $val['order_status_code'];
            $order_status_count[$status_code] = $val['total'];
        }

        $un_review_count = OrderModel::where('user_id', $user_id)
            ->where('order_status_code', 'finished')
            ->where('is_review', '!=', '1')
            ->count();

        $order_status_count['review'] = $un_review_count;

        $refund_count = OrderRefundModel::join('order', 'order.id', '=', 'order_refund.order_id')
        ->where('order_refund.user_id', $user_id)
        ->where('order_refund.status', '0')
        ->where('order.user_id', $user_id)
        ->count();

        $wish_count = ProductWishModel::where('user_id', $user_id)
        ->count();

        $data['wish_count'] = $wish_count;

        $order_status_count['refund'] = $refund_count;

        //vip到期时间
        $date = date('Y-m-d H:i:s');
        $vip_end_day = \Helper::diffBetweenTwoDays($date, $user->vip_end_date);


         //vip状态
        $level_status = config('user.level_status');

        $referrer_user = UserModel::where('id', '=', $user->referrer_user_id)->first();

        $referrer_user_name = '';

        if($referrer_user != null){
            $referrer_user = $referrer_user->toArray();
            $referrer_user_name = $referrer_user['fullname']. '(' . \Helper::hideStar($referrer_user['phone']) . ')';
        }

        $gift_commission = 0;

        $user_commission = $user->commission()->first();

        $manager_commission = 0;

        if(!empty($user_commission)){
            $gift_commission = $user_commission->gift_commission;
            $manager_commission = $user_commission->manager_commission;
        }

        $user_gold = UserService::getInstance()->getUserGold($user);

        $user_gold_amount = 0;

        if(!empty($user_gold)){

            $goldConfig = GoldConfigModel::first();

            $gift_unit = $goldConfig['gift_unit'];
            
            $gold_number = $user_gold['gold_number'];

            $gold_total = $gold_number * $gift_unit;

            $user_gold_amount = $user_gold['bonus_amount'] + $gold_total;
        }

        $serviceList = [
            [
                'name'=> 'address',
                'icon' => 'icon-address',
                'desc'=> '收货地址',
                'url'=> '/pages/addressManage/addressManage'
            ],
            [
                'name'=> 'wish',
                'icon' => 'icon-wish',
                'desc'=> '我的收藏',
                'url'=> '/pages/account/collections'
            ],
             [
                'name'=> 'huoban',
                'icon' => 'icon-dianpu',
                'desc'=> '浏览记录',
                'url'=> '/pages/viewdhistory/index'
            ],
            [
                'name'=> 'huoban',
                'icon' => 'icon-huoban',
                'desc'=> '我的战友',
                'is_vip' => '1',
                'url'=> '/pages/account/referrer'
            ],
            [
                'name'=> 'wish',
                'icon' => 'icon-fensi',
                'desc'=> '我的粉丝',
                'is_vip' => '1',
                'url'=> '/pages/account/u_referrer'
            ],
            [
                'name'=> 'xuexi',
                'icon' => 'icon-xuexi',
                'desc'=> '商学院',
                'url'=> '/pages/help/school'
            ],
            [
                'name'=> 'help',
                'icon' => 'icon-edit',
                'desc'=> '关于我们',
                'url'=> '/pages/help/help?name=about-us&title=关于我们'
            ],
            [
                'name'=> 'wx',
                'icon' => 'icon-weixingongzhonghao',
                'desc'=> '关注公众号',
                'url'=> '/pages/help/help?name=wx-accounts&title=关注公众号'
            ],
            [
                'name'=> 'setting',
                'icon'=> 'icon-setting',
                'desc'=> '帐号设置',
                'url'=> '/pages/account/config'
            ],
            [
                'name'=> 'logout',
                'icon'=> 'icon-logout',
                'desc'=> '退出账号',
                'url'=> ""
            ]
        ];

        //邀请二维码
        $link_qrcode = $this->registerLinkQR(300);

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
            $share_product_link = '/pages/product/index?goods_id=' . $products[0]['id'];
        }

        $result['data']['user_info'] = [
            'is_vip' =>  $user['is_vip'],
            'level_status_value' =>  $user['level_status'],
            'phone' =>  $user['phone'],
            'phone_text' =>  \Helper::hideStar($user['phone']),
            'fullname' => $user['fullname'],
            'user_name' => $user['user_name'],
            'nickname' => $user['nickname'],
            'weixin' => $user['weixin'],
            'created_at' => (String)$user->created_at,
            'vip_end_day' => $vip_end_day,
            'email' => $user['email'],
            'user_data' => $data,
            'avatar' => \HelperImage::getavatar($user['avatar']),
            'order_status_count' => $order_status_count,
            'level_status' => $level_status[$user['level_status']],
            'referrer_user_name' => $referrer_user_name,
            'gift_commission' => $gift_commission,
            'manager_commission' => '￥' . $manager_commission,
            'manager_commission_text' => '稻田管理积分',
            'user_gold_amount' => $user_gold_amount,
            'sub_integral_amount' => $user->sub_integral_amount,
            'serviceList' => $serviceList,
            'link_qrcode' => $link_qrcode,
            'today_task_status' => $today_task_status,
            'today_task_status_text' => $today_task_status_text,
            'share_product_link' => $share_product_link
        ];
        $result['code'] = 'Success';
        $result['message'] = '保存成功';
        return response()->json($result);
    }

     /**
     * 帐号信息
     * @param  Request $request 
     * @return string
     */
    public function orderCount(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        $data = [];

        $status_count = OrderModel::select('order_status_code', \DB::raw('COUNT(id) AS total'))
        ->where('user_id', $user_id)
        ->whereIn('order_status_code', ['pending', 'shipping', 'shipped'])
        ->groupBy('order_status_code')
        ->get();

        $order_status_count = [];
        foreach($status_count as $key => $val){
            $status_code = $val['order_status_code'];
            $order_status_count[$status_code] = $val['total'];
        }

        $un_review_count = OrderModel::where('user_id', $user_id)
            ->where('order_status_code', 'finished')
            ->where('is_review', '!=', '1')
            ->count();

        $order_status_count['review'] = $un_review_count;

        $refund_count = OrderRefundModel::join('order', 'order.id', '=', 'order_refund.order_id')
        ->where('order_refund.user_id', $user_id)
        ->where('order_refund.status', '0')
        ->where('order.user_id', $user_id)
        ->count();

        $order_status_count['refund'] = $refund_count;

        //$order_status_count['complete'] = $complete_count;

        $result['data'] = $order_status_count;

        $result['code'] = 'Success';
        $result['message'] = '';
        return response()->json($result);
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
        $type = $request->type;

        $referrers = $referrers->toArray();
        foreach ($referrers['data'] as $key => $value) {
           $referrers['data'][$key]['avatar'] = \HelperImage::getavatar($value['avatar']);
        }
        if($type == 'app'){

        } else {
            $view = view('account.u_referrer.block.list')->with('referrer_list', $referrers['data'])->render();
            $result['view'] = $view;
            unset($referrers['data']);
        }
        $result['data'] = $referrers;
        $result['code'] = 'Success';
        $result['message'] = '';
        return response()->json($result);
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
        $type = $request->type;
        if($type == 'app'){
            $referrers_list = [];
            foreach ($referrers['data'] as $key => $value) {
               $referrers_list[] = [
                    'id' => $value['id'],
                    'u_id' => $value['u_id'],
                    'nickname' => $value['nickname'],
                    'weixin' => $value['weixin'],
                    'weixin_qr' => $value['weixin_qr'],
                    'avatar' => \HelperImage::getavatar($value['avatar']),
                    'phone' => $value['phone'],
                    'fullname' => $value['fullname'],
                    'created_at' => $value['created_at'],
                    "level_status_text" => $value['level_status_text'],
                    "rf_count" => $value['rf_count'],
                    "rf_month_count" => $value['rf_month_count'],
                    "honor_value" => $value['honor_value'],
                    "honor_vip_value" => $value['honor_vip_value'],
                    'vip_end_date' => $value['vip_end_date'],
                    'rupgrade' => $value['rupgrade']
               ];
            }
            $result['data'] = $referrers_list;
        } else {
            $view = view('account.referrer.block.list')->with('referrer_list', $referrers['data'])->render();
            unset($referrers['data']);
            $result['view'] = $view;
            $result['data'] = $referrers;
        }
        
        
        $result['code'] = 'Success';
        $result['message'] = '';
        return response()->json($result);
    }

    /**
     * 名片屏保保存
     *
     * @return \Illuminate\Http\Response
     */
    public function shareInfo(Request $request)
    {
        $user = Auth::user();
        $backgrounds = ThemeCache::shareBackgrounds();
        foreach ($backgrounds as $key => $value) {
            $backgrounds[$key]['image_link'] = \HelperImage::storagePath($value['image']);
        }
        //邀请二维码
        $link_qrcode = $this->registerLinkQR(60);
        $result['data'] = [
            'user' => $user,
            'backgrounds' => $backgrounds,
            'link_qrcode' => $link_qrcode
        ];
        $result['code'] = 'Success';
        return response()->json($result);
    }

    /**
     * 名片屏保
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function share(Request $request){
        $img = $request->img;
        $postion = $request->postion;
        if(!is_array($postion)){
            $postion = json_decode($postion, true);
        }
        $user = \Auth::user();
        $path = storage_path() . '/app/static/' . $img;
        // 修改指定图片的大小
        
        $img = \Image::make($path);
        $width = $img->width();
        $height = $img->height();
        $p = 640 / $width;
        $width = 640;
        $height = $height * $p;
        $img = $img->resize($width, $height);
        $po = 640 / 260;

        $code_left = floor($postion['left'] * $po);
        $code_top = floor($postion['top'] * $po);


        //邀请二维码
        $link_qrcode = $this->registerLinkQR(60 * $po);
        $fullname = \Helper::hideNameStar($user['fullname']);
        // use callback to define details
        $img->text($fullname, $code_left + 60 * $po / 2 - 40, $code_top + 60 * $po + 50, function($font) {
            $font->file(public_path('fonts/msyhbd.ttf'));
            $font->size(30);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('bottom');
        });
        // 插入水印, 水印位置在原图片的右下角, 距离下边距 10 像素, 距离右边距 15 像素
        $image_data = $img->insert($link_qrcode, 'top-left', $code_left, $code_top)->encode('data-url');
        $base64_code = $image_data->encoded;
        $result = [];
        $result['code'] = 'Success';
        $result['data'] = $base64_code;
        return response()->json($result);
    }

    /**
     * 邀请二维码
     *
     * @return \Illuminate\Http\Response
     */
    private function registerLinkQR($size){
        $user = Auth::user();
        $user_id = $user->id;
        $link = \Helper::route('auth_login', ['register', 'rid' => $user_id, 'is_share' => '1']);
        $link_qrcode = \Helper::qrcode1($link, $size);
        return 'data:image/png;base64,' . base64_encode($link_qrcode);
    }

    /**
     * vip任务积分
     *
     * @return \Illuminate\Http\Response
     */
    public function shareTaskIntegral(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        $date = date('Y-m-d');
        $ShareDateModel = ShareDateModel::where('user_id', $user_id)->where('date', $date)->first();
        if(empty($ShareDateModel)){
            //$ShareDateModel = new ShareDateModel();
            //$ShareDateModel->user_id = $user_id;
            //$ShareDateModel->date = $date;
            //$ShareDateModel->count = 1;
        } else {
            //$ShareDateModel->count = $ShareDateModel->count + 1;
        }
        //$ShareDateModel->save();
        $task_integral = config('user.task.task_integral', 0);
        if($task_integral <=0){
            return;
        }
        if($user['is_vip']){
            $TaskIntegralRecord = TaskIntegralRecord::where('user_id', '=', $user_id)->where('type', 'share')
            ->where('date', '=', $date)->first();
            if($TaskIntegralRecord == null){
                \DB::transaction(function() use ($user, $user_id, $task_integral) {
                    $TaskIntegralRecord = new TaskIntegralRecord();
                    $TaskIntegralRecord->user_id = $user_id;
                    $TaskIntegralRecord->integral = $task_integral;
                    $TaskIntegralRecord->content = '分享挣任务积分';
                    $TaskIntegralRecord->type = 'share';
                    $TaskIntegralRecord->date = date('Y-m-d');
                    $r = $TaskIntegralRecord->save();
                    if($r){
                        $user->task_integral = $user->task_integral + $task_integral;
                        $user->save();
                    }
                });
            }
        }
        $result = [];
        $result['code'] = 'Success';
        return response()->json($result);
    }

     /**
     * vip任务积分
     *
     * @return \Illuminate\Http\Response
     */
    public function checkUser(Request $request){
        $result = [];
        $user = Auth::user();
        $phone = trim($request->phone);
        if($user->phone == $phone){
            $result['code'] = '2x1';
            $result['message'] = '对不起，不能为自己代购';
            return response()->json($result);
        }
        $user_id = $user->id;
        if(strlen($phone) == 11){
            $u = UserModel::where('phone', $phone)->orderBy('is_vip', 'asc')->where('id', '!=', $user_id)
            ->first();
        } else {
            $u = UserModel::where('user_name', $phone)->where('id', '!=', $user_id)->first();
        }
        if($u == null){
            $result['code'] = '2x1';
            $result['message'] = '账号不存在！';
            return response()->json($result);
        }
        if($u['referrer_user_id'] != $user->id){
            $result['code'] = '2x1';
            $result['message'] = '对不起，不能为此用户代购';
            return response()->json($result);
        }
        $result['code'] = 'Success';
        $result['message'] = $u['fullname'] . '( ' . $phone . ' )';
        $u_id = $u['u_id'];
        $link = \Helper::route('account_vip_rupgrade', ['uid' => $u_id]);
        $result['data'] = ['link' => $link, 'uid' => $u_id];
        return response()->json($result);
    }

    /**
     * 礼包佣金转余额
     *
     * @return \Illuminate\Http\Response
     */
    public function giftComtoReward(Request $request){
        $result = [];
        $user = Auth::user();
        $transaction_password = $request->transaction_password;
        //检查当前密码是否正确
        if(!\Hash::check($transaction_password, $user->transaction_password)){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起,当前交易密码错误';
            return $result;
        }
        $amount = trim($request->amount);
        $user_comm = $user->Commission()->first();
        if(empty($user_comm)){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有任何麦粒';
            return response()->json($result);
        }
        $gift_commission = $user_comm->gift_commission;
        if($gift_commission <=0){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有任何麦粒';
            return response()->json($result);
        }
        if($amount > $gift_commission){
            $result['code'] = '2x1';
            $result['message'] = '对不起，只剩' . $gift_commission . '麦粒';
            return response()->json($result);
        }
        $res = \DB::transaction(function() use ($user, $amount) {

            //接收人消息
            $content = '麦粒转入余额 ￥' . $amount . '';

            UserService::getInstance()->userRewardIncome($user, $amount, $content, '');

            UserService::getInstance()->userCommissionOut($user, $amount, $content);
            
            return true;
        }); 
        if(!$res){
            $result['code'] = '2x1';
            $result['message'] = '对不起，操作失败，请稍后再试！';
            return response()->json($result);
        }
        $result['code'] = 'Success';
        $result['message'] = '转换成功';
        $result['data'] = [];
        return response()->json($result);
    }

    /**
     * 礼包佣金置换金麦
     *
     * @return \Illuminate\Http\Response
     */
    public function giftComtoGold(Request $request){
        $result = [];
        $user = Auth::user();
        $transaction_password = $request->transaction_password;
        //检查当前密码是否正确
        if(!\Hash::check($transaction_password, $user->transaction_password)){
            $result['code'] = "0x00x2";
            $result['message'] = '对不起,当前交易密码错误';
            return $result;
        }
        $gold_number = trim($request->gold_number);
        $gold_config = GoldConfigModel::first();
        $gift_unit = $gold_config->gift_unit;
        $amount = $gift_unit * $gold_number;
        $user_comm = $user->Commission()->first();
        if(empty($user_comm)){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有任何麦粒';
            return response()->json($result);
        }
        $gift_commission = $user_comm->gift_commission;
        if($gift_commission <=0){
            $result['code'] = '2x1';
            $result['message'] = '对不起，没有任何麦粒';
            return response()->json($result);
        }
        if($amount > $gift_commission){
            $result['code'] = '2x1';
            $result['message'] = '对不起，只剩' . $gift_commission . '麦粒';
            return response()->json($result);
        }
        $res = \DB::transaction(function() use ($user, $gold_number, $amount) {

            UserService::getInstance()->userGoldNumberIn($user, $gold_number);

            //接收人消息
            $content = '麦粒置换金麦穗 ' . $gold_number . '支';

            UserService::getInstance()->userCommissionOut($user, $amount, $content);
            
            return true;
        }); 
        if(!$res){
            $result['code'] = '2x1';
            $result['message'] = '对不起，操作失败，请稍后再试！';
            return response()->json($result);
        }
        $result['code'] = 'Success';
        $result['message'] = '转换成功';
        $result['data'] = [];
        return response()->json($result);
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
        $vip_end_day = \Helper::diffBetweenTwoDays($date, $user->vip_end_date);
        $gifts = GiftModel::where('gift_type', 'vip')->get();
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
        $result['code'] = 'Success';
        $result['data'] = [
            'user_info' => [
                'phone' =>  $user['phone'],
                'phone_text' =>  \Helper::hideStar($user['phone']),
                'fullname' => $user['fullname'],
                'nickname' => $user['nickname'],
                'vip_end_day' => $vip_end_day,
                'avatar' => \HelperImage::getavatar($user['avatar']),
            ],
            'title' => '开通vip',
            'vip_gift_image' => \Helper::asset_url('/media/images/vip_gift.jpg'),
            'vip_end_day' => $vip_end_day,
            'gifts' => $gifts
        ];
        return response()->json($result);
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
        $gift = GiftModel::where('gift_type', 'vip')->where('id', $gift_id)->first();
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $product_sku = ProductCache::defaultSKU($product);
                $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                $product['sku'] = $product_sku;
                $product = $product->toArray();
            }
            $gift['product'] = $product;
            $goods_detail = ProductCache::productView($gift->product_id);
        }
        $uid = $request->uid;
        $query = ['gift_id' => $gift['id']];
        if(isset($request->uid)){
           $query['uid'] = $uid;
        }
        $result['code'] = 'Success';
        $result['data'] = [
            'user_info' => [
                'phone' =>  $user['phone'],
                'phone_text' =>  \Helper::hideStar($user['phone']),
                'fullname' => $user['fullname'],
                'nickname' => $user['nickname'],
                'vip_end_day' => $vip_end_day,
                'avatar' => \HelperImage::getavatar($user['avatar']),
            ],
            'vip_end_day' => $vip_end_day,
            'gift' => $gift,
            'goods_detail' => $goods_detail,
        ];
        return response()->json($result);
    }

}
