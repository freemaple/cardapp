<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Product\ActivityCategory;
use App\Models\Product\ActivityCategoryProduct;
use App\Models\Product\Product AS ProductModel;
use App\Cache\Product as ProductCache;
use App\Cache\Home as HomeCache;

class ActivityController extends BaseController
{

    /**
     * 活动分类
     *
     * @return void
    */
    public function index(Request $request)
    {
        $ActivityCategory = new ActivityCategory();

        $pageSize = 20;

        $form = $request->all();

        $name = trim($request->name);

        if($name != null){
            $ActivityCategory = $ActivityCategory->where('name', '=', $name);
        }

        $enabled = $request->enabled;

        if(isset($request->enabled) && $enabled !==''){
            $ActivityCategory = $ActivityCategory->where('enabled', '=', $enabled);
        }

        $activitycategorys = $ActivityCategory->orderBy('created_at', 'desc')->paginate($pageSize);

        $activitycategorys->appends($request->all());

        $pager = $activitycategorys->links();

        $view = View('admin.activity.category');

        $view->with("activitycategorys", $activitycategorys);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "活动分类");

        return $view;

    }

     /**
     * 活动分类
     *
     * @return void
    */
    public function product(Request $request, $id)
    {
        $id = $request->id;

        $activity_category = ActivityCategory::where('id', '=', $id)->first();

        if($activity_category == null){
            $result['message'] = '对不起，活动分类不存在！';
        }

        $ActivityCategoryProduct = ActivityCategoryProduct::select('activity_category_product.id', 'activity_category_product.product_id','activity_category_product.created_at', 'product.name as product_name')
        ->join('product', 'product.id', 'activity_category_product.product_id');

        $pageSize = 100;

        $form = $request->all();

        $name = trim($request->name);

        if($name != null){
            $ActivityCategory = $ActivityCategoryProduct->where('product.name', '=', $name);
        }

        $products = $ActivityCategoryProduct->orderBy('activity_category_product.created_at', 'desc')->paginate($pageSize);

        foreach ($products as $key => $product) {
            $product = ProductModel::where('id', $product['product_id'])->first();
            $product_sku = ProductCache::defaultSKU($product);
            $image = !empty($product_sku) ? $product_sku['image'] : '';
            $products[$key]['image'] = \HelperImage::storagePath($image);
            $products[$key]['product_sku'] = $product_sku;
        }


        $products->appends($request->all());

        $pager = $products ->links();
        $view = View('admin.activity.product');

        $view->with("activity_category", $activity_category);

        $view->with("products", $products);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "活动分类" . $activity_category['description'] . "产品");

        return $view;

    }


     /**
     * 活动分类
     *
     * @return void
    */
    public function addProduct(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];

        $activity_category_id = $request->activity_category_id;

        $activity_category = ActivityCategory::where('id', '=', $activity_category_id)->first();

        if($activity_category == null){
            $result['message'] = '对不起，活动分类不存在！';
            return json_encode($result);
        }

        $product_ids = $request->product_ids;

        $product_ids = explode(';', $product_ids);

        $flag = true;

        $add_count = 0;

        foreach ($product_ids as $key => $product_id) {
            $product = ProductModel::where('id', $product_id)->first();
            if($product == null){
                $flag = false;
                $result['message']  .= '产品' . $product_id . '不存在, ';
                continue;
            } else {
                $ActivityCategoryProduct = ActivityCategoryProduct::where('activity_category_id', $activity_category_id)
                ->where('product_id', $product_id)
                ->first();
               if($ActivityCategoryProduct == null){
                    $ActivityCategoryProduct = new ActivityCategoryProduct();
                    $ActivityCategoryProduct->activity_category_id = $activity_category_id;
                    $ActivityCategoryProduct->product_id = $product_id;
                    $ActivityCategoryProduct->admin_id = $this->admin_user->id;
                    $ActivityCategoryProduct->save();
               }
               $add_count ++;
            }
        }

        if($flag == false){
            $result['code'] = '2x1';
            $result['message']  .= ' 添加' . $add_count . '个产品成功';
        } else {
            $result['code'] = '200';
            $result['message']  = '保存成功';
        }

        if($activity_category['name'] == 'special_price'){
            HomeCache::clearSpecialProductCache();
        }
        
        return json_encode($result);

    }

     /**
     * 活动分类
     *
     * @return void
    */
    public function removeProduct(Request $request)
    {
        $result = ['code' => '2x1'];

        $id = $request->id;

        $activity_category = ActivityCategory::where('id', '=', $id)->first();

        if($activity_category == null){
            $result['message'] = '对不起，活动分类不存在！';
            return json_encode($result);
        }

        $product_id = $request->product_id;

        $activity_category_id = $activity_category->id;

        $ActivityCategoryProduct = ActivityCategoryProduct::where('activity_category_id', $activity_category_id)
           ->where('product_id', $product_id)
           ->first();
        if($ActivityCategoryProduct != null){
            $ActivityCategoryProduct->delete();
            if($activity_category['name'] == 'special_price'){
                HomeCache::clearSpecialProductCache();
            }
        }
        $result['code'] = '200';
        $result['message']  = '删除成功';
        return json_encode($result);
    }
}