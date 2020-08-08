<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use Auth;
use App\Models\Product\Product as ProductModel;
use App\Models\Product\Wish as ProductWishModel;
use App\Models\Product\Reserve as ProductReserveModel;
use App\Models\User\User as UserModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Libs\Service\MessageService;
use App\Libs\Service\ProductDispalyService;
use App\Libs\Service\ProductService;
use App\Libs\Service\StoreService;
use App\Cache\Product as ProductCache;


class ProductController extends BaseController
{
	
    /**
     * 商品
     *
     * @return void
    */
    public function detail(Request $request)
    {
        $id = $request->goods_id;
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
        if(!$goods_enable){
            $data = [];
            $result = ['code' => 'Success', 'data' => $data];
            return response()->json($result);
        }
        $store_link = '';
        if($goods_detail['is_self'] != '1'){
            $user_id = $goods_detail['user_id'];
            $store = StoreModel::where('user_id', $user_id)->first();
            if($store != null){
                $store_isvisable = StoreService::isVisable($store);
                if(!$store_isvisable){
                    $data = [];
                    $result = ['code' => 'Success', 'data' => $data];
                    return response()->json($result);
                }
                $store = $store->toArray();
            }
            $goods_detail['store'] = $store;
            $store_link = \Helper::route('store_view', [$store['id']]);
        } else {
            $store_link = \Helper::route('shop');
        }
        $goods_detail['store_link'] = $store_link;
        $reviews_total = ProductDispalyService::getProductReviewCount($id);
        $goods_detail['reviews_total'] = $reviews_total;
        $reviews = ProductDispalyService::getProductReview($id, 2);
        $goods_detail['reviews'] = $reviews;
        $share_data = [
            'product_id' => $id,
            'title' => $goods_detail['name'],
            'content' => $goods_detail['name'],
            'href' => \Helper::route('product_view', [$goods_detail['id']]),
            'image' => isset($goods_detail['main_image']) ? $goods_detail['main_image'] : ''
        ];
        $user = Auth::user();

        if(!empty($user)){
            $sid = $user->u_id;
            $share_data['content'] = $share_data['content'];
            $share_data['href'] = \Helper::route('product_view', [$goods_detail['id'], 'sid' => $sid]);
        }
        
        $is_wish = false;
        if(!empty($user)){
            $user_id = $user->id;
            $wish_product = ProductWishModel::where('product_id', $id)
            ->where('user_id', $user_id)->first();
            if($wish_product != null){
                $is_wish = true;
            }
            if($user->is_vip == '1'){
                $goods_detail['share_text'] = '自购/分享挣￥' . 
                $goods_detail['min_share_integral'] .  '~' . $goods_detail['max_share_integral'] . '红包';
            }
        }

        unset($goods_detail['admin_id']);

        $goods_detail['is_wish'] = $is_wish;

        $goods_detail['share_data'] = $share_data;

        $goods_detail['attribute'] = array_values($goods_detail['attribute']);

        $goods_detail['currency_text'] = '￥';

        $data = [
            'goods_detail' =>  $goods_detail
        ];

        $result = ['code' => 'Success', 'data' => $data];
        
        return response()->json($result);
    }


    /**
     * 预约
     * @param  Request $request 
     * @return string           
     */
    public function reserve(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'fullname' => 'required|max:64',
            'phone' => 'required',
            'city' => 'required',
            'district' => 'required'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = '信息不完整！';
            return $result;
        }
    	$user = \Auth::user();
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        $store_user = UserModel::where('id', $product['user_id'])->first();
        $store = null;
        if($store_user != null){
            $store = StoreModel::where('user_id', $store_user['id'])->where('status', '1')->first();
        }
        if($product == null){
            $result['code'] = "0x00x1";
            $result['message'] = '产品不存在！';
            return $result;
        }
        $data = [
            'user_id' => !empty($user) ? $user->id : 0,
            'product_id' => $product_id,
            'fullname' => trim($request->fullname),
            'phone' => trim($request->phone),
            'city' => trim($request->city),
            'district' => trim($request->district),
            'address' => trim($request->address),
            'comments' => trim($request->comments)
        ];
        //登录事务处理
        $return_result = \DB::transaction(function() use ($store_user, $product, $data, $store) {
            $ProductReserveModel = new ProductReserveModel();
            foreach ($data as $key => $value) {
                $ProductReserveModel->$key = $value;
            }
            $res = $ProductReserveModel->save();
            if($res){
                $product->reserve_number = $product->reserve_number + 1;
                $product->save();
                if($store != null){
                    $store->reserve_number = $store->reserve_number + 1;
                    $store->save();
                }
            }
            if($store_user != null){
                $data = [
                    'user_id' => $store_user->id,
                    'name' => "店铺-客户提交预约了解申请",
                    'content' => '禀奏皇上，客户：' . $data['fullname'] . '(' . $data['phone'] . ')' . ',想购买您的产品: ' . 
                        $product['name'] . '，提交了预约了解申请，请您马上联系客户拿下此单！误失此良机！'
                ];
                MessageService::insert($data);
            }
            $result = [];
            $result['code'] = 'Success';
            $result['message'] = '预约申请已提交！';
            return $result;
        });
       
    	return response()->json($return_result);
    }

      /**
     * 启用禁用产品
     * @param  Request $request 
     * @return string           
     */
    public function enableProduct(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'product_id' => 'required|max:64',
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = '信息不完整！';
            return $result;
        }
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['code'] = "0x00x1";
            $result['message'] = '产品不存在！';
            return $result;
        }
        $enable = $request->enable;
        if($enable == '1'){
            $product->enable = '1';
            $res = $product->save();
            if($res){
                $result['message'] = '产品已上架！';
            }
        }
        else if($enable == '0'){
            $product->enable = '0';
            $res = $product->save();
            if($res){
                $result['message'] = '产品已下架！';
            }
        }
        $result['code'] = "Success";
        return response()->json($result);
    }

     /**
     * 删除产品
     * @param  Request $request 
     * @return string           
     */
    public function deleteProduct(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'product_id' => 'required',
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = '信息不完整！';
            return $result;
        }
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['code'] = "0x00x1";
            $result['message'] = '产品不存在！';
            return $result;
        }
        $product->deleted = '1';
        $res = $product->save();
        if($res){
            $result['message'] = '产品已删除！';
        }
        $result['code'] = "Success";
        return response()->json($result);
    }

    public function reviewsBox(Request $request){
        $result = ['message' => '', 'code' => '2x1'];
        $id = $request->product_id;
        $product = ProductModel::where('id', $id)->first();
        if($product == null){
            return response()->json($result);
        }
        $goods_detail = ['id' => $id];
        $reviews_total = ProductDispalyService::getProductReviewCount($id);
        $goods_detail['reviews_total'] = $reviews_total;
        $reviews = [];
        if($reviews_total > 0){
            $reviews = ProductDispalyService::getProductReview($id, 2);
        }
        $goods_detail['reviews'] = $reviews;
        $view = view('product.block.review_box', ['goods_detail' => $goods_detail])->render();
        $result['code'] = 'Success';
        $result['view'] = $view;
        $result['data'] = [];
        return response()->json($result);
    }

    public function reviews(Request $request){
        $result = ['message' => '', 'code' => '2x1'];
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            return response()->json($result);
        }
        $pageSize = 100;
        $reviews = ProductDispalyService::getProductReview($product_id, $pageSize, true);
        $reviews = $reviews->toArray();
        if($request->type == 'app'){
            $result['data'] = $reviews;
        } else {
            $view = view('product.block.review_list', ['review_list' => $reviews['data']])->render();
            $result['view'] = $view;
            $result['data'] = [];
        }
        $result['code'] = 'Success';
      
        return response()->json($result);
    }

    public function wish(Request $request){
        $user = \Auth::user();
        $result = ['message' => '', 'code' => '2x1'];
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['message'] = '产品不存在！';
            return response()->json($result);
        }
        $user_id = $user->id;
        $is_wish = $request->is_wish;
        $ProductWishModel = ProductWishModel::where('product_id', $product_id)->where('user_id', $user_id)->first();
        if($is_wish == '1'){
            if($ProductWishModel == null){
                $ProductWishModel = new ProductWishModel();
                $ProductWishModel->user_id = $user_id;
                $ProductWishModel->product_id = $product_id;
                $ProductWishModel->save();
                $product->wish_number = $product->wish_number + 1;
                $product->save();
                if($product['is_self'] != '1'){
                    $seller_id = $product['user_id'];
                    $store = StoreModel::where('user_id', '=', $seller_id)->first();
                    $store->wish_number = $store->wish_number + 1;
                    $store->save();
                }
            }
            $result['code'] = 'Success';
            $result['message'] = '恭喜,产品已收藏！';
            return response()->json($result);
        }
        
        if($is_wish == '0'){
            if($ProductWishModel != null){
                $ProductWishModel->delete();
                $product->wish_number = $product->wish_number - 1;
                $product->save();
                if($product['is_self'] != '1'){
                    $seller_id = $product['user_id'];
                    $store = StoreModel::where('user_id', '=', $seller_id)->first();
                    $store->wish_number = $store->wish_number - 1;
                    $store->save();
                }
            }
            $result['code'] = 'Success';
            $result['message'] = '产品已取消收藏！';
            return response()->json($result);
        }
        return response()->json($result);
    } 

    public function removeWish(Request $request){
        $user = \Auth::user();
        $result = ['message' => '', 'code' => '2x1'];
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['message'] = '产品不存在！';
            return response()->json($result);
        }
        $user_id = $user->id;
        $ProductWishModel = ProductWishModel::where('product_id', $product_id)->where('user_id', $user_id)->first();
        
        if($ProductWishModel != null){
            $ProductWishModel->delete();
            $product->wish_number = $product->wish_number - 1;
            $product->save();
            if($product['is_self'] != '1'){
                $seller_id = $product['user_id'];
                $store = StoreModel::where('user_id', '=', $seller_id)->first();
                if($store != null){
                    $store->wish_number = $store->wish_number - 1;
                    $store->save();
                }
            }
        }
        $result['code'] = 'Success';
        $result['message'] = '产品已取消收藏！';
        return response()->json($result);
    } 

    //店铺同产品
    public function getStoreProduct(Request $request){
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['message'] = '产品不存在！';
            return response()->json($result);
        }
        $products = [];
        if($product['is_self'] == '1'){
            $products = ProductDispalyService::getSelfProduct(100);
        } else {
            $seller_id = $product['user_id'];
            $store = StoreModel::where('user_id', '=', $seller_id)->first();
            if($store != null){
                $isVisable = StoreService::isVisable($store);
                if($isVisable){
                    $products = StoreService::getStoreProduct($store, 1, $product_id);
                }
            }
        }
        $view = view('shop.block.products', ['products' => $products])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
        $result['data'] = [];
        return response()->json($result);
    }

    //店铺同产品
    public function getSelfProduct(Request $request){
        $products = ProductDispalyService::getSelfProduct(100);
        $view = view('shop.block.products', ['products' => $products])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
        $result['data'] = [];
        return response()->json($result);
    }

    //店铺同产品
    public function productCodeImage(Request $request){
        $result = [];
        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['message'] = '产品不存在！';
            return response()->json($result);
        }
        $image = ProductService::getInstance()->productCodeImage($product);
        $result = ['code' => 'Success'];
        $result['data'] = ['image' => $image];
        return response()->json($result);
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
        }
        $result['code'] = "Success";
        $result['message'] = 'Success';
        return $result;
    }
}
