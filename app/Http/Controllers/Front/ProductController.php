<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Libs\Service\ProductDispalyService;
use App\Libs\Service\StoreService;
use App\Libs\Service\UserService;
use App\Models\Product\Product as ProductModel;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Product\Wish as ProductWishModel;
use App\Cache\Product as ProductCache;


class ProductController extends BaseController
{
    /**
     * 文章
     *
     * @return void
    */
    public function view(Request $request, $id)
    {
        $goods_enable = true;
        $goods_detail = ProductCache::productView($id);

        if($goods_detail == null){
            $goods_enable = false;
        }
        if($goods_detail['is_sale'] != '1'){
            $goods_enable = false;
        }
        if($goods_detail['deleted'] == '1'){
            $goods_enable = false;
        }
        if($goods_detail['enable'] != '1'){
            $goods_enable = false;
        }
        if($goods_detail['is_gift'] == '1'){
            $goods_enable = false;
        }
        if(!$goods_enable){
            $view = view('product.unfound',[
                'goods_detail' => $goods_detail,
            ]);
            return $view;
        }
        $store_link = '';
        if($goods_detail['is_self'] != '1'){
            $user_id = $goods_detail['user_id'];
            $store = StoreModel::where('user_id', $user_id)->first();
            if($store != null){
                $store_isvisable = StoreService::isVisable($store);
                if(!$store_isvisable){
                    $view = view('product.unfound',[
                        'goods_detail' => $goods_detail,
                    ]);
                    return $view;
                }
                $store = $store->toArray();
            }
            $goods_detail['store'] = $store;
            $store_link = \Helper::route('store_view', [$store['id']]);
        } else {
            $store_link = \Helper::route('shop');
        }
        $goods_detail['store_link'] = $store_link;
        //$reviews_total = ProductDispalyService::getProductReviewCount($id);
        //$goods_detail['reviews_total'] = $reviews_total;
        //$reviews = ProductDispalyService::getProductReview($id, 2);
        //$goods_detail['reviews'] = $reviews;
        $user = Auth::user();
        $share_data = [
            'title' => $goods_detail['name'],
            'content' => $goods_detail['name'],
            'url' => \Helper::route('product_view', [$goods_detail['id']]),
            'image' => isset($goods_detail['main_image']) ? $goods_detail['main_image'] : ''
        ];


        if(!empty($user)){
            $sid = $user->u_id;
            $share_data['content'] =  $share_data['content'];
            $share_data['url'] = \Helper::route('product_view', [$goods_detail['id'], 'sid' => $sid]);
        }
        
        $is_wish = false;
        if(!empty($user)){
            $user_id = $user->id;
            $wish_product = ProductWishModel::where('product_id', $id)
            ->where('user_id', $user_id)->first();
            if($wish_product != null){
                $is_wish = true;
            }
        }

        $sid = $request->sid;

        //微信朋友圈
        if(\Helper::isWeixin() && $request->from == 'timeline'){
            $sid = $request->sid;
            UserService::getInstance()->shareDate($sid);
        }
        
        $view = view('product.view',[
            'goods_detail' => $goods_detail,
            'share_data' => $share_data,
            'is_wish' => $is_wish,
            'sid' => $sid
        ]);
        return $view;
    }

     /**
     * 浏览记录
     * @param  Request $request 
     * @return string           
     */
    public function viewed(Request $request){
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
        $product = ProductModel::where('id', '=', $id)->first();
        if($product == null){
            $result['code'] = "no_exits";
            $result['message'] = '';
            return $result;
        }
        if($product != null){
            $pkey = "product_view:" . $id;
            $product_view = \Cookie::get($pkey, '');
            if($product_view == null){
                $product->view_number = $product->view_number + 1;
                $product->save();
                $store_product = StoreProductModel::where('product_id', $product->id)->first();
                if($store_product != null){
                    $store = StoreModel::where('id', $store_product['store_id'])->first();
                    if($store != null){
                        $store->view_number = $store->view_number + 1;
                        $store->save();
                    }
                }
                \Cookie::queue($pkey, 1, 0.5);
            }
        }
        $result['code'] = "Success";
        $result['message'] = 'Success';
        return $result;
    }
    
    /**
     * 产品评论列表
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function reviews(Request $request, $id){
        $product = ProductModel::where('id', $id)->first();
        if($product == null){
            return redirect(\Helper::route('home'));
        }
        $reviews = ProductDispalyService::getProductReview($id, 100, true);
        $reviews = $reviews->toArray();
        $view = view('product.reviews',[
            'product' => $product,
            'title' => $product['name'] . '-评论',
            'reviews' => $reviews
        ]);
        return $view;
    }
}