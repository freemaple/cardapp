<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Libs\Service\OrderRechargeService;
use App\Models\Order\Recharge as OrderRecharge;  
use App\Models\Order\Order as OrderModel; 
use App\Models\User\VipPackage as VipPackageModel;
use App\Models\Product\Product as ProductModel;
use App\Models\User\Address as AddressModel;
use App\Models\Store\StorePackage as StorePackageModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Gift\Gift as GiftModel;
use App\Libs\Service\ProductDispalyService;
use App\Cache\Checkout as CheckoutCache;
use EasyWeChat\Foundation\Application;
use App\Cache\Help as HelpCache;
use App\Libs\Service\PositionService;
use App\Libs\Service\CartService;
use App\Cache\Product as ProductCache;

class CheckoutController extends BaseController
{


    public function check(Request $request){

        $result = ['code' => '2x1'];

        //产品ID
        $goods_id = $request->goods_id;        

        //产品SKU
        $goods_sku_id = $request->goods_sku_id;

        //SKU数量
        $qty = $request->qty;

        //验证数据
        $validator = Validator::make(['goods_id' => $goods_id,  'goods_sku_id' => $goods_sku_id, 'qty' => $qty], [
            'goods_id' => 'required|int|min:1',
            'goods_sku_id' => 'required|string',
            'qty' => 'required|int|min:1'
        ]);
        
        if($validator->fails()){
            $result['message'] = 'The data format is incorrect.';
            return json_encode($result);
        }

        $product = ProductModel::where('id', $goods_id)->where('deleted', '0')->where('is_sale', '1')->first();

        //检查产品是否存在
        if($product == null){
            $result['message'] = 'product does not exist.';
            return json_encode($result);
        }

        //检查产品和sku是否存在
        $sku = $product->skus()->where('product_sku.id', '=', $goods_sku_id)->where('deleted', '0')->where('is_sale', '1')->first();
        if($sku == null){
            $result['message'] = '此产品不存在！';
            return json_encode($result);
        }

        if($sku['stock'] == 0 ){
            $result['message'] = '此产品已售罄！';
            return json_encode($result);
        }
        else if($qty > $sku['stock']){
            $qty = $sku['stock'];
            $stock = $sku['stock'];
            $result['message'] = '此产品剩余库存 $stock';
            return json_encode($result);
        }
        $result['code'] = 'SUCCESS';
        return json_encode($result);
    }
    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = ['code' => '2x1'];
        //登录验证
        $session_user = \Auth::user();

        //获取用户ID
        $user_id = $session_user->id;

        //产品ID
        $goods_id = $request->goods_id;        

        //产品SKU
        $goods_sku_id = $request->goods_sku_id;

        //SKU数量
        $qty = $request->qty;

        $type = $request->type;

        $productList = [];

        $total_data = [];

        if($type == 'cart'){
            $cart_ids = $request->cart_ids;
            $cart_ids = explode(',', $cart_ids);
            $cart_data = CartService::getCartProduct($cart_ids);
            $productList = $cart_data['data']['products'];
            $total_data = $cart_data['data']['total_data'];

        } else {

            $total_amount = 0;

            $subtotal_amount = 0;

             //验证数据
            $validator = Validator::make(['goods_id' => $goods_id,  'goods_sku_id' => $goods_sku_id, 'qty' => $qty], [
                'goods_id' => 'required|int|min:1',
                'goods_sku_id' => 'required|string',
                'qty' => 'required|int|min:1'
            ]);
            
            if($validator->fails()){
                $result['message'] = 'The data format is incorrect.';
                return json_encode($result);
            }

            $product = ProductModel::where('id', $goods_id)->where('deleted', '0')->where('is_sale', '1')->first();

            //检查产品是否存在
            if($product == null){
                $result['message'] = 'product does not exist.';
                return json_encode($result);
            }

            //检查产品和sku是否存在
            $sku = $product->skus()->where('product_sku.id', '=', $goods_sku_id)->where('deleted', '0')->where('is_sale', '1')->first();
            if($sku == null){
                $result['message'] = '此产品不存在！';
                return json_encode($result);
            }

            if($sku['stock'] == 0 ){
                $result['message'] = '此产品已售罄！';
                return json_encode($result);
            }
            else if($qty > $sku['stock']){
                $qty = $sku['stock'];
                $stock = $sku['stock'];
                $result['message'] = '此产品剩余库存 $stock';
                return json_encode($result);
            }

            $sku['imageUrl'] = \HelperImage::storagePath($sku->image);

            $spec = ProductDispalyService::findProductSkuSpc($sku);

            $sku = $sku->toArray();

            $sku['spec'] = $spec;

            $item = [
                'is_self' => $product['is_self'],
                'spu' => $product['spu'],
                'product_name' => $product['name'],
                'product_id' => $product['id'],
                'sku_id' => $sku['id'],
                'sku' => $sku['sku'],
                'spec' => $spec,
                'image' => $sku['image'],
                'imageUrl' => $sku['imageUrl'],
                'quantity' => $qty,
                'price' => $sku['price'],
                'price_text' => '￥' . $sku['price'],
                'seller_id' => $product['user_id'],
                'store_id' => 0
            ];
            $subtotal_amount += $sku['price'] * $qty;
            $productList[] = $item;
            $shipping_amount = $sku['shipping'] * $qty;

            $total_data =  [
                'order_item_qty' => $qty,
                'subtotal_amount' => $subtotal_amount,
                'subtotal_amount_text' => \HelperCurrency::fixed($subtotal_amount),
                'shipping_amount' => $shipping_amount,
                'shipping_amount_text' => \HelperCurrency::fixed($shipping_amount),
            ];
        }

        //SKU数量
        $address_id = $request->address_id;

        if(!empty($address_id)){
            $address = AddressModel::where('user_id', '=', $user_id)->where('id', '=', $address_id)->first();
        }

        if(empty($address_id) || empty($address)){
            //获取用户默认选择地址
            $address = AddressModel::where('user_id', '=', $user_id)->where('is_default', '=', '1')->first();
            if($address == null){
                //如果用户没有设置默认地址,获取第一条
                $address = AddressModel::where('user_id', '=', $user_id)->first();
            }
        }

        $provices = PositionService::provices();

        // 拼装数据
        $data['products'] = $productList;
        $data['address'] = $address;

        $total_amount = $total_data['subtotal_amount'] + $total_data['shipping_amount'];

        $integral_amount = 0;

        $can_integral_amount = 0;

        $is_use_integral = $request->is_use_integral;

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

        if($is_use_integral == '1' && $can_integral_amount > 0){
            $total_amount = $total_amount - $can_integral_amount;
            $total_data['integral_amount'] = $can_integral_amount;
        } else {
            $total_data['integral_amount'] = 0;
        }

        $total_data['integral_amount_text'] = \HelperCurrency::fixed($total_data['integral_amount']);

        $total_data['total_amount'] = $total_amount;

        $total_data['total_amount_text'] = \HelperCurrency::fixed($total_amount);

        $data['amount'] = $total_data;

        $data['integral']['integral_amount'] = $integral_amount;

        $data['integral']['integral_amount_text'] = \HelperCurrency::fixed($integral_amount);

        $data['integral']['can_integral_amount'] = $can_integral_amount;

        $data['integral']['can_integral_amount_text'] = \HelperCurrency::fixed($can_integral_amount);

        $data['integral']['is_use_integral'] = $is_use_integral;

        $data['currency'] = [
            'text' => '￥'
        ];

        $checkout_data['checkout_data'] = $data;

        $checkout_data['provices'] = $provices;

        $result['data'] = $checkout_data;

        $result['code'] = 'Success';

        return json_encode($result);
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
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $product_sku = ProductCache::defaultSKU($product);
                $product_sku['image'] = \HelperImage::storagePath($product_sku['image']);
                $product['sku'] = $product_sku;
                $product = $product->toArray();
            }
        }
        $result['data'] = [
            'user' => $user,
            'total_amount' => $total_amount,
            'order_no' => $order_no,
            'address' => $address,
            'product' => $product,
            'gift' => $gift
            
        ];

        $result['code'] = 'Success';

        return json_encode($result);
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
        if($gift != null){
            $product = ProductModel::where('id', $gift->product_id)->first();
            if($product != null){
                $product_sku = ProductCache::defaultSKU($product);
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

        $result['data'] = [
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
            'sub_integral_amount_use' => $sub_integral_amount_use
        ];

        $result['code'] = 'Success';

        return json_encode($result);
    }
}