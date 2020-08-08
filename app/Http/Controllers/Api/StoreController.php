<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Auth;
use Session;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Product\Sku as ProductSkuModel;
use App\Models\Product\Attribute as ProductAttributeModel;
use App\Models\Product\Option as OptionModel;
use App\Models\Product\Image as ProductImageModel;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Models\Store\StoreCertificateImage as StoreCertificateImageModel;
use App\Models\Order\Order as OrderModel;
use App\Models\Order\OrderRefund as OrderRefundModel;
use App\Models\Position\Provice as ProviceModel;
use App\Models\Position\City as CityModel;
use App\Models\Position\County as CountyModel;
use App\Models\Position\Town as TownModel;
use App\Models\Position\Village as VillageModel;
use App\Models\Product\ShareApply as ShareApplyModel;
use EasyWeChat\Foundation\Application;

use App\Libs\Service\OrderService;
use App\Libs\Service\StoreService;
use App\Libs\Service\ProductDispalyService;

use App\Libraries\Storage\Store as StoreStorage;
use App\Cache\Product as ProductCache;

class StoreController extends BaseController
{

    /**
     * 个人中心
     *
     * @return \Illuminate\Http\Response
     */
    public function getMyStoreInfo(Request $request)
    {
        $user = Auth::user();

        $date = date('Y-m-d H:i:s');

        $store = StoreModel::where('user_id', '=', $user->id)->first();

        $expire_date = 0;

        $gift_date = 0;

        if($user['is_vip'] && $user['level_status'] >=1){
            if(empty($store) || $store->expire_date == null){
                $gift_date = 1;
                $expire_date = 31;
            }
        }

        if(!empty($store) && $store['is_pay'] == 0){
            $gift_date = 1;
        }

        if($store != null && $store->expire_date){
            $expire_date = Helper::diffBetweenTwoDays($date, $store->expire_date);
        }
        $store_expire_tip = false;
        if($store != null && $store->expire_date){
            if($expire_date <= 0){
                $store_expire_tip = true;
            }
        }

        $store_status = config('store.status');

         //显示在前端的状态
        $level_text = config('store.level_text');

        $store['gift_date'] = $gift_date;

        $store['expire_day'] = $expire_date;

        $store['store_expire_tip'] = $store_expire_tip;

        $store['banner'] = \HelperImage::storagePath($store['banner']);

        $store['level_text'] = $level_text[$user['store_level']];

        $now = date("Y-m-d 00:00:00");

        $next_day = date("Y-m-d 00:00:00",strtotime("+1 day"));

        $day_order_count = OrderModel::where('seller_id', $user->id)
        ->where('created_at', '>=', $now)
        ->where('created_at', '<', $next_day)
        ->count();

        $store['day_order_count'] = $day_order_count;
        
        $data = [
            'title' => '店铺管理中心',
            'store' => $store,
            'level_text' => $level_text
        ];
        $result = ['code' => 'Success', 'data' => $data];
        return response()->json($result);
    }


	
    /**
     * 用户基本信息修改
     * @param  Request $request 
     * @return string           
     */
    public function saveInfo(Request $request){
        $result = ['code' => "2x1"];
        set_time_limit(0);
        $data = $request->all();
        //数据校验
        $validator = \Validator::make($data, [
            'name' => 'required',
            'business_entity_name' => 'required',
            'contact_user_name' => 'required',
            'contact_phone' => 'required',
            'provice_id' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'address' => 'required',
            'business_entity_name' => 'required',
            'description' => 'required'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['message'] = '请填完整信息！';
            return response()->json($result);
        }
    	$user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            $result['message'] = '请先开通店铺！';
            return response()->json($result);
        }
        //身份证
        $id_card = trim($request->id_card);
        if($store == null){
            $store = new StoreModel();
            $store->user_id = $user->id;
        }
        if(empty($store['id_card'])){
            if(empty($id_card)){
                $result['message'] = '请输入身份证号!';
                return response()->json($result);
            }
        }

        if(!empty($id_card)){
            $id_card_store = StoreModel::where('id_card', '=', $id_card)->first();
            if($id_card_store != null && (!empty($store) && $id_card_store['id'] != $store->id)){
                $result['message'] = '此身份证号已经申请过店铺!';
                return response()->json($result);
            }
            $store->id_card = $id_card;
        }

        //店铺名称
        $store->name = trim($request->name);

        //主营主体
        $store->business_entity_name = trim($request->business_entity_name);


        //身份证
        $store->id_card = trim($request->id_card);

        //联系人
        $store->contact_user_name = trim($request->contact_user_name);
        //联系电话
        $store->contact_phone = trim($request->contact_phone);

        $provice_id = trim($request->provice_id);

        $city_id = trim($request->city_id);

        $district_id = trim($request->district_id);

        $town_id = trim($request->town_id);

        $village_id = trim($request->village_id);

        //地址省
        $store->provice_id = trim($request->provice_id);
        //地址城市
        $store->city_id = trim($request->city_id);
        //地址城市
        $store->district_id = trim($request->district_id);

        $store->town_id = trim($request->town_id);

        $store->village_id = trim($request->village_id);

        //地址城市
        $store->town = trim($request->town_id);

        //地址城市
        $store->village = trim($request->village_id);

        $province = ProviceModel::where('provice_id', $provice_id)->first();
        $store->provice = $province['provice_name'];

        $city = CityModel::where('city_id', $city_id)->first();
        $store->city = $city['city_name'];

        $district = CountyModel::where('county_id', $district_id)->first();
        $store->district = $district['county_name'];

        $town = TownModel::where('town_id', $town_id)->first();
        $store->town = $town['town_name'];

        $town = VillageModel::where('village_id', $village_id)->first();
        $store->village = $town['village_name'];

        //地址城市
        $store->address = trim($request->address);

        $store->description = trim($request->description);

        if(empty($store['business_license_front'])){
            if(!isset($request->business_license_front)){
                $result['message'] = '营业执照是必须的';
                return response()->json($result);
            }
        }
        if(empty($store['id_card_front'])){
            if(!isset($request->id_card_front)){
                $result['message'] = '身份证正面照是必须的';
                return response()->json($result);
            }
        }

        if(empty($store['id_card_back'])){
            if(!isset($request->id_card_back)){
                $result['message'] = '身份证反面照是必须的';
                return response()->json($result);
            }
        }

        if(isset($request->business_license_front)){
            //检查文件上传
            $blf_file = $request->file('business_license_front');
            if(!$blf_file || !$blf_file->isValid()){
                $result['message'] = 'This is not a valid image.';
                return response()->json($result);
            }
            //获取上传文件的大小
            $size = $blf_file->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 5*1024*1024){
                $result['message'] = '上传文件不能超过5M';
                return response()->json($result);
            }
            $storeStorage = new StoreStorage('store_certificate');
            $blf_filepath = $storeStorage->saveUpload($blf_file);
            $store->business_license_front = $blf_filepath;
        }

        if(isset($request->id_card_front)){
            //检查文件上传
            $id_front_file = $request->file('id_card_front');
            if(!$id_front_file || !$id_front_file->isValid()){
                $result['message'] = 'This is not a valid image.';
                return json_encode($result);
            }
            //获取上传文件的大小
            $size = $id_front_file->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 5*1024*1024){
                $result['message'] = '上传文件不能超过5M';
                return response()->json($result);
            }
            $storeStorage = new StoreStorage('store_certificate');
            $id_front_filepath = $storeStorage->saveUpload($id_front_file);
            $store->id_card_front = $id_front_filepath;
        }

        if(isset($request->id_card_back)){
             //检查文件上传
            $id_back_file = $request->file('id_card_back');
            if(!$id_back_file || !$id_back_file->isValid()){
                $result['message'] = 'This is not a valid image.';
                return response()->json($result);
            }
            //获取上传文件的大小
            $size = $id_back_file->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 5*1024*1024){
                $result['message'] = '上传文件不能超过5M';
                return response()->json($result);
            }
            $storeStorage = new StoreStorage('store_certificate');
            $id_back_filepath = $storeStorage->saveUpload($id_back_file);
            $store->id_card_back = $id_back_filepath;
        }

        $is_recert = $request->is_recert;

        $store->status = '1';

        $store->save();

        $image_files = $request->certificate_image;

        if(!empty($image_files)){
            foreach ($image_files as $key => $image_file) {
                $path = $image_file->path();
                $type = $image_file->getClientMimeType();
                list($width, $height, $type, $attr) = getimagesize($path);
                $StoreStorage = new StoreStorage('store_certificate');
                if($width > 2000){
                    $h = 2000 / $width * $height;
                    $img = \Image::make($image_file);
                    $filepath = 'store_certificate/' . md5(time()) . '.jpg';
                    $img = $img->resize(2000, $h)->save(storage_path() . '/app/static/' . $filepath);
                } else {
                    $filepath = $StoreStorage->saveUpload($image_file);
                }
                $StoreCertificateImageModel = new StoreCertificateImageModel();
                $StoreCertificateImageModel->store_id = $store->id;
                $StoreCertificateImageModel->user_id = $user->id;
                $StoreCertificateImageModel->image = $filepath;
                $StoreCertificateImageModel->save();
            }
        }

        $result = [];

        $result['code'] = 'Success';

        $result['message'] = '提交成功！';

    	return response()->json($result);
    }

    /**
     * 修改封面
     * @param  Request $request 
     * @return string
     */
    public function changeBanner(Request $request){
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            $result['code'] = '2xf';
            $result['message'] = '店铺不存在！';
            return response()->json($result);
        }
        //当前密码
        $result = [];
        //检查文件上传
        $file = $request->file('image');
        if(!$file || !$file->isValid()){
            $result['code'] = '2xf';
            $result['message'] = 'This is not a valid image.';
            return json_encode($result);
        }
        //获取上传文件的大小
        $size = $file->getSize();
        //这里可根据配置文件的设置，做得更灵活一点
        if($size > 3*1024*1024){
            $result['code'] = '2xf';
            $result['message'] = '上传文件不能超过3M';
            return response()->json($result);
        }
        $path = $file->path();
        $type = $file->getClientMimeType();
        list($width, $height, $type, $attr) = getimagesize($path);
        $storeStorage = new StoreStorage('store');
        if($width > 1024){
            $h = 1024 / $width * $height;
            $img = \Image::make($file);
            $filepath = 'store/' . md5(time()) . '.jpg';
            $img = $img->resize(1024, $h)->save(storage_path() . '/app/static/' . $filepath);
        } else {
            $filepath = $storeStorage->saveUpload($file);
        }
        $old_banner = $store->banner;
        $store->banner = $filepath;
        $res = $store->save();
        if($res && $store->banner != $old_banner){
            $storeStorage->deleteFile($old_banner);
        }
        $result['code'] = 'Success';
        $result['message'] = '保存成功';
        return response()->json($result);
    }

     /**
     * 添加产品
     * @param  Request $request 
     * @return string
     */
    public function addProduct(Request $request){
        set_time_limit(0);
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            $result['code'] = '2xf';
            $result['message'] = '店铺不存在！';
            return response()->json($result);
        }

        $skus = $request->skus;

        $skus = json_decode($skus, true);


        $f_price_shelf = !empty($skus) ? $skus[0]['price'] : 0;
        foreach ($skus as $skey => $sku) {
            if($sku['market_price'] <= 0){
                $result['code'] = '2x1';
                $result['message'] = "价格必须大于0";
                return $result;
            }
            if($sku['market_price'] <= $sku['price']){
                $result['code'] = '2x1';
                $result['message'] = "原价要大于活动价";
                return $result;
            }
            if($sku['share_integral'] < $sku['price'] * 0.02){
                $result['code'] = '2x1';
                $result['message'] = "共享积分不能小于活动价的2%";
                return $result;
            }
            if($sku['share_integral'] > $sku['price'] * 0.5){
                $result['code'] = '2x1';
                $result['message'] = "共享积分不能超过活动价的一半";
                return $result;
            }
            if($skey > 0){
                $f_min = $f_price_shelf * 0.2;
                $f_max = $f_price_shelf * 2;
                if($sku['price'] > $f_max){
                    $result['code'] = '2x1';
                    $result['message'] = "规格价格幅度太大受限,请检查";
                    return $result;
                }
                if($sku['price'] < $f_min){
                    $result['code'] = '2x1';
                    $result['message'] = "规格价格幅度太大受限,请检查";
                    return $result;
                }
            }
        }

        //登录事务处理
        $return_result = \DB::transaction(function() use ($user, $store, $request, $skus) {
            $user_id = $user->id;
            $product = new ProductModel();
            $product->user_id = $user_id;
            $product->name = $request->name;
            $product->market_price = $request->market_price;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->shop_link = $request->shop_link;
            $product->category_id = $request->category_id;
            $product->is_sale = '1';

            if(isset($request->integral_pay)){
                $product->integral_pay = $request->integral_pay == '1' ? '1' : '0';
            }

            $product->save();

            $StoreProductModel = new StoreProductModel();

            $StoreProductModel->store_id = $store->id;

            $StoreProductModel->product_id = $product->id;

            $image_files = $request->image;

            $StoreProductModel->save();

            $image_paths = [];

            if(!empty($image_files)){
                foreach ($image_files as $ikey => $image_file) {
                    $StoreStorage = new StoreStorage('product');
                    $filepath = $StoreStorage->saveUpload($image_file);
                    $image_paths[] = $filepath;
                    $ProductImageModel = new ProductImageModel();
                    $ProductImageModel->type = 'main';
                    $ProductImageModel->product_id = $product->id;
                    $ProductImageModel->user_id = $user_id;
                    $ProductImageModel->image = $filepath;
                    $ProductImageModel->save();
                }
            }

            $description_image_files = $request->description_image;

            if(!empty($description_image_files)){
               
                foreach ($description_image_files as $dkey => $description_image_file) {
                    $StoreStorage = new StoreStorage('product');
                    $filepath = $StoreStorage->saveUpload($description_image_file);
                    $ProductImageModel = new ProductImageModel();
                    $ProductImageModel->type = 'description';
                    $ProductImageModel->product_id = $product->id;
                    $ProductImageModel->user_id = $user_id;
                    $ProductImageModel->image = $filepath;
                    $ProductImageModel->save();
                }
            }

            $color_option = OptionModel::where('name', '=', 'color')->first();

            $size_option = OptionModel::where('name', '=', 'size')->first();

            foreach ($skus as $skey => $sku) {
                $ProductSkuModel = new ProductSkuModel();
                $ProductSkuModel->product_id = $product->id;
                $ProductSkuModel->price = $sku['price'];
                $ProductSkuModel->market_price = $sku['market_price'];
                $ProductSkuModel->share_integral = $sku['share_integral'];
                $ProductSkuModel->shipping = $sku['shipping'] > 0 ? $sku['shipping'] : 0;
                $ProductSkuModel->stock = $sku['stock'] > 0 ? $sku['stock'] : 0;
                $image = isset($sku['image_file']) && isset($image_paths[$sku['image_file']]) ? $image_paths[$sku['image_file']] : '';
                $ProductSkuModel->image = $image;
                $ProductSkuModel->price_shelf = $sku['price'];
                $ProductSkuModel->save();
                
                if(isset($sku['color'])){
                    $ProductAttributeModel = new ProductAttributeModel();
                    $ProductAttributeModel->product_id = $product->id;
                    $ProductAttributeModel->product_sku_id = $ProductSkuModel->id;
                    $ProductAttributeModel->option_id = $color_option->id;
                    $ProductAttributeModel->option_value = $sku['color'];
                    $ProductAttributeModel->save();
                }
                if(isset($sku['size'])){
                    $ProductAttributeModel = new ProductAttributeModel();
                    $ProductAttributeModel->product_id = $product->id;
                    $ProductAttributeModel->product_sku_id = $ProductSkuModel->id;
                    $ProductAttributeModel->option_id = $size_option->id;
                    $ProductAttributeModel->option_value = $sku['size'];
                    $ProductAttributeModel->save();
                }
            }

            $video = $request->file('video');
            if($video && $video->isValid()){
                //获取上传文件的大小
                $size = $video->getSize();
                if($size > 20*1024*1024){
                    $result['code'] = '2x1';
                    $result['message'] = '上传文件不能超过20M';
                    return $result;
                }

                $StoreStorage = new StoreStorage('product_video');
                $filepath = $StoreStorage->saveUpload($video);
                $product->video = $filepath;
                $product->save();
            }

            //ProductCache::clearDefaultSKUCache($product->id);
            
            ProductCache::clearProductCache($product->id);

            $result = [];
            $result['code'] = 'Success';
            $result['message'] = '保存成功';
            return $result;
        });
        return response()->json($return_result);
    }


    /**
     * 添加产品
     * @param  Request $request 
     * @return string
     */
    public function saveProduct(Request $request){
        set_time_limit(0);
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            $result['code'] = '2x1';
            $result['message'] = '店铺不存在！';
            return response()->json($result);
        }

        //登录事务处理
        $return_result = \DB::transaction(function() use ($user, $request) {
            $result = [];
            $product = ProductModel::where('id', $request->id)->first();
            if($product == null){
                $result['code'] = '2x1';
                $result['message'] = '店铺产品不存在！';
                return $result;
            }
            $skus = $request->skus;
            $skus = json_decode($skus, true);
            $f_price_shelf = 0;
            foreach ($skus as $skey => $sku) {
                if($sku['price'] <= 0){
                    $result['code'] = '2x1';
                    $result['message'] = "价格必须大于0";
                    return $result;
                }
                if($sku['market_price'] <= 0){
                    $result['code'] = '2x1';
                    $result['message'] = "价格必须大于0";
                    return $result;
                }
                if($sku['market_price'] <= $sku['price']){
                    $result['code'] = '2x1';
                    $result['message'] = "原价要大于活动价";
                    return $result;
                }
                if($sku['share_integral'] < $sku['price'] * 0.02){
                    $result['code'] = '2x1';
                    $result['message'] = "共享积分不能小于活动价的2%";
                    return $result;
                }
                if($sku['share_integral'] > $sku['price'] * 0.5){
                    $result['code'] = '2x1';
                    $result['message'] = "共享积分不能超过活动价的一半";
                    return $result;
                }
                $attribute = '';
                if(isset($sku['color'])){
                    $attribute = $sku['color'];
                }
                if(isset($sku['size'])){
                    $attribute .= " " . $sku['size'];
                }
                $sku_id = $sku['sku_id'];
                $ProductSkuModel =  ProductSkuModel::where('product_id', $product->id)->where('id', '=', $sku_id)->where('deleted', '!=', '1')->first();
                if($ProductSkuModel != null){
                    $price_shelf = $ProductSkuModel->price_shelf;
                    $min = $price_shelf * 0.2;
                    $max = $price_shelf * 2;
                    if(!$f_price_shelf){
                        $f_price_shelf = $price_shelf;
                    }
                    if($sku['price'] > $max && $max > 0){
                        $result['code'] = '2x1';
                        $result['message'] = "$attribute 价格最大只能调整到￥" . $max;
                        return $result;
                    }
                    if($sku['price'] < $min){
                        $result['code'] = '2x1';
                        $result['message'] = "$attribute 价格最小只能调整到￥" . $min;
                        return $result;
                    }
                }
                if($ProductSkuModel == null){
                    $f_min = $f_price_shelf * 0.2;
                    $f_max = $f_price_shelf * 2;
                    if($sku['price'] > $f_max && $f_max > 0){
                        $result['code'] = '2x1';
                        $result['message'] = "$attribute 价格最大只能调整到￥" . $f_max;
                        return $result;
                    }
                    if($sku['price'] < $f_min){
                        $result['code'] = '2x1';
                        $result['message'] = "$attribute 价格最小只能调整到￥" . $f_min;
                        return $result;
                    }
                }
            }
            $product->name = $request->name;
            $product->market_price = $request->market_price;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->shop_link = $request->shop_link;
            $product->category_id = $request->category_id;

            if(isset($request->integral_pay)){
                $product->integral_pay = $request->integral_pay == '1' ? '1' : '0';
            }

            $product->save();


            $color_option = OptionModel::where('name', '=', 'color')->first();

            $size_option = OptionModel::where('name', '=', 'size')->first();

            foreach ($skus as $skey => $sku) {
                $sku_id = $sku['sku_id'];
                $ProductSkuModel =  ProductSkuModel::where('product_id', $product->id)->where('id', '=', $sku_id)->first();
                if($ProductSkuModel == null){
                    $ProductSkuModel = new ProductSkuModel();
                    $ProductSkuModel->product_id = $product->id;
                    $ProductSkuModel->price_shelf = $sku['price'];
                }
                
                $ProductSkuModel->price = $sku['price'];
                $ProductSkuModel->market_price = $sku['market_price'];
                $ProductSkuModel->share_integral = $sku['share_integral'];
                $ProductSkuModel->shipping = $sku['shipping'];
                $ProductSkuModel->stock = $sku['stock'];
                $image = isset($sku['image_path']) ? $sku['image_path'] : '';
                $ProductSkuModel->image = $image;
                $ProductSkuModel->save();

                $ProductAttributeModel = ProductAttributeModel::where('product_id', $product->id)
                ->where('product_sku_id', $ProductSkuModel->id)
                ->where('option_id', $color_option->id)
                ->first();
                
                if(isset($sku['color'])){
                    if($ProductAttributeModel == null){
                        $ProductAttributeModel = new ProductAttributeModel();
                        $ProductAttributeModel->product_id = $product->id;
                        $ProductAttributeModel->product_sku_id = $ProductSkuModel->id;
                        $ProductAttributeModel->option_id = $color_option->id;
                    }
                    $ProductAttributeModel->option_value = $sku['color'];
                    $ProductAttributeModel->save();
                }
                $ProductAttributeModel = ProductAttributeModel::where('product_id', $product->id)
                ->where('product_sku_id', $ProductSkuModel->id)
                ->where('option_id', $size_option->id)
                ->first();
                if(isset($sku['size'])){
                    if($ProductAttributeModel == null){
                        $ProductAttributeModel = new ProductAttributeModel();
                        $ProductAttributeModel->product_id = $product->id;
                        $ProductAttributeModel->product_sku_id = $ProductSkuModel->id;
                        $ProductAttributeModel->option_id = $size_option->id;
                    }
                    $ProductAttributeModel->option_value = $sku['size'];
                    $ProductAttributeModel->save();
                }
            }

            $description_image_files = $request->description_image;

            if(!empty($description_image_files)){
                foreach ($description_image_files as $dkey => $description_image_file) {
                    $StoreStorage = new StoreStorage('product');
                    $filepath = $StoreStorage->saveUpload($description_image_file);
                    $ProductImageModel = ProductImageModel::where('type', 'description')
                    ->where('product_id', $product->id)
                    ->where('user_id', $user->id)
                    ->where('image', $filepath)
                    ->first();
                    if($ProductImageModel == null){
                        $ProductImageModel = new ProductImageModel();
                        $ProductImageModel->type = 'description';
                        $ProductImageModel->product_id = $product->id;
                        $ProductImageModel->user_id = $user->id;
                        $ProductImageModel->image = $filepath;
                        $ProductImageModel->save();
                    }
                }
            }

            $video = $request->file('video');
            if($video && $video->isValid()){
                //获取上传文件的大小
                $size = $video->getSize();
                //这里可根据配置文件的设置，做得更灵活一点
                if($size > 20*1024*1024){
                    $result['code'] = '2x1';
                    $result['message'] = '上传文件不能超过20M';
                    return $result;
                }

                $StoreStorage = new StoreStorage('product_video');
                $filepath = $StoreStorage->saveUpload($video);
                $product->video = $filepath;
                $product->save();
            }

            //ProductCache::clearDefaultSKUCache($product->id);
            
            ProductCache::clearProductCache($product->id);
           
            $result = [];
            $result['code'] = 'Success';
            $result['message'] = '保存成功';
            return $result;
        });
        return response()->json($return_result);
    }

    /**
     * 添加产品图片
     * @param  Request $request 
     * @return string
     */
    public function addProductImage(Request $request){
        set_time_limit(0);
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            $result['code'] = '2x1';
            $result['message'] = '店铺不存在！';
            return response()->json($result);
        }

        $product_id = $request->product_id;

        $product = ProductModel::where('id', $product_id)->first();

        if($product == null){
            $result['code'] = '2x1';
            $result['message'] = '店铺产品不存在！';
            return response()->json($result);
        }

        //登录事务处理
        $return_result = \DB::transaction(function() use ($user, $product, $request) {
           
            $file = $request->file('image');
            if(!$file || !$file->isValid()){
                $result['code'] = '2x1';
                $result['message'] = 'This is not a valid image.';
                return $result;
            }
            //获取上传文件的大小
            $size = $file->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 5*1024*1024){
                $result['code'] = '2x1';
                $result['message'] = '上传文件不能超过5M';
                return $result;
            }

            $type = $request->type;

            $StoreStorage = new StoreStorage('product');
            $filepath = $StoreStorage->saveUpload($file);
            $ProductImageModel = new ProductImageModel();
            $ProductImageModel->product_id = $product->id;
            $ProductImageModel->user_id = $user->id;
            $ProductImageModel->image = $filepath;
            $ProductImageModel->type = $type;
            $ProductImageModel->save();

            $image_link = \HelperImage::storagePath($filepath);

            $result = [];
            $result['data'] = ['image' => $ProductImageModel->image, 'image_path' => $filepath, 'image_link' => $image_link];
            $result['code'] = 'Success';
            $result['message'] = '保存成功';
            return $result;
        });
        return response()->json($return_result);
    }

    /**
     * 添加产品图片
     * @param  Request $request 
     * @return string
     */
    public function editProductVideo(Request $request){
        set_time_limit(0);
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            $result['code'] = '2x1';
            $result['message'] = '店铺不存在！';
            return response()->json($result);
        }

        $product_id = $request->product_id;

        $product = ProductModel::where('id', $product_id)->first();

        if($product == null){
            $result['code'] = '2x1';
            $result['message'] = '店铺产品不存在！';
            return response()->json($result);
        }

        //登录事务处理
        $return_result = \DB::transaction(function() use ($user, $product, $request) {
           
            $file = $request->file('image');
            if(!$file || !$file->isValid()){
                $result['code'] = '2x1';
                $result['message'] = 'This is not a valid image.';
                return $result;
            }
            //获取上传文件的大小
            $size = $file->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 30*1024*1024){
                $result['code'] = '2x1';
                $result['message'] = '上传文件不能超过30M';
                return $result;
            }

            $StoreStorage = new StoreStorage('product_video');
            $filepath = $StoreStorage->saveUpload($file);
            $product->video = $filepath;
            $product->save();

            $result = [];
            $result['code'] = 'Success';
            $result['message'] = '保存成功';
            return $result;
        });
        return response()->json($return_result);
    }


    /**
     * 删除产品图片
     * @param  Request $request 
     * @return string
     */
    public function removeProductImage(Request $request){
        $user = Auth::user();
        $store = StoreModel::where('user_id', '=', $user->id)->first();
        if($store == null){
            $result['code'] = '2xf';
            $result['message'] = '店铺不存在！';
            return response()->json($result);
        }

        $product_id = $request->product_id;

        $product = ProductModel::where('id', $product_id)->first();

        if($product == null){
            $result['code'] = '2xf';
            $result['message'] = '店铺产品不存在！';
            return response()->json($result);
        }

        //登录事务处理
        $return_result = \DB::transaction(function() use ($user, $product, $request) {

            $product_image_id = $request->product_image_id;

            $ProductImageModel = ProductImageModel::where('id', '=', $product_image_id)->first();

            if($ProductImageModel != null){
                $ProductImageModel->delete();
            }

            $result = [];
            $result['code'] = 'Success';
            $result['message'] = '删除成功';
            return $result;
        });
        return response()->json($return_result);
    }

     /**
     * 删除产品图片
     * @param  Request $request 
     * @return string
     */
    public function deleteProductSku(Request $request){

        $result = [];

        $user = Auth::user();

        $user_id = $user->id;
        
        $product_id = $request->product_id;

        $product = ProductModel::where('id', $product_id)->where('user_id', $user_id)->first();

        if($product == null){
            $result['code'] = '2xf';
            $result['message'] = '店铺产品不存在！';
            return response()->json($result);
        }

        $product_sku_id = $request->product_sku_id;

        $product_sku = $product->skus()->where('id', $product_sku_id)->first();

        if($product_sku == null){
            $result['code'] = '2xf';
            $result['message'] = '店铺产品规格不存在！';
            return response()->json($result);
        }

        $product_sku->deleted = '1';

        $product_sku->save();

        $product_sku = $product->attribute()->where('product_sku_id', $product_sku_id)->update([
            'deleted' => '1'
        ]);

        $result = [];
        $result['code'] = 'Success';
        $result['message'] = '删除成功';
        return response()->json($result);
    }

     /**
     * 确认发货
     *
     * @return void
    */
    public function orderShipped(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];

        $order_id = $request->order_id;

        $user = Auth::user();

        $user_id = $user->id;

        $order = OrderModel::where('id', $order_id)->where('seller_id', '=', $user_id)->first();

        if($order == null){
            $result['message'] = '订单不存在';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'pending'){
            $result['message'] = '订单未付款';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'cancel'){
            $result['message'] = '订单已取消';
            return response()->json($result);
        }

       
        if($order['order_status_code'] == 'finished'){
            $result['message'] = '订单已收货完成';
            return response()->json($result);
        }

        if($order['order_status_code'] != 'shipping'){
            $result['message'] = '订单未付款';
            return response()->json($result);
        }

        if($order['refund_status'] == '1'){
            $result['message'] = '订单退款中！';
            return response()->json($result);
        }

        if($order['refund_status'] == '2'){
            $result['message'] = '订单已退款！';
            return response()->json($result);
        }

        $OrderRefundModel = OrderRefundModel::where('order_id', $order_id)
        ->whereIn('status', ['0', '1', '2'])
        ->first();

        if($OrderRefundModel != null){
            $result['message'] = '客户申请退款中！';
            return response()->json($result);
        }

        $shipping_method = $request->shipping_method;

        $tracknumber = $request->tracknumber;

        if($order != null){
            $shipping_data = [
                'shipping_method' => $shipping_method,
                'tracknumber' => $tracknumber,
            ];
            OrderService::orderShipped($order, $shipping_data, $user);
            $result['code'] = 'Success';
            $result['message'] = '订单已经发货完成,请关注订单物流状态！确保及时发货！';
        }
        return response()->json($result);
    }

      /**
     * 确认发货
     *
     * @return void
    */
    public function orderRefundHandel(Request $request, Application $app)
    {
        $result = ['code' => '2x1'];

        $order_id = $request->order_id;

        $user = Auth::user();

        $user_id = $user->id;

        $order = OrderModel::where('id', $order_id)->where('seller_id', '=', $user_id)->first();

        if($order == null){
            $result['message'] = '订单不存在';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'pending'){
            $result['message'] = '订单未付款';
            return response()->json($result);
        }

        if($order['order_status_code'] == 'cancel'){
            $result['message'] = '订单已取消';
            return response()->json($result);
        }

        if($order['refund_status'] == '1'){
            $result['message'] = '订单退款中!';
            return response()->json($result);
        }

        if($order['refund_status'] == '2'){
            $result['message'] = '订单已退款!';
            return response()->json($result);
        }

        $order_refund_id = $request->order_refund_id;

        $OrderRefundModel = OrderRefundModel::where('id', $order_refund_id)
        ->where('order_id',  $order_id)
        ->first();

        if($OrderRefundModel == null){
            $result['message'] = '退换单不存在！';
            return response()->json($result);
        }

        if($OrderRefundModel['status'] != '0'){
            $result['message'] = '退换单已处理';
            return response()->json($result);
        }

        $handel_type = $request->handel_type;

        $handel_reason = $request->handel_reason;

        if($handel_type == '1'){
            $OrderRefundModel->handel_reason = $handel_reason;
            $OrderRefundModel->status = '-1';
            $OrderRefundModel->save();
            $order->save();
            $result['code'] = 'Success';
            $result['message'] = '退换单已处理';
            return response()->json($result);
        }

        if($handel_type == '2'){
            $refundFee = $OrderRefundModel->amount;
            $res = OrderService::refundHandel($order, $OrderRefundModel, $app, $refundFee);
            $result['code'] = 'Success';
            $result['message'] = $res['message'];
            return response()->json($result);
        }
    }

     /**
     * 帐号信息
     * @param  Request $request 
     * @return string
     */
    public function orderCount(Request $request){
        $user = Auth::user();
        $seller_id = $user->id;
        $data = [];

        $status_count = OrderModel::select('order_status_code', \DB::raw('COUNT(id) AS total'))
        ->where('seller_id', $seller_id)
        ->whereIn('order_status_code', ['pending', 'shipping', 'shipped'])
        ->groupBy('order_status_code')
        ->get();

        $order_status_count = [];
        foreach($status_count as $key => $val){
            $status_code = $val['order_status_code'];
            $order_status_count[$status_code] = $val['total'];
        }

        $un_review_count = OrderModel::where('seller_id', $seller_id)
            ->where('order_status_code', 'finished')
            ->where('is_review', '!=', '1')
            ->count();

        $order_status_count['review'] = $un_review_count;

        $refund_count = OrderRefundModel::join('order', 'order.id', '=', 'order_refund.order_id')
        ->where('order_refund.status', '0')
        ->where('order.seller_id', $seller_id)
        ->count();

        $order_status_count['refund'] = $refund_count;

        $result['data'] = $order_status_count;

        $result['code'] = 'Success';
        $result['message'] = '';
        return response()->json($result);
    }

    /**
     * 帐号信息
     * @param  Request $request 
     * @return string
     */
    public function productToShare(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        $product_id = $request->product_id;
        $product = ProductModel::where('id', '=', $product_id)->where('user_id', '=', $user_id)->first();
        if($product == null){
            $result['message'] = '产品不存在！';
            return response()->json($result);
        }
        if($product['is_shared'] == '1'){
            $result['message'] = '产品已经在共享专区';
            return response()->json($result);
        }
        $is_flag = true;
        $produt_sku = $product->skus()->where('deleted', '!=', '1')->get();
        foreach($produt_sku as $pkey => $sku){
            if($sku['share_integral'] <=0){
                $result['message'] = '产品规格共享积分未设置，不可申请，请先编辑产品！';
                $is_flag = false;
                return response()->json($result);
            }
            if($sku['share_integral'] < $sku['price'] * 0.1){
                $result['message'] = '产品规格共享积分必须全部大于产品规格价格的10%才可申请，请先编辑产品！';
                $is_flag = false;
                return response()->json($result);
            }
        }
        if(!$is_flag){
            return response()->json($result);
        }
        $ShareApplyModel = ShareApplyModel::where('user_id', '=', $user_id)
        ->where('product_id', '=', $product_id)
        ->where('status', '=', '0')
        ->first();
        if($ShareApplyModel != null){
            $result['message'] = '产品已申请，请等待审核！';
            return response()->json($result);
        }
        $ShareApplyModel = new ShareApplyModel();
        $ShareApplyModel->user_id = $user_id;
        $ShareApplyModel->product_id = $product_id;
        $ShareApplyModel->save();
        $result['code'] = 'Success';
        $result['message'] = '产品已申请，请等待审核！';
        return response()->json($result);
    }

    public function view(Request $request)
    {
        $result = ['code' => '2x1'];


        $id = $request->store_id;

        $store = StoreModel::where('id', '=', $id)->first();

        if($store == null){
            $result['code'] = 'Success';
            $result['data'] = [];
            return response()->json($result);
        }

        $expire_date = $store->expire_date;
        $date = date('Y-m-d H:i:s');
        $store_enable = 0;
        if($store['status'] == '2'){
            if($expire_date != null && $date <= $expire_date){
                $store_enable = 1;
            }
        }
        $user = Auth::user();
        $share_data = [
            'title' => $store['name'],
            'content' => $store['name'],
            'url' => \Helper::route('store_view', [$store['id']]),
            'image' => empty($store['banner']) ? \Helper::asset_url('/media/images/default_store_banner.png') 
                    : \HelperImage::storagePath($store['banner'])
        ];
        $is_viewd = 1;
        if(empty($store) || !$store_enable){
            $is_viewd = 0;
        }
        if(!empty($store) && !empty($user) && $user['id'] == $store['user_id']){
            $is_viewd = 0;
        }
        if(empty($store['banner'])){
            $store['banner'] = Helper::asset_url('/media/images/default_store_banner.png');
        } else {
            $store['banner'] = HelperImage::storagePath($store['banner']);
        }

        $store['info_image'] = Helper::asset_url('/media/images/bstore.gif');

        $store['bcate'] = Helper::asset_url('/media/images/bcate.png');
        
        $data =  [
            'title' => $store['name'],
            'store' => $store,
            'store_enable' => $store_enable,
            'share_data' => $share_data,
            'is_viewd' => $is_viewd
        ];
        $result['code'] = 'Success';
        $result['data'] = $data;
        return response()->json($result);
    }

     public function products(Request $request)
    {
        $result = ['code' => '2x1'];


        $id = $request->store_id;

        $store = StoreModel::where('id', '=', $id)->first();

        if($store == null){
            $result['code'] = 'Success';
            $result['data'] = [];
            return response()->json($result);
        }

        $expire_date = $store->expire_date;
        $date = date('Y-m-d H:i:s');
        $store_enable = 0;
        if($store['status'] == '2'){
            if($expire_date != null && $date <= $expire_date){
                $store_enable = 1;
            }
        }
        $self_products = ProductDispalyService::getShareProduct(0,100);
        if(empty($store != null) || !$store_enable){
            $data =  [
                'products' => [],
                'self_products' => $self_products
            ];
            $result['code'] = 'Success';
            $result['data'] = $data;
            return response()->json($result);
        }

        $products = [];

        if($store != null){
            $products = $products = StoreService::getStoreProduct($store, '1');
        }
       
        $data =  [
            'products' => $products,
            'self_products' => $self_products
        ];
        $result['code'] = 'Success';
        $result['data'] = $data;
        return response()->json($result);
    }
}
