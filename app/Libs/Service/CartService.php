<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use DB;
use App\Models\User\User as UserModel;
use App\Models\Product\Sku as ProductSkuModel; 
use App\Models\Product\Product as ProductModel;
use App\Models\Product\Attribute as ProductAttributeModel;
use App\Models\Store\StoreProduct as StoreProductModel;

class CartService
{
    /**
     *
     */
    public static function getCartData($request){

    	$result = [];        

        //产品SKU
        $sku_ids = $request->sku_id;

        if(!is_array($sku_ids)){
            $sku_ids = [];
            $sku_ids[] = $request->sku_id;
        }

        //SKU数量
        $qtys = $request->qtys;

        if(!is_array($qtys)){
            $qtys = [];
            $qtys[] = $request->qtys;
        }

        $products = [];

        $subtotal = 0;

        $order_item_qty = 0;

        $shipping_amount = 0;

        $sid = $request->sid;

        $share_user_id = 0;

        if($sid){
            $share_user = UserModel::where('u_id', '=', $sid)->first();
            if($share_user != null){
                $share_user_id = $share_user->id;
            }
        }

        $buyer = \Auth::user();

        if($buyer != null && $buyer->id == $share_user_id){
            $share_user_id = 0;
        }

        foreach ($sku_ids as $skey => $sku) {
            $quantity = $qtys[$skey];
            //检查产品和sku是否存在
            $sku = ProductSkuModel::where('product_sku.id', '=', $sku)
            ->where('deleted', '0')->where('is_sale', '1')
            ->first();
            if($sku == null){
            	$result['message'] = '呀，此规格产品已下架';
            	$result['status'] = '0';
                return $result;
            }
            //检查产品和sku是否存在
            $product = ProductModel::where('id', '=', $sku['product_id'])
            ->where('deleted', '0')->where('is_sale', '1')
            ->first();
            if($product == null){
                $result['message'] = '呀，此产品已下架！';
                $result['status'] = '0';
                return $result;
            }
            if($sku['stock'] == 0){
                $result['message'] = '呀，此产品已售罄！';
                $result['status'] = '0';
                return $result;
            }
            if($quantity > $sku['stock']){
                $result['message'] = '哎呀,产品库存数量只剩余' . $sku['stock'];
                $result['status'] = '0';
                return $result;
            }
            $product = ProductModel::where('id', $sku['product_id'])
            ->where('deleted', '0')->where('is_sale', '1')->first();
            //检查产品是否存在
            if($product == null){
            	$result['message'] = '产品不存在';
            	$result['status'] = '0';
                return $result;
            }
            if($product['is_self']){
                $seller_id = 0;
            } else {
                $seller_id = $product['user_id'];
            }
            if($seller_id > 0 && $buyer['id'] == $seller_id){
                $result['message'] = '您不能购买自己店铺产品！';
                $result['status'] = '0';
                return $result;
            }
            $spec = ProductDispalyService::findProductSkuSpc($sku);

            $store_id = 0;

            if(!$product['is_self']){
                $store = StoreProductModel::where('product_id', $product['id'])->first();
                if($store != null){
                    $store_id = $store['id'];
                }
            }
           
            $share_integral_amount = $sku['share_integral'];
            $share_integral_amount = $share_integral_amount > $sku['price'] ? $sku['price'] : $share_integral_amount;
            $products[] = [
                'is_self' => $product['is_self'],
                'spu' => $product['spu'],
                'product_name' => $product['name'],
                'product_id' => $product['id'],
                'sku_id' => $sku['id'],
                'sku' => $sku['sku'],
                'spec' => $spec,
                'image' => $sku['image'],
                'quantity' => $quantity,
                'price' => $sku['price'],
                'share_integral_amount' => $share_integral_amount,
                'seller_id' => $seller_id,
                'store_id' => $store_id,
                'share_user_id' => $share_user_id ? $share_user_id : 0
            ];
            $subtotal += $sku['price'] * $quantity;
            $order_item_qty += $quantity;
            if($sku['shipping'] > 0){
                $shipping_amount += $sku['shipping'] * $quantity;
            }
        }

        $product_data = [
            'order_item_qty' => $order_item_qty,
            'subtotal_amount' => $subtotal,
            'shipping_amount' => $shipping_amount,
            'products' => $products,
        ];
        $result['status'] = '1';
        $result['data'] = $product_data;
        return $result;
    }
}   