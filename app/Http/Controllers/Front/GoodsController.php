<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Auth;
use App\Libs\Service\ProductDispalyService;
use App\Models\Product\Product as ProductModel;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Models\Store\Store as StoreModel;

class GoodsController extends BaseController
{
    /**
     * 产品
     *
     * @return void
    */
    public function view(Request $request, $id)
    {
        $goods_detail = ProductDispalyService::getStoreProduct($id);
        if($goods_detail == null){
            //返回视图
            $view = view('product.no_exits');
            $view->with('title', '产品不存在或者已经下架');
            return $view;
        }
        $view = view('goods.view',[
            'title' => $goods_detail['name'],
            'goods_detail' => $goods_detail
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
}