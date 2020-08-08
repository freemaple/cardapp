<?php
namespace App\Http\Controllers\Admin\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Helper\Base as HelperBase;
use Auth;
use Excel;
use App\Libs\Service\ProductService;

use App\Models\Product\Product as ProductModel;
use App\Libraries\Storage\Product as ProductStorage;
use App\Models\Product\Image as ProductImageModel;
use App\Models\Product\ActivityCategory as ActivityCategoryModel;
use App\Models\Product\ActivityCategoryProduct;
use App\Cache\Product as ProductCache;

class ProductController extends Controller
{

     /**
     * 上传产品
     *
     * @return void
    */
    public function uploadProduct(Request $request)
    {
        $result = ['status' => false, 'code' => '2x1', 'message' => ''];
        $product_service = ProductService::getInstance();
        $file = $request->file('file');
        $edit_type = $request->edit_type;
        $filePath = $file->getRealPath();
        try{
            Excel::load($filePath, function($reader) use($product_service, &$result, $edit_type) {
                if($reader == null){
                    $result['message'] = '没有可导入的数据';
                    return false;
                }
                $data = $reader->all();
                if($data == null){
                    $result['message'] = '没有可导入的数据';
                    return false;
                }
                $admin_user = Auth::guard('admin')->User();
                $admin_id = $admin_user->id;
                $list_data = $data->toArray();
                if($edit_type == 'edit'){
                    $res = $product_service->uploadEditProduct($admin_id, $list_data);
                } else {
                    $res = $product_service->uploadProduct($admin_id, $list_data);
                }
                
                if($res['status']){
                    $result['code'] = '200';
                    $result['status'] = true;
                }
                $result['message'] = $res['message'];
            });
            if($result['status']){
                $message = ['type' => 'success', 'message' => $result['message']];
                return redirect()->back()->withInput()->with('message', $message);
            } else {
                $message = ['type' => 'error', 'message' => $result['message']];
                dd($message);
                return redirect()->back()->withInput()->with('message', $message);
            }
        } catch(\Exception $e){
            $result['message'] = $e->getMessage();
            $message = ['type' => 'error', 'message' => $result['message']];
            dd($e->getMessage());
            return redirect()->back()->withInput()->with('message', $message);
        }
    }

    /**
     * 加载产品
     *
     * @return void
    */
    public function loadProduct(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $id = $request->id;
        $product_service = ProductService::getInstance();
        $product = $product_service->findProduct($id);
        if($product == null){
            $result['message'] = '产品不存在！';
            return json_encode($result);
        }
        $product_image = $product->images()->where('type', '=', 'description')->get();
        if(count($product_image) > 0){
            $product_image = $product_image->toArray();
            foreach ($product_image as $key => $value) {
                $product_image[$key]['imgsrc'] = \HelperImage::storagePath($value['image']);
            }
        }
        $product = $product->toArray();
        if($product['video'] != ''){
            $product['video'] = \HelperImage::storagePath($product['video']);
        }
        $product['image'] = $product_image;
        $result['code'] = '200';
        $result['data'] = $product;
        return json_encode($result);
    }

     /**
     * 编辑产品
     *
     * @return void
    */
    public function addProduct(Request $request)
    {
        set_time_limit(0);
        $result = ['code' => '2x1', 'message' => ''];
        $admin_user = Auth::guard('admin')->user();
        $product_service = ProductService::getInstance();
        $result = $product_service->addProduct($admin_user, $request);
        return response()->json($result);
    }


    /**
     * 编辑产品
     *
     * @return void
    */
    public function editProduct(Request $request)
    {
        set_time_limit(0);
        $result = ['code' => '2x1', 'message' => ''];
        $model = $request->all();
        $admin_user = Auth::guard('admin')->User();
        $model['admin_id'] = $admin_user->id;
        $product_service = ProductService::getInstance();
        $result = $product_service->updateProduct($model);
        if($result['status'] == true){
            $result['code'] = '200';
        } 
        return json_encode($result);
    }

    public function editProductVideo(Request $request){

        $result = ['code' => '2x1', 'message' => ''];

        $product_id = $request->product_id;

        $product_service = ProductService::getInstance();
        $product = $product_service->findProduct($product_id);
        if($product == null){
            $result['message'] = '产品不存在！';
            return json_encode($result);
        }

        $video = $request->file('video');

        if(!$video || !$video->isValid()){
            $result['message'] = '不是有效的视频文件';
            return json_encode($result);
        }
        
         //获取上传文件的大小
        $size = $video->getSize();
        //这里可根据配置文件的设置，做得更灵活一点
        if($size > 30*1024*1024){
            $result['code'] = '2x1';
            $result['message'] = '上传文件不能超过30M';
            return $result;
        }

        $ProductStorage = new ProductStorage('product_video');
        $filepath = $ProductStorage->saveUpload($video);
        $product->video = $filepath;
        $product->save();

        ProductCache::clearProductCache($product->id);

        $result['code'] = '200';
        $result['data']['video'] = \HelperImage::storagePath($filepath);
        return json_encode($result);
    }

    /**
     * 加载供应商
     *
     * @return void
    */
    public function loadProductSpuImageSelect(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $id = $request->id;
        $product_service = ProductService::getInstance();
        $product = $product_service->findProduct($id);
        if($product == null){
            $result['message'] = '产品不存在！';
            return json_encode($result);
        }
        $product_image = $product->images()->get();
        if(count($product_image) > 0){
            $product_image = $product_image->toArray();
            foreach ($product_image as $key => $value) {
                $product_image[$key]['imgsrc'] = \HelperImage::storagePath($value['image']);
            }
        }
        $product = $product->toArray();
        $product['image'] = $product_image;
        $view = view('admin.product.block.spu_image_select_modal', ['product' => $product])->render();
        $product['image'] = $product_image;
        $result['code'] = '200';
        $result['data'] = ['view' => $view];
        return json_encode($result);
    }

    /**
     * 保存spu主图
     *
     * @return void
    */
    public function saveSpuimage(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $product_service = ProductService::getInstance();
        $id = $request->id;
        $image_check = $request->image_check;
        //创建banner
        $product = $product_service->findProduct($id);
        if($product == null){
            $result['message'] = 'sku 不存在！';
            return;
        }
        $spu = $product->spu;
        $product->image = $image_check;
        $product->save();
        $result['code'] = '200';
        return json_encode($result);
    }

    /**
     * 保存sku主图
     *
     * @return void
    */
    public function saveSkuimage(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $product_service = ProductService::getInstance();
        $id = $request->id;
        $image_check = $request->image_check;
        //创建banner
        $product_sku = $product_service->findProductSku([['id', '=', $id]]);
        if($product_sku == null){
            $result['message'] = 'sku 不存在！';
        }
        $product_sku->image = $image_check;
        $product_sku->save();
        $result['code'] = '200';
        return json_encode($result);
    }


    /**
     * 加载sku编辑
     *
     * @return void
    */
    public function loadSkuEdit(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $product_service = ProductService::getInstance();
        $id = $request->id;
        $image_check = $request->image_check;
        //创建banner
        $product_sku = $product_service->findProductSku([['id', '=', $id]]);
        if($product_sku == null){
            $result['message'] = 'sku 不存在！';
        }
        $attributes = $product_sku->attribute()->get();
        if(count($attributes) > 0){
            foreach ($attributes as $key => $attribute) {
                $attribute->optionData = $attribute->option;
                $attribute->optionValueData = $attribute->optionValue;
            }
            $attributes = $attributes->toArray();
        }

        $product_sku = $product_sku->toArray();

        $product_sku['attributes'] = $attributes;

        $view = view('admin.product.sku.block.edit', ['product_sku' => $product_sku])->render();
        
        $result['code'] = '200';

        $result['data'] = ['view' => $view];

        return json_encode($result);
    }

    /**
     * 编辑产品
     *
     * @return void
    */
    public function addProductSku(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $model = $request->all();
        $admin_user = Auth::guard('admin')->User();
        $model['admin_id'] = $admin_user->id;
        $product_service = ProductService::getInstance();
        $result = $product_service->addProductSku($model);
        if($result['status'] == true){
            $result['code'] = '200';
        } 
        return json_encode($result);
    }

    /**
     * 编辑产品
     *
     * @return void
    */
    public function editProductSku(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $model = $request->all();
        $admin_user = Auth::guard('admin')->User();
        $model['admin_id'] = $admin_user->id;
        $product_service = ProductService::getInstance();
        $result = $product_service->updateProductSku($model);
        if($result['status'] == true){
            $result['code'] = '200';
        } 
        return json_encode($result);
    }

    /**
     * 添加产品图片
     * @param  Request $request 
     * @return string
     */
    public function addProductImage(Request $request){

        $product_id = $request->product_id;

        $product = ProductModel::where('id', $product_id)->first();

        if($product == null){
            $result['code'] = '2x1';
            $result['message'] = '产品不存在！';
            return response()->json($result);
        }

        //登录事务处理
        $return_result = \DB::transaction(function() use ($product, $request) {
           
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

            $ProductStorage = new ProductStorage('product');
            $filepath = $ProductStorage->saveUpload($file);
            $ProductImageModel = new ProductImageModel();
            $ProductImageModel->product_id = $product->id;
            $ProductImageModel->user_id = 0;
            $ProductImageModel->image = $filepath;
            $ProductImageModel->type = 'main';
            $ProductImageModel->save();

            $image_link = \HelperImage::storagePath($filepath);

            $result = [];
            $result['data'] = ['image' => $ProductImageModel->image, 'image_path' => $filepath, 'image_link' => $image_link];
            $result['code'] = '200';
            $result['message'] = '保存成功';
            return $result;
        });
        return response()->json($return_result);
    }


    /**
     * 添加到活动分类
     *
     * @return void
    */
    public function addActivityCategoryProduct(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $product_service = ProductService::getInstance();
        $activity_category_id = $request->activity_category_id;
        $product_id = $request->product_id;
        //创建banner
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result['message'] = '产品不存在！';
        }
        $ActivityCategoryModel = ActivityCategoryModel::where('id', $activity_category_id)->first();
        if($ActivityCategoryModel == null){
            $result['message'] = '活动分类不存在！';
        }
        $ActivityCategoryProduct = ActivityCategoryProduct::where('product_id', $product_id)->where('activity_category_id', $activity_category_id)->first();
        if($ActivityCategoryProduct == null){
            $ActivityCategoryProduct = new ActivityCategoryProduct();
            $ActivityCategoryProduct->product_id = $product_id;
            $ActivityCategoryProduct->activity_category_id = $activity_category_id;
            $ActivityCategoryProduct->save();
        }
        $result['code'] = '200';
        return json_encode($result);
    }
}