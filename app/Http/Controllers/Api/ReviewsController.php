<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\ReviewsService;
use App\Models\Order\Order as OrderModel; 
use App\Models\Order\Reviews as OrderReviews;  
use App\Models\Product\Product as ProductModel;
use App\Models\Store\Store as StoreModel;
use Validator;

class ReviewsController extends BaseController
{

    /**
     * 添加订单评论
     *
     * @return \Illuminate\Http\Response
     */
    public function addOrderReviews(Request $request, $order_id)
    {
        $result = ['code' => 'UNAUTH', 'message' => trans('api.message.unauth'), 'data' => []];

        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }

        if(empty($order_id)){
            $order_id = $request->order_id;
        }

        //验证数据
        $validator = Validator::make(['order_id' => $order_id], [
            'order_id' => 'required|int|min:1'
        ]);

        if($validator->fails()){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = '无效的数据';
            return response()->json($result);
        }

        $user_id = $session_user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', $user_id)->first();

        if($order == null){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = '订单不存在！';
            return response()->json($result);
        }

        if($order['order_status_code'] != 'finished'){
            return redirect(Helper::route('account_orders'));
            $result['code'] = 'NOT_ALLOWED_TO_COMMENT';
            $result['message'] = '订单未完成，不能评论！';
            return response()->json($result);
        }

        $order_reviews = $order->reviews()->get();
        //如果订单已经评论过,跳转到评论详情页
        if(count($order_reviews) > 0){
            $result['code'] = 'HAS_BEEN_COMMENT';
            $result['message'] = '订单已评论过！';
        }

        //添加评论
        $request_all = $request->all();

        $order_product = $order->products()->get();
        $order_product_ids = [];
        if(!empty($order_product)){
            foreach($order_product as $op_key => $op_val){
                $order_product_ids[] = $op_val['id'];
            }
        }

        try{
            $return = \DB::transaction(function () use ($user_id, $order_id, $request_all, $order_product_ids, $order){
                $is_review = false;
                foreach($request_all['review_text'] as $k => $v){
                    $data = [];
                    if(in_array($request_all['order_product_id'][$k], $order_product_ids)){
                        $data['user_id'] = $user_id;
                        $data['order_id'] = $order_id;
                        $data['product_id'] = isset($request_all['product_id'][$k]) ? $request_all['product_id'][$k] : '';
                        $data['order_product_id'] = isset($request_all['order_product_id'][$k]) ? $request_all['order_product_id'][$k] : '';
                        $data['review_text'] = $v;
                        $data['review_rate'] = isset($request_all['review_rate'][$k]) ? $request_all['review_rate'][$k] : '';
                        $order_review = OrderReviews::where('order_id', '=', $order_id)->where('order_product_id', '=', $data['order_product_id'])->first();
                        if($order_review == null){
                            $OrderReviews = new OrderReviews();
                            $OrderReviews->product_id = $data['product_id'];
                            $OrderReviews->order_id = $order_id;
                            $OrderReviews->user_id = $user_id;
                            $OrderReviews->order_product_id = $data['order_product_id'];
                            $OrderReviews->review_text = $data['review_text'];
                            $OrderReviews->review_rate = $data['review_rate'];
                            $res = $OrderReviews->save();
                            if($res){
                                $is_review = true;
                            }
                        }
                        $product = ProductModel::where('id', $data['product_id'])->first();
                        if($product != null && !$product['is_self']){
                            $store = StoreModel::where('user_id', $order['seller_id'])->first();
                            if($store != null){
                                $store->rating_honor = $store->rating_honor + $data['review_rate'];
                                $store_product_rating = OrderReviews::join('order', 'order.id', '=', 'order_reviews.order_id')
                                ->where('seller_id', '=', $store['user_id'])
                                ->avg('review_rate');
                                $store->rating = $store_product_rating;
                                $store->save();
                            }
                        }
                        if($product != null){
                            $product_rating = OrderReviews::where('product_id', $product['id'])
                            ->avg('review_rate');
                            $product->rating = $product_rating;
                            $product->save();
                        }
                    }
                }
                $order_reviews = $order->reviews()->get();
                if(count($order_reviews) > 0){
                    $order->is_review = 1;
                    $order->save();
                }
                return true;
            });
        }catch(\Exception $e){
            $result = ['code' => 'SYSTEM_ERROR', 'message' => $e->getMessage()];
            return response()->json($result);
        }            
        $result['code'] = 'Success';
        $result['message'] = '评论成功！';
        return response()->json($result);
    }

     /**
     * 添加订单评论
     *
     * @return \Illuminate\Http\Response
     */
    public function addAppOrderReviews(Request $request)
    {
        $result = ['code' => 'UNAUTH', 'message' => trans('api.message.unauth'), 'data' => []];

        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }

        $order_id = $request->order_id;

        //验证数据
        $validator = Validator::make(['order_id' => $order_id], [
            'order_id' => 'required|int|min:1'
        ]);

        if($validator->fails()){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = '无效的数据';
            return response()->json($result);
        }

        $user_id = $session_user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', $user_id)->first();

        if($order == null){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = '订单不存在！';
            return response()->json($result);
        }

        if($order['order_status_code'] != 'finished'){
            return redirect(Helper::route('account_orders'));
            $result['code'] = 'NOT_ALLOWED_TO_COMMENT';
            $result['message'] = '订单未完成，不能评论！';
            return response()->json($result);
        }

        $order_reviews = $order->reviews()->get();
        //如果订单已经评论过,跳转到评论详情页
        if(count($order_reviews) > 0){
            $result['code'] = 'HAS_BEEN_COMMENT';
            $result['message'] = '订单已评论过！';
        }

        //添加评论
        $request_all = $request->all();

        $order_product = $order->products()->get();
        $order_product_ids = [];
        if(!empty($order_product)){
            foreach($order_product as $op_key => $op_val){
                $order_product_ids[] = $op_val['id'];
            }
        }

        $reviews =  json_decode($request->review, true);

        //dd($reviews);

        try{
            $return = \DB::transaction(function () use ($user_id, $order_id, $reviews, $order_product_ids, $order){
                $is_review = false;
                foreach($reviews as $k => $v){
                    $data = [];
                    if(in_array($v['order_product_id'], $order_product_ids)){
                        $data['user_id'] = $user_id;
                        $data['order_id'] = $order_id;
                        $data['product_id'] = isset($v['product_id']) ? $v['product_id'] : '';
                        $data['order_product_id'] = isset($v['order_product_id']) ? $v['order_product_id'] : '';
                        $data['review_text'] = $v['review_text'];
                        $data['review_rate'] = isset($v['score']) ? $v['score'] : '1';
                        $order_review = OrderReviews::where('order_id', '=', $order_id)->where('order_product_id', '=', $data['order_product_id'])->first();
                        if($order_review == null){
                            $OrderReviews = new OrderReviews();
                            $OrderReviews->product_id = $data['product_id'];
                            $OrderReviews->order_id = $order_id;
                            $OrderReviews->user_id = $user_id;
                            $OrderReviews->order_product_id = $data['order_product_id'];
                            $OrderReviews->review_text = $data['review_text'];
                            $OrderReviews->review_rate = $data['review_rate'];
                            $res = $OrderReviews->save();
                            if($res){
                                $is_review = true;
                            }
                        }
                        $product = ProductModel::where('id', $data['product_id'])->first();
                        if($product != null && !$product['is_self']){
                            $store = StoreModel::where('user_id', $order['seller_id'])->first();
                            if($store != null){
                                $store->rating_honor = $store->rating_honor + $data['review_rate'];
                                $store_product_rating = OrderReviews::join('order', 'order.id', '=', 'order_reviews.order_id')
                                ->where('seller_id', '=', $store['user_id'])
                                ->avg('review_rate');
                                $store->rating = $store_product_rating;
                                $store->save();
                            }
                        }
                        if($product != null){
                            $product_rating = OrderReviews::where('product_id', $product['id'])
                            ->avg('review_rate');
                            $product->rating = $product_rating;
                            $product->save();
                        }
                    }
                }
                $order_reviews = $order->reviews()->get();
                if(count($order_reviews) > 0){
                    $order->is_review = 1;
                    $order->save();
                }
                return true;
            });
        }catch(\Exception $e){
            $result = ['code' => 'SYSTEM_ERROR', 'message' => $e->getMessage()];
            return response()->json($result);
        }            
        $result['code'] = 'Success';
        $result['message'] = '评论成功！';
        return response()->json($result);
    }

     /**
     * 获取订单评论
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrderReviews(Request $request)
    {
      
        $result = ['code' => '2x1', 'message' => '', 'data' => []];

        $order_id = $request->order_id;

        $order_reviews = [];

        //登录验证
        $session_user = \Auth::user();

        $user_id = $session_user->id;

        $order = OrderModel::where('id', $order_id)->where('user_id', $user_id)->first();

        if($order == null){
            $result['message'] = '订单不存在';
            return response()->json($result);
        }

        $order_reviews = $order->reviews()->get();

        foreach ($order_reviews as $okey => $oi) {
            $order_product = $oi->orderProduct()->select('order_product.id as order_product_id','product_id', 'spec','quantity','price', 'sku_id', 'image', 'product_name')
            ->first();
            if($order_product != null){
                $order_product = $order_product->toArray();
                $oi->product_id = $order_product['product_id'];
                $oi->sku_spec = $order_product['spec'];
                $oi->sku_price = $order_product['price'];
                $oi->product_name = $order_product['product_name'];
                $oi->sku_image = \HelperImage::storagePath($order_product['image']);
            }
        }

        $order_reviews = $order_reviews->toArray();

        $result['code'] = 'Success';

        $result['data'] = ['comments' => $order_reviews];

        return response()->json($result);
    }

}
