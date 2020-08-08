<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Libs\Service\OrderRechargeService;
use App\Models\Order\Recharge as OrderRecharge;  
use App\Models\Order\Order as OrderModel; 
use App\Models\User\VipPackage as VipPackageModel;
use App\Models\Product\Product as ProductModel;
use App\Models\User\User as UserModel;
use App\Models\User\Address as AddressModel;
use App\Models\Store\StorePackage as StorePackageModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Gift\Gift as GiftModel;
use App\Libs\Service\ProductDispalyService;
use App\Cache\Checkout as CheckoutCache;
use EasyWeChat\Foundation\Application;
use App\Cache\Help as HelpCache;
use App\Cache\Product as ProductCache;
use App\Libs\Service\PositionService;

class CheckoutController extends BaseController
{
    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $message = '';
        $data = [];
        $view = view('checkout.index');
        $view->with('data', $data);

        //登录验证
        $session_user = \Auth::user();
        \Session::set('checkout_redirect_link', \URL::full());
        if($session_user == null){
            $link = \Helper::route('auth_login', ['register', 'is_checkout' => '1']);
            return redirect($link);
        }

        //获取用户ID
        $user_id = $session_user->id;

        //产品ID
        $goods_id = $request->goods_id;        

        //产品SKU
        $goods_sku_id = $request->goods_sku_id;

        //SKU数量
        $qty = $request->qty;

        $basket_code = $request->basket_code;

        if($basket_code != null){
            $checkout_cahe = CheckoutCache::getBasketCode($basket_code);
            if($checkout_cahe && $checkout_cahe['order_no'] != null){
                $order = OrderModel::where('order_no', $checkout_cahe['order_no'])->first();
                if($order != null){
                    $link = \Helper::route('account_order_detail', ['order_no' => $checkout_cahe['order_no']]);
                    return redirect($link);
                }
                
            }
        }

        //验证数据
        $validator = Validator::make(['goods_id' => $goods_id,  'goods_sku_id' => $goods_sku_id, 'qty' => $qty], [
            'goods_id' => 'required|int|min:1',
            'goods_sku_id' => 'required|string',
            'qty' => 'required|int|min:1'
        ]);
        
        if($validator->fails()){
            $view->with('message', 'The data format is incorrect.');
            return $view;
        }

        $product = ProductModel::where('id', $goods_id)->where('deleted', '0')->where('is_sale', '1')->first();

        //检查产品是否存在
        if($product == null){
            $view->with('message', 'product does not exist.');
            return $view;
        }

        if($product['is_self'] == '1'){
            $is_integral_pay = '1';
        } else {
            $is_integral_pay = $product['integral_pay'];
        }

        //检查产品和sku是否存在
        $sku = $product->skus()->where('product_sku.id', '=', $goods_sku_id)->where('deleted', '0')->where('is_sale', '1')->first();
        if($sku == null){
            $view->with('message', '此产品不存在！');
            return $view;
        }

        if($sku['stock'] == 0 ){
            $view->with('sku_message', "此产品已售罄！");
        }
        else if($qty > $sku['stock']){
            $qty = $sku['stock'];
            $stock = $sku['stock'];
            $view->with('sku_message', "此产品剩余库存 $stock");
        }

        $sku['image'] = \HelperImage::storagePath($sku->image);

        //获取用户默认选择地址
        $address_default = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
        if($address_default == null){
            //如果用户没有设置默认地址,获取第一条
            $address_default = AddressModel::where('user_id', '=', $user_id)->first();
        }

        $spec = ProductDispalyService::findProductSkuSpc($sku);

        $productList = [];

        $total_amount = 0;

        $subtotal_amount = 0;

        $sku = $sku->toArray();
        $sku['spec'] = $spec;

        $item = [];
        $item['goods_id'] = $product['id'];
        $item['name'] = $product['name'];
        $item['goods_sku_id'] = $sku['id'];
        $item['price'] = $sku['price'];
        $item['image'] = $sku['image'];
        $item['qty'] = $qty;
        $item['spec'] = $spec;
        $item['shipping'] = $sku['shipping'];
        $item['goods_sku_data'] = $sku;
        $subtotal_amount += $sku['price'] * $qty;

        $productList[] = $item;

        $shipping_amount = $sku['shipping'] * $qty;

        $total_amount = $subtotal_amount + $shipping_amount;

        $integral_amount = 0;

        $can_integral_amount = 0;

        $is_use_integral = 0;

        if($is_integral_pay == '1'){
             //积分
            $integral = $session_user->integral()->first();
            if($integral != null){
               $integral = $integral->toArray();
               $integral_amount = $integral['point'];
            }

            $can_integral_amount = $integral_amount;

            if($can_integral_amount > $total_amount){
                $can_integral_amount = $total_amount;
            }

            $is_use_integral = $request->use_integral;

            if($is_use_integral && $can_integral_amount > 0){
                $total_amount = $total_amount - $can_integral_amount;
            }
        }

        $provices = PositionService::provices();

        // 拼装数据
        $data['products'] = $productList;
        $data['address_default'] = $address_default;
        $data['total_amount'] = $total_amount;

        $data['subtotal_amount'] = $subtotal_amount;

        $data['shipping_amount'] = $shipping_amount;

        $sid = $request->sid;

        $view->with('data', $data);

        $view->with('integral_amount', $integral_amount);

        $view->with('can_integral_amount', $can_integral_amount);

        $view->with('is_use_integral', $is_use_integral);

        $view->with('title', '支付');

        $view->with('provices', $provices);

        $view->with('basket_code', $basket_code);

        $view->with('is_integral_pay', $is_integral_pay);

        $view->with('sid', $sid);

        return $view;
    }

    /**
     * 开通vip
     *
     * @return void
    */
    public function vip(Request $request)
    {
        $user = Auth::user();
        if(empty($user)){
            return redirect(\Helper::route('auth_login', ['login']));
        }
        $user_id = $user->id;
        $vippackage = VipPackageModel::where('enable', '=', '1')->where('year', '=', '1')->first();
        $total_amount = $vippackage['amount'];
        $order_no = $request->order_no;
        if(!empty($order_no)){
            $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->where('order_type', 'vip')->first();
            if($order != null){
                return redirect(\Helper::route('checkout_vip_success', [$order_no]));
            }
        }
        $vip_date = $user->vip_end_date;
        $vip_type = $request->vip_type;
        if(!$vip_date || !$user->is_vip){
            $vip_type == 'open';
        }
        if($user->is_vip && $vip_type == 'open'){
            return redirect(\Helper::route('account_index'));
        }
         //获取用户默认选择地址
        $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
        if($address == null){
            //如果用户没有设置默认地址,获取第一条
            $address = AddressModel::where('user_id', '=', $user_id)->first();
        }
        $gift_id = $request->gift_id;
        $gift = GiftModel::where('gift_type', 'vip')->where('id', $gift_id)->first();
        $product = [];
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $product_sku = ProductCache::defaultSKU($product);
                $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                $product['sku'] = $product_sku;
                $product = $product->toArray();
            }
        }
        $view = view('checkout.vip',[
            'title' => $vip_type == 'renewal' ? '续费vip' : '开通vip',
            'vippackage' => $vippackage,
            'description' => '',
            'keywords' => '',
            'user' => $user,
            'total_amount' => $total_amount,
            'order_no' => $order_no,
            'address' => $address,
            'product' => $product,
            'gift' => $gift
        ]);
        return $view;
    }

    /**
     * vip支付成功
     *
     * @return void
    */
    public function vipSuccess(Request $request, $order_no)
    {
        $user = Auth::user();
        $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->first();
        $view = view('checkout.vip_success',[
            'title' => '支付成功',
            'description' => '',
            'keywords' => '',
            'user' => $user,
            'order' => $order
        ]);
        return $view;
    }



     /**
     * 开通vip
     *
     * @return void
    */
    public function vipUpgrade(Request $request)
    {
        $user = Auth::user();
        if(empty($user)){
            return redirect(\Helper::route('auth_login', ['login']));
        }
        $user_id = $user->id;
        $vippackage = VipPackageModel::where('enable', '=', '1')->where('year', '=', '1')->first();
        $total_amount = $vippackage['amount'];
        $order_no = $request->order_no;
        if(!empty($order_no)){
            $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->where('order_type', 'vip')->first();
            if($order != null){
                return redirect(\Helper::route('checkout_vip_success', [$order_no]));
            }
        }
        $vip_date = $user->vip_end_date;
        $vip_type = $request->vip_type;
        if(!$vip_date || !$user->is_vip){
            $vip_type == 'open';
        }
        if($user->is_vip && $vip_type == 'open'){
            return redirect(\Helper::route('account_index'));
        }
         //获取用户默认选择地址
        $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
        if($address == null){
            //如果用户没有设置默认地址,获取第一条
            $address = AddressModel::where('user_id', '=', $user_id)->first();
        }
        $gift_id = $request->gift_id;
        $gift = GiftModel::where('gift_type', 'vip')->where('id', $gift_id)->first();
        $product = [];
        $product_sku_id = $request->product_sku_id;
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $product_sku = $product->skus()->where('id', $product_sku_id)->first();
                if($product_sku == null){
                    $product_sku = ProductCache::defaultSKU($product);
                }
                $product_sku_id = $product_sku['id'];
                $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                $product['sku'] = $product_sku;
                $product = $product->toArray();
            }
        }
        $product_sku_id = $request->product_sku_id;
        $store_agreement = HelpCache::get('store_agreement');
        $provices = PositionService::provices();
        $view = view('checkout.vipUpgrade',[
            'title' => '抢金麦礼包',
            ///'vippackage' => $vippackage,
            'description' => '',
            'keywords' => '',
            'user' => $user,
            'total_amount' => $total_amount,
            'order_no' => $order_no,
            'address' => $address,
            'product' => $product,
            'gift' => $gift,
            'product_sku_id' => $product_sku_id,
            'store_agreement' => $store_agreement,
            'provices' => $provices
        ]);
        return $view;
    }

    /**
     * 代购开通vip
     *
     * @return void
    */
    public function viprupgrade(Request $request)
    {
        $user = Auth::user();
        if(empty($user)){
            return redirect(\Helper::route('auth_login', ['login']));
        }
        $sub_integral_amount = $user->sub_integral_amount;
        $sub_integral_amount_use = $sub_integral_amount;
        if($sub_integral_amount_use > 200){
            $sub_integral_amount_use = 200;
        }
        $user_id = $user->id;
        $order_no = $request->order_no;
        if(!empty($order_no)){
            $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->where('order_type', 'vip')->first();
            if($order != null){
                return redirect(\Helper::route('checkout_vip_success', [$order_no]));
            }
        }
        $uid = $request->uid;
        $r_user = UserModel::where('u_id', '=', $uid)->first();
        if(empty($r_user)){
            return;
        }
        $r_user_id = $r_user->id;
        //获取用户默认选择地址
        $address = AddressModel::where('user_id', '=', $r_user_id)->where('is_default', '=', '1')->first();
        if($address == null){
            //如果用户没有设置默认地址,获取第一条
            $address = AddressModel::where('user_id', '=', $r_user_id)->first();
        }
        $gift_id = $request->gift_id;
        $gift = GiftModel::where('gift_type', 'vip')->where('id', $gift_id)->first();
        $product = [];
        $product_sku_id = $request->product_sku_id;
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $product_sku = $product->skus()->where('id', $product_sku_id)->first();
                if($product_sku == null){
                    $product_sku = ProductCache::defaultSKU($product);
                }
                $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                $product['sku'] = $product_sku;
                $product = $product->toArray();
            }
        }

        $total_amount = $gift['price'];

        $is_use_integral = $request->use_integral;

        if($is_use_integral && $sub_integral_amount_use > 0){
            $total_amount = $total_amount - $sub_integral_amount_use;
        }
        if($total_amount <=0){
            $total_amount = 0;
        }

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

        $is_use_reward = $request->use_reward;

        $used_reward_amount = $total_amount;

        if($used_reward_amount > $reward_amount){
            $used_reward_amount = $reward_amount;
        }

        if($is_use_reward && $reward_amount > 0){
            $total_amount = $total_amount - $reward_amount;
        }


        if($total_amount <=0){
            $total_amount = 0;
        }

        $provices = [];

        if(empty($address)){
            $provices = PositionService::provices();
        }

        $view = view('checkout.viprupgrade',[
            'title' => '开通vip',
            'description' => '',
            'keywords' => '',
            'user' => $user,
            'r_user' => $r_user,
            'uid' => $uid,
            'order_no' => $order_no,
            'address' => $address,
            'product' => $product,
            'gift' => $gift,
            'total_amount' => $total_amount,
            'is_use_integral' => $is_use_integral,
            'sub_integral_amount' => $sub_integral_amount,
            'sub_integral_amount_use' => $sub_integral_amount_use,
            'product_sku_id' => $product_sku_id,
            'is_use_reward' => $is_use_reward,
            'used_reward_amount' => $used_reward_amount,
            'reward_amount' => $reward_amount,
            'provices' => $provices
        ]);
        return $view;
    }

    /**
     * 积分充值
     *
     * @return void
    */
    public function integral(Request $request)
    {
        $user = Auth::user();
        $total_amount = 168;
        $order_no = $request->order_no;
        if(!empty($order_no)){
            $order = OrderRecharge::where('order_no', '=', $order_no)->where('order_type', 'integral')->where('status', '=', '2')->first();
            if($order != null){
                return redirect(\Helper::route('checkout_integral_success', [$order_no]));
            }
        }
        $vip_date = $user->vip_end_date;
        $vip_type = $request->vip_type;
        if(!$vip_date || !$user->is_vip){
            $vip_type == 'open';
        }
        if($user->is_vip && $vip_type == 'open'){
            return redirect(\Helper::route('account_index'));
        }
        $view = view('checkout.integral',[
            'title' => '充值有赏积分',
            'description' => '',
            'keywords' => '',
            'user' => $user,
            'total_amount' => $total_amount,
            'order_no' => $order_no
        ]);
        return $view;
    }

    /**
     * 积分充值成功
     *
     * @return void
    */
    public function integralSuccess(Request $request, $order_no)
    {
        $user = Auth::user();
        $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->first();
        $view = view('checkout.integral_success',[
            'title' => '支付成功',
            'description' => '',
            'keywords' => '',
            'user' => $user,
            'order' => $order
        ]);
        return $view;
    }

     /**
     * 名片续费
     *
     * @return \Illuminate\Http\Response
     */
    public function cardRenewal(Request $request)
    {
        $user = Auth::user();
        $total_amount = 68;
        $order_no = $request->order_no;
        if(!empty($order_no)){
            $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->where('order_type', 'card_renewal')->first();
            if($order != null){
                return redirect(\Helper::route('checkout_card_renewal_success', [$order_no]));
            }
        }
        $view = view('checkout.card_renewal',[
            'user' => $user,
            'title' => '名片续费',
            'total_amount' => $total_amount,
            'order_no' => $order_no
        ]);
        return $view;
    }

     /**
     * 名片续费成功
     *
     * @return void
    */
    public function cardRenewalSuccess(Request $request, $order_no)
    {
        $user = Auth::user();
        $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->where('order_type', 'card_renewal')->first();
        $view = view('checkout.card_renewal_success',[
            'title' => '支付成功',
            'description' => '',
            'keywords' => '',
            'user' => $user,
            'order' => $order
        ]);
        return $view;
    }

     /**
     * 开通续费店铺
     *
     * @return void
    */
    public function store(Request $request, Application $app)
    {
        $user = Auth::user();
        if(empty($user)){
            return redirect(\Helper::route('auth_login', ['login']));
        }
        $user_id = $user->id;
        $store_package = StorePackageModel::where('enable', '=', '1')->first();
        $total_amount = $store_package['amount'];
        $order_no = $request->order_no;
        if(!empty($order_no)){
            $order = OrderRecharge::where('order_no', '=', $order_no)
            ->where('status', '=', '2')->where('order_type', 'store')->first();
            if($order != null){
                return redirect(\Helper::route('checkout_store_success', [$order_no]));
            } else{
                $response = $app->payment->query($order_no);
                if ($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
                    if ($response['trade_state'] == 'SUCCESS') {
                        OrderRechargeService::storeOrderPay($OrderRecharge);
                        return redirect(\Helper::route('checkout_store_success', [$order_no]));
                    }
                }
            }
        }
        $date = date('Y-m-d H:i:s');
        $expire_date = null;
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if(empty($store) || $store['expire_date'] == null){
            $type = 'open';
        } else {
            $type = 'renewal';
        }
        if(!empty($store) && $store->expire_date){
            $expire_date = \Helper::diffBetweenTwoDays($date, $store->expire_date); 
        }
        $store_agreement = HelpCache::get('store_agreement');
         //获取用户默认选择地址
        $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
        if($address == null){
            //如果用户没有设置默认地址,获取第一条
            $address = AddressModel::where('user_id', '=', $user_id)->first();
        }
        $gift = GiftModel::where('gift_type', 'store')->first();
        $product = null;
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $product_sku = ProductCache::defaultSKU($product);
                $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                $product['sku'] = $product_sku;
                $product = $product->toArray();
            }
        }
        $view = view('checkout.store',[
            'title' => $type == 'renewal' ? '续约店铺' : '开通店铺',
            'store_package' => $store_package,
            'user' => $user,
            'store' => $store,
            'total_amount' => $total_amount,
            'expire_date' => $expire_date,
            'order_no' => $order_no,
            'store_agreement' => $store_agreement,
            'address' => $address,
            'product' => $product,
            'gift' => $gift
        ]);
        return $view;
    }

    /**
     * 名片续费成功
     *
     * @return void
    */
    public function storeSuccess(Request $request, $order_no)
    {
        $user = Auth::user();
        $order = OrderRecharge::where('order_no', '=', $order_no)->where('status', '=', '2')->where('order_type', 'store')->first();
        $view = view('checkout.store_success',[
            'title' => '支付成功',
            'user' => $user,
            'order' => $order
        ]);
        return $view;
    }
}