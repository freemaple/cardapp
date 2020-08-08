<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\Order\Order as OrderModel;  
use Validator;
use Helper;

class ReviewsController extends BaseController
{
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

        $order = OrderModel::where('id', $order_id)->where('user_id', $user_id)->first();

        if($order == null){

        }

        $order_reviews = $order->reviews()->get();

        foreach ($order_reviews as $okey => $oi) {
            $order_product = $oi->orderProduct()->select('order_product.id as order_product_id', 'order_product.spec','order_product.quantity','order_product.price', 'order_product.sku_id', 'product_sku.image')
            ->leftjoin('product_sku', 'product_sku.id', '=', 'order_product.sku_id')
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

        return view('account.order.reviews')->with([
            'title' => '订单评论',
            'order_reviews' => $order_reviews
        ]);
    }

    /**
     * 添加订单评论
     *
     * @return \Illuminate\Http\Response
     */
    public function addOrderReviews(Request $request, $order_id)
    {
        $message = '';

        $order_product = [];

        //登录验证
        $user = \Auth::user();

        $order = OrderModel::where('id', $order_id)->where('user_id', $user->id)->first();

        if($order == null){
            return redirect(Helper::route('account_orders'));
        }

        if($order['order_status_code'] != 'finished'){
            return redirect(Helper::route('account_orders'));
        }

        $order_reviews = $order->reviews()->get();
        //如果订单已经评论过,跳转到评论详情页
        if(count($order_reviews) > 0){
            return redirect(Helper::route('account_order_reviews', $order_id));
        }
        $order_products = $order->products()->get();
        foreach ($order_products as $okey => $order_product) {
            $order_product->order_product_id = $order_product->id;
            $is_self = 0;
            $product = $order_product->product()->first();
            if($product['is_self']){
                $is_self = 1;
            }
            $order_product_sku = $order_product->sku()->first();
            $image = !empty($order_product_sku) ? $order_product_sku['image'] : '';
            $image = \HelperImage::storagePath($image);
            $order_product->image = $image;
            $order_product->is_self = $is_self;
        }
        if($order_products != null){
            $order_products = $order_products->toArray();
        }
        return view('account.order.reviews_add')->with([
            'title' => '评论',
            'message' => $message,
            'order_id' => $order_id,
            'order_products' => $order_products
        ]);
    }
}
