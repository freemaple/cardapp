<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Models\User\User as UserModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Order\Order as OrderModel; 
use App\Models\Order\OrderRefund as OrderRefundModel;
use App\Models\Order\OrderProduct as OrderProductModel;
use App\Models\Order\OrderAccountRecord;
use Auth;
use Session;
use Helper;
use App\Libs\Service\ProductCategoryService;
use App\Libs\Service\ProductDispalyService;
use App\Libs\Service\StoreService;
use App\Libs\Service\CardService;
use App\Cache\Help as HelpCache;
use App\Models\Order\Reviews as OrderReviews;  
use App\Cache\ShippingMethod as ShippingMethodCache;
use App\Libs\Service\PositionService;

class StoreController extends BaseController
{

    /**
     * 个人中心
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $date = date('Y-m-d H:i:s');

        $store = StoreModel::where('user_id', '=', $user->id)->first();

        $expire_date = 0;

        $gift_date = 0;

        if($store != null && $store->expire_date){
            $expire_date = Helper::diffBetweenTwoDays($date, $store->expire_date);
        }
        $store_expire_tip = false;
        if($store != null && $store->expire_date){
            if($expire_date <= 0){
                $store_expire_tip = true;
            }
        }

        if($request->to_product == '1' && !$store_expire_tip && $store['status'] == '2'){
            return redirect(Helper::route('account_store_products'));
        }

        $store_status = config('store.status');

        $now = date('Y-m-d H:i:s');

        //显示在前端的状态
        $order_status_list = config('order.account_store_show_status');

        //显示在前端的状态
        $level_text = config('store.level_text');

        if(empty($store)){
            $serviceList = [[
                'name'=> 'store',
                'icon' => 'icon-dianpu',
                'desc'=> '开通店铺',
                'url'=> \Helper::route('account_vipUpgrade')
            ]];
            $serviceList[] = [
                'name'=> 'agreement',
                'icon' => 'icon-edit',
                'desc'=> '店铺协议',
                'url'=> \Helper::route('help_view', ['store_agreement'])
            ];
        } else {
            $serviceList = [[
                'name'=> 'shangjia',
                'icon' => 'icon-shangjia',
                'desc'=> '产品管理',
                'url'=> \Helper::route('account_store_products')
            ],[
                'name'=> 'renew',
                'icon' => 'icon-renew',
                'desc'=> '续约店铺',
                'is_vip' => '1',
                'url'=> \Helper::route('account_vipUpgrade')
            ],[
                'name'=> 'setting',
                'icon'=> 'icon-setting',
                'desc'=> '店铺认证',
                'url'=> \Helper::route('account_store_info')
            ]];
        }

        $serviceList[] =  [
            'name'=> 'xuexi',
            'icon' => 'icon-xuexi',
            'desc'=> '商学院',
            'url'=> \Helper::route('help_catalog_doc', ['store'])
        ];

        
        $view = view('account.store.index',[
            'user' => $user,
            'title' => '店铺管理中心',
            'expire_date' => $expire_date,
            'store_expire_tip' => $store_expire_tip,
            'store' => $store,
            'gift_date' => $gift_date,
            'order_status_list' => $order_status_list,
            'level_text' => $level_text,
            'serviceList' => $serviceList
        ]);
        return $view;
    }

    /**
     * 店铺信息
     *
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request)
    {
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store != null){
            $certificate_image = $store->certificateImage()->get();
        } else {
            $certificate_image = [];
        }
        $is_recert = $request->is_recert;
        $store_edit = true;
        if(!empty($store)){
            if($store['status'] == '1' || $store['status'] == '2'){
                $store_edit = false;
            }
        }
        if($is_recert == '1'){
            $store_edit = true;
        }
        $store_cert = HelpCache::get('store_cert');
        $store_agreement = HelpCache::get('store_agreement');

        $provices = PositionService::provices();

        $citys = [];

        $countys = [];

        $towns = [];
        
        $villages = [];

        if(!empty($store)){
            $provice_id = $store['provice_id'];
            $citys = PositionService::getCity($provice_id);

            $city_id = $store['city_id'];
            $countys = PositionService::getCounty($city_id);

            $district_id = $store['district_id'];
            $towns = PositionService::getTown($district_id);

            $town_id = $store['town_id'];
            $villages = PositionService::getVillage($town_id);
        }

        $view = view('account.store.info',[
            'user' => $user,
            'title' => '店铺身份信息',
            'user' => $user,
            'store' => $store,
            'certificate_image' => $certificate_image,
            'store_edit' => $store_edit,
            'store_cert' => $store_cert,
            'store_agreement' => $store_agreement,
            'provices' => $provices,
            'citys' => $citys,
            'countys' => $countys,
            'towns' => $towns,
            'villages' => $villages,
            'is_recert' => $is_recert
        ]);
        return $view;
    }

    public function products(Request $request){
        $user = Auth::user();

        $date = date('Y-m-d H:i:s');

        $store = StoreModel::where('user_id', '=', $user->id)->first();

        if($store == null){
            return redirect(Helper::route('account_vipUpgrade'));
        }

        $expire_date = 0;

        if($store != null && $store->expire_date){
            $expire_date = Helper::diffBetweenTwoDays($date, $store->expire_date);
        }
        $store_expire_tip = false;
        if($store != null && $store->expire_date){
            if($expire_date <= 0){
                $store_expire_tip = true;
            }
        }

        if($store['status'] != '2'){
            return redirect(Helper::route('account_store_info'))->with('message', '店铺认证还未审核通过！');
        }
        $expire_date = $store->expire_date;
        $date = date('Y-m-d H:i:s');
        if($expire_date == null || $date > $expire_date){
            return redirect(Helper::route('account_vipUpgrade'));
        }

        $store_status = config('store.status');

        $products = [];

        $pager = null;

        if($store != null){

            $products = StoreService::getStoreProduct($store, '0', 0, 10);

            $products->appends($request->all());

            $pager = $products->links();

        }

        $now = date('Y-m-d H:i:s');

        //显示在前端的状态
        $level_text = config('store.level_text');

        
        $view = view('account.store.products',[
            'user' => $user,
            'title' => '店铺产品中心',
            'expire_date' => $expire_date,
            'store_expire_tip' => $store_expire_tip,
            'store' => $store,
            'products' => $products,
            'pager' => $pager,
            'level_text' => $level_text,
        ]);
        return $view;
    }

    /**
     * 店铺信息
     *
     * @return \Illuminate\Http\Response
     */
    public function addProduct()
    {
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            return redirect(Helper::route('account_vipUpgrade'));
        }
        if($store['status'] != '2'){
            return redirect(Helper::route('account_store'))->with('message', '店铺认证还未审核通过！');
        }
        $expire_date = $store->expire_date;
        $date = date('Y-m-d H:i:s');
        if($expire_date == null || $date > $expire_date){
            return redirect(Helper::route('account_vipUpgrade'));
        }
        $product_count = ProductModel::where('user_id', $user['id'])->count();
        if($product_count > 1000){
            return redirect(Helper::route('account_store'))->with('message', '店铺最多上传1000个产品');
        }
        $categorys = ProductCategoryService::getInstance()->getTopCategoryList();
        $view = view('account.store.edit_product',[
            'user' => $user,
            'title' => '添加产品',
            'user' => $user,
            'store' => $store,
            'categorys' => $categorys,
            'type' => 'add'
        ]);
        return $view;
    }

    /**
     * 编辑产品
     *
     * @return \Illuminate\Http\Response
     */
    public function editProduct(Request $request, $id)
    {
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            return redirect(Helper::route('account_vipUpgrade'));
        }
        if($store['status'] != '2'){
            return redirect(Helper::route('account_store'))->with('message', '店铺认证还未审核通过！');
        }
        $product = ProductModel::where('id', $id)->where('user_id', $user->id)->first();
        if($product == null){
            return redirect(Helper::route('account_store'))->with('message', '店铺产品不存在！');
        }
        $product_images = $product->images()->get();
        $product_skus = $product->skus()
        ->where('deleted', '!=', '1')
        ->get();
        $attribute = $product->attribute()->select('product_attribute.*', 'option.name as option_name', 'option.description as option_description')
        ->join('option', 'option.id', '=', 'product_attribute.option_id')
         ->where('product_attribute.deleted', '!=', '1')
        ->get();
        foreach ($product_skus as $key => $sku) {
            $image = !empty($sku['image']) ? \HelperImage::storagePath($sku['image']) : '';
            $product_skus[$key]['image_link'] = $image;
            foreach ($attribute as $key => $attribute_item) {
               if($attribute_item['product_sku_id'] == $sku->id){
                  $sku['attribute'][$attribute_item['option_name']] = $attribute_item;
               }
            }
        }
        $attribute_option = [];
        foreach ($attribute as $key => $attribute_item) {
            if(!in_array($attribute_item['option_name'], $attribute_option)){
                $attribute_option[] = $attribute_item['option_name'];
            }
        }
        $product_skus = $product_skus->toArray();
        $categorys = ProductCategoryService::getInstance()->getTopCategoryList();
        $view = view('account.store.edit_product', [
            'user' => $user,
            'title' => '编辑产品',
            'user' => $user,
            'product' => $product,
            'product_images' => $product_images,
            'product_skus' => $product_skus,
            'store' => $store,
            'categorys' => $categorys,
            'attribute_option' => $attribute_option,
            'type' => 'edit'
        ]);
        return $view;
    }

    /**
     * 个人中心
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $id)
    {
        $store = StoreModel::where('id', '=', $id)->first();
        if($store == null){
            return redirect(Helper::route('home'));
        }
        $expire_date = $store->expire_date;
        $date = date('Y-m-d H:i:s');
        $store_enable = 0;
        if($store['status'] == '2'){
            if($expire_date != null && $date <= $expire_date){
                $store_enable = 1;
            }
        }
        $products = [];
        if($store != null){
            $products = $products = StoreService::getStoreProduct($store, '1');
        }
        $user = Auth::user();
        $share_data = [
            'title' => $store['name'],
            'content' => $store['name'],
            'url' => \Helper::route('store_view', [$store['id']]),
            'image' => empty($store['banner']) ? \Helper::asset_url('/media/images/default_store_banner.png') 
                    : \HelperImage::storagePath($store['banner'])
        ];
        $self_products = ProductDispalyService::getShareProduct(0,100);
        $is_viewd = 1;
        if(empty($store) || !$store_enable){
            $is_viewd = 0;
        }
        if(!empty($store) && !empty($user) && $user['id'] == $store['user_id']){
            $is_viewd = 0;
        }
        $view = view('store.view', [
            'title' => $store['name'],
            'user' => $user,
            'store' => $store,
            'store_enable' => $store_enable,
            'products' => $products,
            'self_products' => $self_products,
            'share_data' => $share_data,
            'is_viewd' => $is_viewd
        ]);
        return $view;
    }

     /**
     * 个人中心
     *
     * @return \Illuminate\Http\Response
     */
    public function storeinfo(Request $request, $id)
    {
        $store = StoreModel::where('id', '=', $id)->first();
        if($store == null){
            return redirect(Helper::route('home'));
        }
        $expire_date = $store->expire_date;
        $date = date('Y-m-d H:i:s');
        $store_enable = 0;
        if($store['status'] == '2'){
            if($expire_date != null && $date <= $expire_date){
                $store_enable = 1;
            }
        }
        $store_user = UserModel::where('id', $store['user_id'])->first();
        $certificateImage = $store->certificateImage()->get();
        foreach($certificateImage as $ckey => $cImage){
            $certificateImage->image = \HelperImage::storagePath($cImage['image']);
        }
        $certificateImage = $certificateImage->toArray();
        $store = $store->toArray();
        $card = CardService::getDefaultCard($store['user_id']);
        if($store['rating'] == '0'){
            $store['rating'] = 5;
        }
        $view = view('store.info',[
            'title' => $store['name'],
            'store' => $store,
            'store_enable' => $store_enable,
            'store_user' => $store_user,
            'certificateImage' => $certificateImage,
            'card' => $card
        ]);
        return $view;
    }


     /**
     * 浏览记录
     * @param  Request $request 
     * @return string           
     */
    public function viewed(Request $request){
        $user = Auth::user();
        $data = $request->all();
        //数据校验
        $validator = \Validator::make($data, [
            'id' => 'required',
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->id;
        $store = StoreModel::where('id', '=', $id)->first();
        if($store == null){
            $result['code'] = "no_exits";
            $result['message'] = '';
            return $result;
        }
        if(!empty($user) && $store['user_id'] == $user->id){
            $result['code'] = "self_store";
            $result['message'] = '';
            return $result;
        }
        if($store != null){
            $pkey = "store_view:" . $id;
            $store_view = \Cookie::get($pkey, '');
            if($store_view == null){
                $store->view_number = $store->view_number + 1;
                $store->save();
                \Cookie::queue($pkey, 1, 0.5);
            }
        }
        $result['code'] = "Success";
        $result['message'] = 'Success';
        return $result;
    }

    public function orders(Request $request){

        $orders = [];

        $user = Auth::user();

        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            return redirect(Helper::route('account_vipUpgrade'));
        }
    
        $user_id = $user->id;

        $pageSize = config('paginate.orders_list', 50);

        $OrderModel = OrderModel::where('seller_id', $user_id);

        $status_code = $request->status_code;

        if($status_code == 'complete') {
            $OrderModel->where('order_status_code', '=', 'finished');
        } if($status_code == 'review'){
            $OrderModel->where('order_status_code', '=', 'finished');
            $OrderModel->where('is_review', '=', '0');
        } 
        else {
            if($status_code != null){
                $OrderModel->where('order_status_code', '=', $status_code);
            }
        }

        //获取用户订单列表
        $orders = $OrderModel->orderBy("id", "desc")->paginate($pageSize);

        $orders->appends($request->all());

        $pager = '';

        if(count($orders) > 0){
            $pager = $orders->links();
        }

        foreach ($orders as $key => $order) {
            if($order['order_status_code'] == 'shipped' || $order['order_status_code'] == 'finished'){
                $order_shipping = $order->shipping()->first();
                if($order_shipping != null){
                    $order_shipping = $order_shipping->toArray();
                }
                $order->shipping_info = $order_shipping;
            }
            $order_product = $order->products()->select('order_product.*')->first();
            if($order_product != null){
                $image = $order_product['image'];
                if(empty($image)){
                    $order_product_sku = $order_product->sku()->first();
                    $image = !empty($order_product_sku) ? $order_product_sku['image'] : '';
                }
                if(!empty($image)){
                    $image = \HelperImage::storagePath($image);
                }
                $order_product->image = $image;
                $order_product = $order_product->toArray();
            }
            $order->product = $order_product;

            if(in_array($order['order_status_code'], ['shipping', 'shipped'])){
                $refund = OrderRefundModel::where('order_id', $order->id)->whereIn('status', ['0', '1', '2'])
                 ->orderBy('id', 'desc')
                ->first();
                if($refund != null){
                    $refund = $refund->toArray();
                }
                $order->refund = $refund;
            }
            
        }

        //订单状态
        $order_status = config('order.status');

        //显示在前端的状态
        $do_show_status = config('store.do_show_order_status');

        $form = ['status_code' => ''];

        $filter = $request->all();

        $form = array_merge($form, $filter);

        $shipping_method = ShippingMethodCache::get();

        return view('account.store.order.index')->with([
            'title' => '订单列表',
            'orders' => $orders,
            'order_status' => $order_status,
            'do_show_status' => $do_show_status,
            'form' => $form,
            'shipping_method' => $shipping_method,
            'pager' => $pager
        ]);
    }

    /**
     * 订单详情
     *
     * @return \Illuminate\Http\Response
     */
    public function orderDetail(Request $request, $number)
    {
        $user = Auth::user();
        $order = OrderModel::where('order_no', $number)->first();
        if($order == null){
            return redirect(\Helper::route('account_store_orders'));
        }
        $order_share_amount = 0;
        $order_products = $order->products()->get();
        foreach ($order_products as $okey => $order_product) {
            $image = $order_product['image'];
            if(empty($image)){
                $order_product_sku = $order_product->sku()->first();
                $image = !empty($order_product_sku) ? $order_product_sku['image'] : '';
            }
            $image = \HelperImage::storagePath($image);
            $order_product->image = $image;
            $price = $order_product['price'];
            $share_integral_amount = $order_product['share_integral_amount'];
            if($share_integral_amount > $price){
                $share_integral_amount = $price;
            }
            $quantity = $order_product['quantity'];
            $order_share_amount += $order_product['share_integral_amount'] * $quantity;
        }
        if($order_products != null){
            $order_products = $order_products->toArray();
        }
        $order['order_share_amount'] = $order_share_amount;
        $order['order_products'] = $order_products;
        $order_userinfo = $order->userinfo()->first();
        if($order_userinfo != null){
            $order_userinfo = $order_userinfo->toArray();
        }
        $order['user_info'] = $order_userinfo;

        $order_shipping = $order->shipping()->first();

        if($order_shipping != null){
            $order_shipping = $order_shipping->toArray();
        }

        $order['shipping_info'] = $order_shipping;

        $shipping_method = [];

        if($order['order_status_code'] == 'shipping'){
            $shipping_method = ShippingMethodCache::get();
        }

        $account_record = OrderAccountRecord::where('order_id', $order->id)->first();

        $order['account_record'] = $account_record;

        $refund = OrderRefundModel::where('order_id', $order->id)->whereIn('status', ['0', '1', '2'])
        ->orderBy('id', 'desc')
        ->first();
        if($refund != null){
            $refund = $refund->toArray();
        }
        $order['refund'] = $refund;


        //显示在前端的状态
        $order_status = config('order.status');
        
        $view = view('account.store.order.detail',[
            'user' => $user,
            'title' => '订单支付',
            'order_detail' => $order,
            'order_status' => $order_status,
            'shipping_method' => $shipping_method
        ]);
        return $view;
    }

    /**
     * 获取订单评论
     *
     * @return \Illuminate\Http\Response
     */
    public function findOrderReviews(Request $request, $order_id)
    {

        $order_reviews = [];

        //登录验证
        $session_user = \Auth::user();

        $user_id = $session_user->id;

        $order = OrderModel::where('id', $order_id)->where('seller_id', $user_id)->first();

        if($order == null){
            return redirect(\Helper::route('account_store_orders'));
        }

        $order_reviews = $order->reviews()->get();

        foreach ($order_reviews as $okey => $oi) {
            $order_product = $oi->orderProduct()->select('order_product.id as order_product_id', 'order_product.spec','order_product.quantity','order_product.price', 'order_product.sku_id', 'order_product.image')
            ->first();
            if($order_product != null){
                $order_product = $order_product->toArray();
                $oi->sku_spec = $order_product['spec'];
                $oi->sku_price = $order_product['price'];
            }
            $product = $oi->product()->first();
            if($product != null){
                $product = $product->toArray();
            }
            if($product != null && $order_product != null){
                $oi->sku_image = \HelperImage::storagePath($order_product['image']);
            }
            $oi->product_info = $product;
            $oi->product_name = isset($product['name']) ? $product['name'] : '';
        }

        $order_reviews = $order_reviews->toArray();

        return view('account.store.order.reviews')->with([
            'title' => '订单评论',
            'order_reviews' => $order_reviews
        ]);
    }

    /**
     * 订单详情
     *
     * @return \Illuminate\Http\Response
     */
    public function orderRefundList(Request $request)
    {
        $order_id = $request->order_id;
        $user = Auth::user();
        $user_id = $user->id;
        $pageSize = 2;
        $order_refunds = OrderRefundModel::select('order_refund.*', 'order.order_no', 'order.order_total', 'order.order_status_code', 'order.order_item_qty')
        ->where('order.seller_id', $user_id);
        if($order_id){
            $order_refunds = $order_refunds->where('order_id', $order_id);
        }
        $order_refunds =  $order_refunds ->join('order', 'order.id', 'order_refund.order_id')
        ->orderBy('id', 'desc')
        ->paginate($pageSize);

        $order_refunds->appends($request->all());

        $pager = '';

        if(count($order_refunds) > 0){
            $pager = $order_refunds->links();
        }

         foreach ($order_refunds as $key => $o_refunds) {
            $order_product = OrderProductModel::select('order_product.*')
            ->where('order_id', '=', $o_refunds['order_id'])->first();
            if($order_product != null){
                $image = !empty($order_product) ? $order_product['image'] : '';
                $image = \HelperImage::storagePath($image);
                $order_product->image = $image;
                $order_product = $order_product->toArray();
            }
            $o_refunds->product = $order_product;
        }

        //显示在前端的状态
        $refund_status = config('order.refund_status');
        
        $view = view('account.store.order.refund',[
            'user' => $user,
            'title' => '退换货申请',
            'order_refunds' => $order_refunds,
            'refund_status' => $refund_status,
            'pager' => $pager
        ]);
        return $view;
    }
}
