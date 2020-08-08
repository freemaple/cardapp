<?php
namespace App\Libs\Service;

use Validator;
use DB;
use Image;
use App\Repository\Base as BaseRepository;
use App\Repository\Product as ProductRepository;
use App\Models\Product\Product as ProductModel;
use App\Models\Product\Sku as ProductSkuModel;
use App\Models\Product\Option as OptionModel;
use App\Models\Product\optionValue as optionValueModel;
use App\Models\Product\Attribute as ProductAttributeModel;
use App\Models\Product\Image as ProductImageModel;
use App\Libraries\Storage\Product as ProductStorage;
use App\Cache\Product as ProductCache;

class ProductService
{
  /**
     * @var Singleton reference to singleton instance
     */
  private static $_instance;  

  
  /**
     * 构造函数私有，不允许在外部实例化
     *
    */
  private function __construct(){}

  /**
     * 防止对象实例被克隆
     *
     * @return void
    */
  private function __clone() {}
  
  /**
   * Create a new Repository instance.单例模式
   *
   * @return void
   */
    public static function getInstance()    
    {    
        if(! (self::$_instance instanceof self) ) {    
            self::$_instance = new self();   
        }
        return self::$_instance;    
    }  

    /**
     * 产品分页列表
     * @param  array $data 
     * @return array
     */
    public function productList($data = []){
        $product_repository = BaseRepository::model("Product\Product");
        $product_list = $product_repository->get($data);
        return $product_list;
    }


    /**
     * 加载产品
     * @param  integer $id 
     * @return array
     */
    public function findProduct($id){
        $product_repository = BaseRepository::model("Product\Product");
        $product = $product_repository->find($id);
        return $product;
    }

    /**
     * 产品sku列表
     * @param  array $data 
     * @return array
     */
    public function productSkuList($data = []){
        $product_repository = BaseRepository::model("Product\Sku");
        $product_sku_list = $product_repository->get($data);
        return $product_sku_list;
    }

    /**
     * 查找产品sku
     * @param  array   $where    查询条件
     * @return array
     */
    public function findProductSku($where = []){
        $product_sku_repository = BaseRepository::model("Product\Sku");
        $product_value = $product_sku_repository->findOne($where);
        return $product_value;
    }

    /**
     *更新产品
     * @param  int $admin_id 
     * @param  array $list_data 文件上传数据
     * @return array
     */
    public function uploadProduct($admin_id, $list_data)
    {
        //返回信息
        $result = ['status' => false, 'message' => '', 'success_count' => 0, 'faild_count' => 0];

        $exist_upload_fail = false;

        $spu_data = [];

        $ProductCategoryService = ProductCategoryService::getInstance();

        foreach ($list_data as $key => $data) {
            $validator_data = [
                'spu' => 'required'
            ];
            //数据校验
            $validator = Validator::make($data, $validator_data);
            //校验失败
            if($validator->fails()){
                $errors = $validator->errors()->all();
                $result['message'] .= implode(' ', $errors);
                $exist_upload_fail =  true;
                continue;
            }
            if(isset($data['sku']) && $data['sku'] != null){
                $sku_validator_data = [
                    'sku' => 'required',
                    'option' => 'required',
                    'price' => 'required|Numeric|min:0',
                    'market_price' => 'Numeric|min:0',
                ];
                //数据校验
                $validator = Validator::make($data, $sku_validator_data);
                //校验失败
                if($validator->fails()){
                    $errors = $validator->errors()->all();
                    $result['message'] .= "sku:" . $data['sku'] . " " . implode(' ', $errors);
                    $exist_upload_fail =  true;
                    continue;
                }
            }
            $spu = $data['spu'];
            if(!isset($spu_data[$spu])){
                $spu_data_item = ['skus' => []];
                if(isset($data['category_id'])){
                   $category = $ProductCategoryService->find($data['category_id']);
                   if($category == null){
                        $result['message'] .= "分类category_id:$category_id 不存在！";
                        $exist_upload_fail =  true;
                        return result;
                   }
                   $spu_data_item['category_id'] =  $data['category_id'];
                }
                $spu_data_item['spu'] =  $spu;
                if(isset($data['name'])){
                   $spu_data_item['name'] =  $data['name'];
                }
                if(isset($data['cn_name'])){
                   $spu_data_item['cn_name'] =  $data['cn_name'];
                }
                if(isset($data['description'])){
                   $spu_data_item['description'] =  $data['description'];
                }
                if(isset($data['tag'])){
                    $spu_data_item['tag'] = $data['tag'];
                }
                if(isset($data['is_sale'])){
                   $spu_data_item['is_sale'] =  $data['is_sale'];
                }
                $spu_data[$spu] = $spu_data_item;
            }
            if(isset($data['sku']) && $data['sku'] != null){
                $spu_data[$spu]['skus'][] = [
                    'sku' => $data['sku'],
                    'option' => $data['option'],
                    'price' => $data['price'],
                    'market_price' => isset($data['market_price']) ? $data['market_price'] : 0,
                    'is_sale' => $data['sku_is_sale'] ? $data['sku_is_sale'] : '0',
                    'share_integral' => isset($data['share_integral']) ? $data['share_integral'] : 0,
                ];
            }
        }
        foreach ($spu_data as $spu => $spu_item) {
            $ProductModel = ProductModel::where('spu', $spu)->first();
            if($ProductModel == null){
                $ProductModel = new ProductModel();
            }
            foreach ($spu_item as $key => $value) {
                if($key != 'skus'){
                    $ProductModel->$key = $value;
                }
            }
            $ProductModel->is_self = '1';
            $ProductModel->save();
            if(!empty($spu_item['skus']) && $ProductModel->id != null){
                foreach ($spu_item['skus'] as $skey => $sku_item) {
                    $ProductSkuModel = ProductSkuModel::where('product_id', $ProductModel->id)->where('sku', $sku_item['sku'])->first();
                    if($ProductSkuModel == null){
                        $ProductSkuModel = new ProductSkuModel();
                    }
                    $ProductSkuModel->product_id = $ProductModel->id;
                    foreach ($sku_item as $skey => $svalue) {
                        if($skey != 'option'){
                            $ProductSkuModel->$skey = $svalue;
                        }
                    }
                    $r = $ProductSkuModel->save();
                    if($r){
                        if(isset($sku_item['option'])){
                            $option_data = json_decode($sku_item['option'], true);
                            $option_value_ids = [];
                            foreach ($option_data as $option => $option_value) {
                               $option_model = OptionModel::where('name', '=', $option)->first();
                                if($option_model == null){
                                    $option_model = new OptionModel();
                                    $option_model->admin_id = $admin_id;
                                    $option_model->name = $option;
                                    $option_model->save();
                                }
                                $option_value_model = optionValueModel::where('option_id', $option_model->id)->where('value', '=', $option_value)->first();
                               if($option_value_model == null){
                                    $option_value_model = new optionValueModel();
                                    $option_value_model->option_id = $option_model->id;
                                    $option_value_model->value = $option_value;
                                    $option_value_model->admin_id = $admin_id;
                                    $option_value_model->save();
                               }
                               $ProductAttributeModel = ProductAttributeModel::where('product_id', '=', $ProductModel->id)
                               ->where('product_sku_id', $ProductSkuModel->id)
                               ->where('option_id', $option_model->id)->where('option_value_id', $option_value_model->id)->first();
                               if($ProductAttributeModel == null){
                                    $ProductAttributeModel = new ProductAttributeModel();
                                    $ProductAttributeModel->product_id = $ProductModel->id;
                                    $ProductAttributeModel->product_sku_id = $ProductSkuModel->id;
                                    $ProductAttributeModel->option_id = $option_model->id;
                                    $ProductAttributeModel->option_value_id = $option_value_model->id;
                                    $ProductAttributeModel->option_value = $option_value_model->value;
                                    $ProductAttributeModel->save();
                               }
                               $option_value_ids[] = $option_value_model->id;
                            }
                            $c_option_value_ids = implode(',', $option_value_ids);
                            $ProductSkuModel->option_value_ids = $c_option_value_ids;
                            $ProductSkuModel->save();
                        }
                    }
                }
            }
        }
        if(!$exist_upload_fail){
            $result['status'] = true;
            $result['message'] = '上传成功';
        }
        return $result;
    }

    /**
     *上传更新产品
     * @param  int $admin_id 
     * @param  array $list_data 文件上传数据
     * @return array
     */
    public function uploadEditProduct($admin_id, $list_data)
    {
        //返回信息
        $result = ['status' => false, 'message' => '', 'success_count' => 0, 'faild_count' => 0];

        $exist_upload_fail = false;

        $spu_data = [];

        foreach ($list_data as $key => $data) {
            $validator_data = [
                'spu' => 'required'
            ];
            //数据校验
            $validator = Validator::make($data, $validator_data);
            //校验失败
            if($validator->fails()){
                $errors = $validator->errors()->all();
                $result['message'] .= implode(' ', $errors);
                $exist_upload_fail =  true;
                continue;
            }
            if(isset($data['sku']) && $data['sku'] != null){
                $sku_validator_data = [
                    'price' => 'Numeric|min:0',
                ];
                //数据校验
                $validator = Validator::make($data, $sku_validator_data);
                //校验失败
                if($validator->fails()){
                    $errors = $validator->errors()->all();
                    $result['message'] .= "sku:" . $data['sku'] . " " . implode(' ', $errors);
                    $exist_upload_fail =  true;
                    continue;
                }
            }
            $spu = $data['spu'];
            if(!isset($spu_data[$spu])){
                $spu_data_item = ['skus' => []];
                if(isset($data['name'])){
                   $spu_data_item['name'] =  $data['name'];
                }
                if(isset($data['cn_name'])){
                   $spu_data_item['cn_name'] =  $data['cn_name'];
                }
                if(isset($data['description'])){
                   $spu_data_item['description'] =  $data['description'];
                }
                if(isset($data['tag'])){
                    $spu_data_item['tag'] = $data['tag'];
                }
                if(isset($data['is_sale'])){
                   $spu_data_item['is_sale'] =  $data['is_sale'];
                }
                $spu_data[$spu] = $spu_data_item;
            }
            if(isset($data['sku']) && $data['sku'] != null){
                $sku_data_item = ['sku' => $data['sku']];
                if(isset($data['price'])){
                   $sku_data_item['price'] =  $data['price'];
                }
                if(isset($data['market_price'])){
                   $sku_data_item['market_price'] =  $data['market_price'];
                }
                if(isset($data['is_sku_sale'])){
                   $sku_data_item['is_sku_sale'] =  $data['is_sku_sale'];
                }
                if(isset($data['share_integral'])){
                   $sku_data_item['share_integral'] =  $data['share_integral'];
                }
                $spu_data[$spu]['skus'][] = $sku_data_item;
            }
        }
        foreach ($spu_data as $spu => $spu_item) {
            $ProductModel = ProductModel::where('spu', $spu)->first();
            if($ProductModel != null){
                foreach ($spu_item as $key => $value) {
                    if($key != 'skus'){
                        $ProductModel->$key = $value;
                    }
                }
                $ProductModel->save();
            }
            if(!empty($spu_item['skus']) && $ProductModel->id != null){
                foreach ($spu_item['skus'] as $skey => $sku_item) {
                    $ProductSkuModel = ProductSkuModel::where('product_id', $ProductModel->id)->where('sku', $sku_item['sku'])->first();
                    if($ProductSkuModel != null){
                        foreach ($sku_item as $skey => $svalue) {
                            $ProductSkuModel->$skey = $svalue;
                        }
                        $r = $ProductSkuModel->save();
                    }
                }
            }
        }
        if(!$exist_upload_fail){
            $result['status'] = true;
            $result['message'] = '更新成功';
        }
        return $result;
    }

    /**
     * 添加产品
     * @param  Request $request 
     * @return string
     */
    public function addProduct($admin_user, $request){
        //登录事务处理
        $return_result = \DB::transaction(function() use ($admin_user, $request) {
            $spu = $request->spu;
            $spu_product = ProductModel::where('spu', '=', $spu)->first();
            if($spu_product != null){
                $result = [];
                $result['code'] = '2x1';
                $result['message'] = 'spu已存在！';
                return $result;
            }
            $admin_id = $admin_user->id;
            $product = new ProductModel();
            $product->spu = $spu;
            $product->is_self = '1';
            $product->admin_id = $admin_id;
            $product->name = $request->name;
            $product->market_price = $request->market_price;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->is_sale = '1';
            $product->is_shared = '1';
            $product->service_phone = $request->service_phone;

            $product->save();

            $image_files = $request->image;

            $image_paths = [];

            if(!empty($image_files)){
                foreach ($image_files as $ikey => $image_file) {
                    $ProductStorage = new ProductStorage('product');
                    $filepath = $ProductStorage->saveUpload($image_file);
                    $image_paths[] = $filepath;
                    $ProductImageModel = new ProductImageModel();
                    $ProductImageModel->type = 'main';
                    $ProductImageModel->product_id = $product->id;
                    $ProductImageModel->admin_id = $admin_id;
                    $ProductImageModel->image = $filepath;
                    $ProductImageModel->save();
                }
            }

            $description_image_files = $request->description_image;

            if(!empty($description_image_files)){
                foreach ($description_image_files as $dkey => $description_image_file) {
                    $ProductStorage = new ProductStorage('product');
                    $filepath = $ProductStorage->saveUpload($description_image_file);
                    $ProductImageModel = new ProductImageModel();
                    $ProductImageModel->type = 'description';
                    $ProductImageModel->product_id = $product->id;
                    $ProductImageModel->admin_id = $admin_id;
                    $ProductImageModel->image = $filepath;
                    $ProductImageModel->save();
                }
            }

            $skus = $request->skus;

            $skus = json_decode($skus, true);

            $color_option = OptionModel::where('name', '=', 'color')->first();

            $size_option = OptionModel::where('name', '=', 'size')->first();

            foreach ($skus as $skey => $sku) {
                $ProductSkuModel = new ProductSkuModel();
                $ProductSkuModel->product_id = $product->id;
                $ProductSkuModel->price = $sku['price'];
                $ProductSkuModel->market_price = $sku['market_price'];
                $ProductSkuModel->shipping = $sku['shipping'] > 0 ? $sku['shipping'] : 0;
                $ProductSkuModel->share_integral = $sku['share_integral'] > 0 ? $sku['share_integral'] : 0;
                if($ProductSkuModel->share_integral >= $ProductSkuModel->price){
                    $ProductSkuModel->share_integral = $ProductSkuModel->price;
                }
                $ProductSkuModel->stock = $sku['stock'] > 0 ? $sku['stock'] : 0;
                $image = isset($image_paths[$sku['image_file']]) ? $image_paths[$sku['image_file']] : '';
                $ProductSkuModel->share_integral = $sku['share_integral'];
                $ProductSkuModel->image = $image;
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
                if($size > 10*1024*1024){
                    $result['code'] = '2x1';
                    $result['message'] = '上传文件不能超过10M';
                    return $result;
                }

                $ProductStorage = new ProductStorage('product_video');
                $filepath = $ProductStorage->saveUpload($video);
                $product->video = $filepath;
                $product->save();
            }

            //ProductCache::clearDefaultSKUCache($product->id);

            ProductCache::clearProductCache($product->id);

            $result = [];
            $result['code'] = '200';
            $result['message'] = '保存成功';
            return $result;
        });
        return $return_result;
    }

    /**
     * 更新产品
     * @param  array $data
     * @return array
     */
    public function updateProduct($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $product = $this->findProduct($data['id']);

        if($product == null){
            $result['message'] = '产品不存在！';
            return $result;
        }

        $update_data = [];

        if(isset($data['category_id'])){
            $update_data['category_id'] = $data['category_id'];
        }

        if(isset($data['name'])){
            $update_data['name'] = $data['name'];
        }

        if(isset($data['cn_name'])){
            $update_data['cn_name'] = $data['cn_name'];
        }

        if(isset($data['description'])){
            $update_data['description'] = $data['description'];
        }

        if(isset($data['tag'])){
            $update_data['tag'] = $data['tag'];
        }

        if(isset($data['is_sale'])){
            $update_data['is_sale'] = $data['is_sale'];
        }

        if(isset($data['service_phone'])){
            $update_data['service_phone'] = $data['service_phone'];
        }

        if(!empty($update_data)){
            foreach ($update_data as $key => $value) {
                $product->$key = $value;
            }
            $product->save();
        }

        if(isset($data['product_images'])){
            $product_images_models = $product->images()->where('type', '=', 'description')->orderBy('id', 'asc')->get();
            $product_images = $data['product_images'];
            $is_modify = true;
            if(count($product_images_models) > 0){
                $product_images_arr = $product_images_models->toArray();
                if($product_images != null  && count($product_images) > 0 && count($product_images) == count($product_images_arr)){
                    $is_modify = false;
                    foreach ($product_images_arr as $key => $value) {
                        if(isset($product_images[$key]['image']) && $product_images_arr[$key]['image'] != $product_images[$key]['image']){
                            $is_modify = true;
                            break;
                        }
                    }
                }
            }
            if($is_modify){
                if($product_images != null  && count($product_images) > 0){
                    foreach ($product_images as $pkey => $product_image) {
                        if(empty($product_image['image']) && !empty($product_image['imgsrc'])){
                            $res = $this->base64upload($product_image['imgsrc'], 'product');
                            if($res['filename']){
                                $product_image['image'] = $res['filename'];
                            }
                        }
                        if($product_image['image']){
                            $ProductImageModel = new ProductImageModel(); 
                            $ProductImageModel->product_id =  $product->id;
                            $ProductImageModel->image = $product_image['image'];
                            $ProductImageModel->type = 'description';
                            $ProductImageModel->save();
                        }
                    }
                }
                if(count($product_images_models) > 0){
                    foreach ($product_images_models as $key => $product_images_model) {
                        $image = $product_images_model->image;
                        $product_images_model->delete();
                    }
                }
            }
        }

        ProductCache::clearProductCache($product->id);

        $result['status'] = true;
        return $result;
    }

    /**
     * 上传产品图片
     */
    public function base64upload($base64_image_content, $directory) {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $matches)){
            //图片路径地址    
            $fullpath = storage_path() . '/app/static/' . $directory;
            if(!is_dir($fullpath)){
                mkdir($fullpath, 0777, true);
            }
            $image_file = \Image::make($base64_image_content);
            $width = $image_file->width();
            $height = $image_file->height();
            if($width > 1024){
                $h = 1024 / $width * $height;
                $image_file = $image_file->resize(1024, $h);
                $base64_image_content = $image_file->encode('data-url');
            }
            $type = $matches[2];
            $content_arr = explode($matches[0], $base64_image_content);
            $img = base64_decode($content_arr[1]);
            $filename = md5(date('YmdHis').rand(1000, 9999)). '.jpg';
            $savepath = $fullpath . '/' . $filename;
            file_put_contents($savepath, $img);
            //服务器文件存储路径
            if (file_put_contents($savepath, $img)){
                $result['status'] = 1;
                $result['filename'] = 'product//' . $filename;
                $result['filepath'] = '/' . $savepath;
                return $result;
            }else{
                $result['status'] = 0;
                $result['message'] = '保存失败';
                return $result;
            }
        } else {
            $result['status'] = 0;
            $result['message'] = '不是有效的图片';
            return $result;
        }
    }

    /**
     *生成小图
    */
    public function makeSkuImage($product_sku){

        if(empty($product_sku['image1'])){
            return false;
        }

        $directory = storage_path("app/public/product");

        $image_size = [500, 260, 100];

        $imag1 = $product_sku['image1'];

        $newFile = sprintf('%s/%s', $directory, $imag1);

        $image = Image::make($newFile);

        foreach ($image_size as $size) {
            $filename = sprintf("%s/%s_%s.jpg", $directory, $product_sku['sku'], $size);
            $image->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($filename);
        }

        return true;
    }

      /**
     * 添加产品sku
     * @param  array $data
     * @return array
     */
    public function addProductSku($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'product_id' => 'required',
            'price' => 'required'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $product_id = $data['product_id'];

        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['message'] = '产品不存在！';
            return $result;
        }

        if(!empty($data['market_price']) && $data['market_price'] < $data['price']){
            $result['message'] = '市场价不能小于价格';
            return $result;
        }

        if(!empty($data['share_integral']) && $data['share_integral'] * 10 >= $data['price']){
            $result['message'] = '共享积分不能大于售价';
            return $result;
        }

        $ProductSkuModel = new ProductSkuModel();

        $ProductSkuModel->product_id = $product_id;

        $ProductSkuModel->price = $data['price'];

        if(isset($data['market_price'])){
            $ProductSkuModel->market_price = $data['market_price'] ? $data['market_price'] : 0;
        }

        $ProductSkuModel->is_sale = $data['is_sale'] ? $data['is_sale'] : 0;

        $ProductSkuModel->stock = $data['stock'] ? $data['stock'] : 0;

        $ProductSkuModel->share_integral = $data['share_integral'] ? $data['share_integral'] : 0;

        $ProductSkuModel->save();

         if(isset($sku['color'])){
            $ProductAttributeModel = ProductAttributeModel::where('product_id', $product->id)
            ->where('product_sku_id', $ProductSkuModel->id)
            ->where('option_id', $color_option->id)
            ->first();
            if($ProductAttributeModel == null){
                $ProductAttributeModel = new ProductAttributeModel();
                $ProductAttributeModel->product_id = $product->id;
                $ProductAttributeModel->product_sku_id = $ProductSkuModel->id;
                $ProductAttributeModel->option_id = $color_option->id;
            }
            $ProductAttributeModel->option_value = $sku['color'];
            $ProductAttributeModel->save();
        }
        if(isset($sku['size'])){
            $ProductAttributeModel = ProductAttributeModel::where('product_id', $product->id)
            ->where('product_sku_id', $ProductSkuModel->id)
            ->where('option_id', $size_option->id)
            ->first();
            if($ProductAttributeModel == null){
                $ProductAttributeModel = new ProductAttributeModel();
                $ProductAttributeModel->product_id = $product->id;
                $ProductAttributeModel->product_sku_id = $ProductSkuModel->id;
                $ProductAttributeModel->option_id = $size_option->id;
            }
            $ProductAttributeModel->option_value = $sku['size'];
            $ProductAttributeModel->save();
        }

        //ProductCache::clearDefaultSKUCache($product_id);
        
        ProductCache::clearProductCache($product_id);

        $result['status'] = true;
        return $result;
    }

     /**
     * 更新产品sku
     * @param  array $data
     * @return array
     */
    public function updateProductSku($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required',
            'price' => 'Numeric|min:0',
            'market_price' => 'Numeric|min:0',
            'stock' => 'integer|min:0',
            'share_integral' => 'Numeric|min:0',
            'shipping' => 'Numeric|min:0'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $product_sku = $this->findProductSku([['id', '=', $data['id']]]);

        if($product_sku == null){
            $result['message'] = '产品SKU不存在！';
            return $result;
        }

        $update_data = [];

        $price = $product_sku['price'];

        if(isset($data['price'])){
            $update_data['price'] = $data['price'];
            $price = $data['price'];
        }

        if(isset($data['market_price'])){
            $update_data['market_price'] = $data['market_price'];
        }

        if(isset($data['purchase_price'])){
            $update_data['purchase_price'] = $data['purchase_price'];
        }

        if(isset($data['is_sale'])){
            $update_data['is_sale'] = $data['is_sale'];
        }

        if(isset($data['stock'])){
            $update_data['stock'] = $data['stock'];
        }

        if(isset($data['share_integral'])){
            $update_data['share_integral'] = $data['share_integral'];
        }

        if(!empty($update_data['share_integral']) && $update_data['share_integral']  >= $price){
            $result['message'] = '共享积分不能大于售价';
            return $result;
        }

        if(isset($data['shipping'])){
            $update_data['shipping'] = $data['shipping'];
        }

        if(!empty($update_data)){
            foreach ($update_data as $key => $value) {
                $product_sku->$key = $value;
            }
            $product_sku->save();
        }

        ProductCache::clearProductCache($product_sku['product_id']);

        $result['status'] = true;
        return $result;
    }

    public function productCodeImage($product){

        $sku = $product->skus()->where('deleted', '!=', '1')->first();

        if($sku == null){
            return false;
        }

        $img = $sku['image'];

        $path = storage_path() . '/app/static/' . $img;

        $product_img = \Image::make($path);

        $width = $product_img->width();

        $height = $product_img->height();


        $p = 640 / $width;

        $width = 640;

        $height = round($height * $p, 0);

        $canvas_height = $height + 160;

        $product_img = $product_img->resize($width, $height);

         // 修改指定图片的大小
        $canvas = \Image::canvas(640, $canvas_height, '#ffffff');

        $canvas->insert($product_img, 'top-left', 0, 0);

        $product_name = str_limit($product['name'], 35);

        // 标题
        $canvas = $canvas->text($product_name, 5, $height + 35 , function($font) {
            $font->file(public_path('fonts/msyhbd.ttf'));
            $font->size(24);
            $font->color('#333');
            $font->align('left');
            $font->valign('bottom');
        });

        // 
        $canvas = $canvas->text('原价:￥' . $sku['market_price'], 5, $height + 80 , function($font) {
            $font->file(public_path('fonts/msyhbd.ttf'));
            $font->size(22);
            $font->color('#999');
            $font->align('left');
            $font->valign('bottom');
        });

        //
        $canvas = $canvas->text('活动价:￥' . $sku['price'], 5, $height + 135 , function($font) {
            $font->file(public_path('fonts/msyhbd.ttf'));
            $font->size(26);
            $font->color('#f00');
            $font->align('left');
            $font->valign('bottom');
        });

        //
        $canvas = $canvas->text('人人有赏 扫码购', $width - 10, $height + 130 , function($font) {
            $font->file(public_path('fonts/msyhbd.ttf'));
            $font->size(18);
            $font->color('#f92704');
            $font->align('right');
            $font->valign('bottom');
        });

        $goods_detail = ProductDispalyService::findProduct($product['id']);

        if(isset($goods_detail['max_share_integral']) && $goods_detail['max_share_integral'] > 0){
            $share_tip = '自购/分享赚红包' . $goods_detail['share_amount_min'] . '~' .$goods_detail['share_amount_max'];
            $canvas = $canvas->text($share_tip, 5, $height + 160 , function($font) {
                $font->file(public_path('fonts/msyhbd.ttf'));
                $font->size(20);
                $font->color('#444444');
                $font->align('left');
                $font->valign('bottom');
            });
        }


        $product_qrcode = $this->qrcode($product, 100, [0, 255, 255]);

        // 插入水印, 水印位置在原图片的右下角, 距离下边距 10 像素, 距离右边距 15 像素
        $image_data = $canvas->insert($product_qrcode, 'top-right', 25, $height)->encode('data-url');
        
        $base64_code = $image_data->encoded;

        return $base64_code;
    }

    /**
     * 二维码
     *
     * @return \Illuminate\Http\Response
     */
    public function qrcode($product, $size){
        $user = \Auth::user();
        $product_link = \Helper::route('product_view', $product['id']);
        if(!empty($user)){
            $sid = $user->u_id;
            $product_link = \Helper::route('product_view', [$product['id'], 'sid' => $sid]);
        }
        $product_qrcode = \Helper::qrcode1($product_link, $size, [0, 0, 0]);
        return  'data:image/png;base64,' . base64_encode($product_qrcode);
    }
}