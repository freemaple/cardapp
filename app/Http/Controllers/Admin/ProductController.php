<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Libs\Service\ProductService;
use App\Libs\Service\ProductDispalyService;
use App\Libs\Service\ProductCategoryService;
use App\Libs\Service\OptionService;
use App\Libs\Service\MessageService;
use App\Models\Store\StoreProduct as StoreProductModel;
use App\Models\Store\Store as StoreModel;
use App\Models\Product\Product as ProductModel;
use App\Models\Product\ShareApply as ShareApplyModel;
use App\Models\Gift\Gift as GiftModel;
use App\Models\Admin\AdminUser as AdminUserModel;
use App\Cache\User as UserCache;
use App\Cache\Product as ProductCache;


class ProductController extends BaseController
{

    /**
     * 店铺信息
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $categorys = ProductCategoryService::getInstance()->getTopCategoryList();
        $view = view('admin.product.add',[
            'title' => '添加产品',
            'categorys' => $categorys,
            'type' => 'add'
        ]);
        return $view;
    }

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $form = $request->all();

        $is_add_gift = $request->is_add_gift ? 1 : 0;

        $product_service = ProductService::getInstance();

        $productcategory_service = ProductCategoryService::getInstance();

        $orderBy = [['id', 'desc']];

        $allProductCategory = $productcategory_service->allProductCategory([], $orderBy);

        $productCategory_select_list = $productcategory_service->treeAll($allProductCategory);

        $pageSize = 20;

        $where = [['deleted', '!=', '1']];

        $whereIn = [];

        $category_id = trim($request->category_id);

        if($category_id != null){
            $cetegory_ids = ProductCategoryService::getInstance()->getChildProductCategoryIds($category_id);
            if(count($cetegory_ids) > 1){
                $whereIn[] = ['category_id', $cetegory_ids];
            } else {
                $where[] = ['category_id', '=', $category_id];
            }
        }

        $name = trim($request->name);

        $is_self = $request->is_self;

        if($is_self == '1'){
            $where[] = ['is_self', '=', '1'];
        } else {
            $where[] = ['is_self', '!=', '1'];
        }

        $is_gift = $request->is_gift ? $request->is_gift : 0;

        if($request->is_gift){
            $where[] = ['is_gift', '=', $is_gift];
        }

        

        if($name != null){
            $where[] = ['name', '=', $name];
        }

        $spu = trim($request->spu);

        if($spu != null){
            $where[] = ['spu', '=', $spu];
        }

        $is_sale = trim($request->is_sale);

        if($is_sale != null){
            $where[] = ['is_sale', '=', $is_sale];
        }

        if($is_add_gift == '1'){
            $where[] = ['is_gift', '!=', '1'];
        }

        $productOrderBy = [['id', 'desc']];

        $data = ['where' => $where, 'whereIn' => $whereIn, 'orderBy' => $productOrderBy, 'pageSize' => 20];

        //当前页数据
        $product_list = $product_service->productList($data);

        foreach ($product_list as $key => $product) {
            $product_sku = $product->skus()->orderBy('sku')->get();
            if($product_sku != null){
                $product_sku = $product_sku->toArray();
            }
            $image = !empty($product_sku) ? $product_sku[0]['image'] : '';
            $product_list[$key]->image = $image;
            $product_list[$key]->sku_list = $product_sku;

            $store_product = StoreProductModel::where('product_id', $product['id'])->first();
            if($store_product != null){
                $store = StoreModel::where('id', $store_product['store_id'])->first();
                $product_list[$key]->store_info = !empty($store) ? $store->toArray() : [];
                $user = $product->user()->first();
                $product->user_info = !empty($user) ? $user->toArray() : [];;
            }
            if($product['category_id']){
                foreach ($productCategory_select_list as $key => $category_item) {
                    if($product['category_id'] == $category_item['id']){
                        $product->categoryinfo = $category_item;
                        break;
                    }
                }
            }
        }

        $product_list->appends($request->all());

        $pager = $product_list->links();

        if($product_list != null){
            $product_list = $product_list->toArray();
        }

        $OptionService = OptionService::getInstance();

        $option_list = $OptionService->optionList();

        $view = View('admin.product.index', [
            'product_list' => $product_list,
            'productCategory_select_list' => $productCategory_select_list,
            'form' => $form,
            'pager' => $pager,
            'option_list' => $option_list,
            'is_add_gift' => $is_add_gift,
            'title' => "产品列表"
        ]);

        return $view;

    }

    /**
     * 产品列表sku
     *
     * @return void
    */
    public function sku(Request $request, $id)
    {
        $form = $request->all();

        $product_service = ProductService::getInstance();

        $product = $product_service->findProduct($id);
        if(empty($product)){
            return redirect(route("admin_product"));
        }

        $sku = trim($request->sku);

        $pageSize = 20;

        $where = [];

        $where[] = ['product_id', '=', $product->id];

        if($sku != null){
            $where[] = ['sku', '=', $sku];
        }

        $data = ['where' => $where, 'pageSize' => 20];

        //当前页数据
        $product_sku_list = $product_service->productSkuList($data);

        $product_sku_list->appends($request->all());

        $pager = $product_sku_list->links();

        if($product_sku_list != null){
            $product_sku_list = $product_sku_list->toArray();
        }

        $product_image = $product->images()->get();
        if(count($product_image) > 0){
            $product_image = $product_image->toArray();
            foreach ($product_image as $key => $value) {
                $product_image[$key]['imgsrc'] = \HelperImage::storagePath($value['image']);
            }
        }

        $attribute = $product->attribute()->select('product_attribute.*', 'option.name as option_name', 'option.description as option_description')
        ->join('option', 'option.id', '=', 'product_attribute.option_id')
         ->where('product_attribute.deleted', '!=', '1')
        ->get();

        $attribute_option = [];
        foreach ($attribute as $key => $attribute_item) {
            if(!in_array($attribute_item['option_name'], $attribute_option)){
                $attribute_option[] = $attribute_item['option_name'];
            }
        }

        $view = View('admin.product.sku.list');

        $view->with("product", $product);

        $view->with("product_sku_list", $product_sku_list);

        $view->with("product_image", $product_image);

        $view->with("pager", $pager);

        $view->with("form", $form);

        $view->with("title", "产品sku");

        $view->with("attribute_option", $attribute_option);

        return $view;

    }

    /**
     * 生成图
     */
    public function makeImage(Request $request, $id){
        $product_service = ProductService::getInstance();
        $product_sku = $product_service->findProductSku([['id', '=', $id]]);
        if(empty($product_sku)){
            return redirect(route("admin_product"));
        }
        $product_id = $product_sku['product_id'];
        $result = $product_service->makeSkuImage($product_sku);
        return redirect(route("admin_product_sku", $product_id));
    }

     /**
     * 用户列表
     *
     * @return void
    */
    public function option(Request $request)
    {
        $OptionService = OptionService::getInstance();

        $option_list = $OptionService->optionList();

        if(!empty($option_list)){
            $option_list = $option_list->toArray();
        }

        $view = View('admin.product.option.index');

        $view->with("option_list", $option_list);

        $view->with("title", "属性分类");

        return $view;

    }

    /**
     * 用户列表
     *
     * @return void
    */
    public function shareApply(Request $request)
    {
        $status = $request->status;
        
        $ShareApplyModel = new ShareApplyModel();

        if($status != ''){
            $ShareApplyModel = $ShareApplyModel->where('status', $status);
        }

        $pageSize = 20;

        $ShareApplyModel = $ShareApplyModel->orderBy('product_share_apply.id', 'desc')
        ->paginate($pageSize);

        foreach($ShareApplyModel as $skey => $shareApply){
            $shareApply->product = ProductDispalyService::findProduct($shareApply->product_id);
            $shareApply->user = UserCache::info($shareApply->user_id);
            $store = StoreModel::where('user_id', '=', $shareApply->user_id)->first();
            $shareApply->store = $store;
        }

        $ShareApplyModel->appends($request->all());

        $pager = $ShareApplyModel->links();

        $status_text = [
            '0' => '待审核',
            '1' => '通过',
            '-1' => '拒绝'
        ];

        $view = View('admin.product.shareApply');

        $view->with("shareApplys", $ShareApplyModel);

        $view->with("pager", $pager);

        $view->with("status_text", $status_text);

        $view->with("title", "共享专区申请");

        return $view;

    }


    public function shareApplyApproval(Request $request){
        $id = $request->id;
        $type = $request->type;
        $remarks = $request->remarks;
        $ShareApplyModel = ShareApplyModel::where('id', $id)->first();
        if($ShareApplyModel == null){
            $result = [];
            $result['message'] = '审核记录不存在！';
            return response()->json($result);
        }
        $user_id = $ShareApplyModel->user_id;
        $product_id = $ShareApplyModel->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        if($product == null){
            $result = [];
            $result['message'] = '产品不存在！';
            return response()->json($result);
        }
        if($product->user_id != $user_id){
            $result = [];
            $result['message'] = '产品不属于此用户！';
            return response()->json($result);
        }
        if($type == '1'){
            $product->is_shared = '1';
            $product->save();
            $ShareApplyModel->status = '1';
            $ShareApplyModel->admin_id = $this->admin_user->id;
            $ShareApplyModel->approval_time = date('Y-m-d h:i:s');
            $ShareApplyModel->save();
            $data = [
                'user_id' => $user_id,
                'name' => "申请进入共享专区已审核通过",
                'content' => "您申请产品:" . $product['name'] . " 进入共享专区 已审核通过"
            ];
            MessageService::insert($data);
        } else {
            $ShareApplyModel->status = '-1';
            $ShareApplyModel->remarks = $remarks;
            $ShareApplyModel->admin_id = $this->admin_user->id;
            $ShareApplyModel->approval_time = date('Y-m-d h:i:s');
            $ShareApplyModel->save();
            $data = [
                'user_id' => $user_id,
                'name' => "对不起，申请进入共享专区被拒绝",
                'content' => "您申请产品:" . $product['name'] . " 进入共享专区 已被拒绝， 请检查后再申请，原因如下：" . $remarks 
            ];
            MessageService::insert($data);
        }
        $result = ['code' => 'Success', 'message' => '已审批'];
        return response()->json($result);
    }

     /**
     * 用户列表
     *
     * @return void
    */
    public function gift(Request $request)
    {
        $form = $request->all();

        $pageSize = 20;

        $gift_model = new GiftModel();

        $gift_list = $gift_model->paginate($pageSize);

        foreach ($gift_list as $key => $gift) {
            $product_id = $gift['product_id'];
            $product = ProductModel::where('id', $product_id)->first();
            $product_sku = $product->skus()->orderBy('sku')->get();
            if($product_sku != null){
                $product_sku = $product_sku->toArray();
            }
            $image = !empty($product_sku) ? $product_sku[0]['image'] : '';
            $gift_list[$key]->image = \HelperImage::storagePath($image);
            $gift_list[$key]->product = $product;
            $admin_id = $gift->admin_id;
            $admin_user = AdminUserModel::where('id', $admin_id)->first();
            $gift->admin_name = !empty($admin_user['username']) ? $admin_user['username'] : '';
            $stock = 0;
            foreach ($product_sku as $key => $sku) {
                if($sku['is_sale'] && $sku['deleted'] != '1'){
                    $stock += $sku['stock'];
                }
            }
            $gift->skus_stock = $stock;
        }

        $gift_list->appends($request->all());

        $pager = $gift_list->links();

        if($gift_list != null){
            $gift_list = $gift_list->toArray();
        }

        $view = View('admin.gift.index', [
            'gift_list' => $gift_list,
            'form' => $form,
            'pager' => $pager,
            'title' => "产品列表"
        ]);

        return $view;

    }

    /**
     * 用户列表
     *
     * @return void
    */
    public function addgift(Request $request)
    {
          if($request->isMethod('post')){

            $product_id = $request->product_id;

            $form = $request->all();

            $validator = \Validator::make($request->all(), [
                'price' => 'required',
                'market_price' => 'required',
                'gift_commission' => 'required',
                'sub_integral' => 'required',
                'manager_commission' => 'required',
                'director_commission' => 'required',
                'first_gift_commission_1' => 'required',
                'secend_gift_commission_1' => 'required',
                'first_gift_commission_2' => 'required',
                'secend_gift_commission_2' => 'required',
            ]);

            if($validator->fails()){

                $result = array();

                $result['code'] = "0x00001";

                exit(json_encode($result));
            }

            $user = \Auth::guard('admin')->user();

            $gift = GiftModel::where('product_id', '=', $product_id)->first();

            if(!empty($gift)){
                return;
            }

            $product = ProductModel::where('id', $product_id)->first();



            $gift = new GiftModel();

            $gift->admin_id = $user->id;

            $gift->product_id = $product_id;

            //$gift->name = $request->name;

            $gift->price = $request->price;

            $gift->market_price = $request->market_price;

            $gift->gift_commission = $request->gift_commission;

            $gift->sub_integral = $request->sub_integral;

            $gift->manager_commission = $request->manager_commission;

            $gift->director_commission = $request->director_commission;

            $gift->first_gift_commission_1 = $request->first_gift_commission_1;

            $gift->first_gift_reward_1 = $request->first_gift_reward_1;

            $gift->secend_gift_commission_1 = $request->secend_gift_commission_1;

            $gift->first_gift_commission_2 = $request->first_gift_commission_2;

            $gift->first_gift_reward_2 = $request->first_gift_reward_2;

            $gift->secend_gift_commission_2 = $request->secend_gift_commission_2;

            $gift->gold_amount = $request->gold_amount;

            $gift->ref_remove_gold_number = $request->ref_remove_gold_number;

            $gift->gift_type = 'vip';

            $gift->save();

            $product->is_gift = 1;

            $product->save();

            ProductCache::clearProductCache($product_id);

            return redirect(route("admin_product_gift"));

        }

        $product_id = $request->product_id;
        $product = ProductModel::where('id', $product_id)->first();
        $product_sku = $product->skus()->orderBy('sku')->get();
        if($product_sku != null){
            $product_sku = $product_sku->toArray();
        }
        $image = !empty($product_sku) ? $product_sku[0]['image'] : '';
        $product->image = \HelperImage::storagePath($image);

        $view = View('admin.gift.add', [
            'title' => "添加礼品",
            'product_id' => $product_id,
            'product' => $product,
        ]);

        return $view;

    }

     /**
     * 用户列表
     *
     * @return void
    */
    public function editgift(Request $request)
    {
        $gift_id = $request->id;

        $gift = GiftModel::where('id', '=', $gift_id)->first();

        if(empty($gift)){
            return;
        }

        $product_id = $gift['product_id'];

        if($request->isMethod('post')){

            $form = $request->all();

            $validator = \Validator::make($request->all(), [
                'price' => 'required',
                'market_price' => 'required',
                'gift_commission' => 'required',
                'sub_integral' => 'required',
                'manager_commission' => 'required',
                'director_commission' => 'required',
                'first_gift_commission_1' => 'required',
                'secend_gift_commission_1' => 'required',
                'first_gift_commission_2' => 'required',
                'secend_gift_commission_2' => 'required',
            ]);

            if($validator->fails()){

                $result = array();

                $result['code'] = "0x00001";

                exit(json_encode($result));
            }

            $user = \Auth::guard('admin')->user();

            $product_id = $gift['product_id'];

            $product = ProductModel::where('id', $product_id)->first();

            //$gift->name = $request->name;

            $gift->price = $request->price;

            $gift->market_price = $request->market_price;

            $gift->gift_commission = $request->gift_commission;

            $gift->sub_integral = $request->sub_integral;

            $gift->manager_commission = $request->manager_commission;

            $gift->director_commission = $request->director_commission;

            $gift->first_gift_commission_1 = $request->first_gift_commission_1;

            $gift->first_gift_reward_1 = $request->first_gift_reward_1;

            $gift->secend_gift_commission_1 = $request->secend_gift_commission_1;

            $gift->first_gift_commission_2 = $request->first_gift_commission_2;

            $gift->first_gift_reward_2 = $request->first_gift_reward_2;

            $gift->secend_gift_commission_2 = $request->secend_gift_commission_2;

            $gift->gold_amount = $request->gold_amount;

            $gift->ref_remove_gold_number = $request->ref_remove_gold_number;

            $gift->enable = $request->enable;

            $gift->save();

            ProductCache::clearProductCache($product_id);

            return redirect(route("admin_product_gift_edit", ['id' => $gift_id]));

        }

        $product = ProductModel::where('id', $product_id)->first();
        $product_sku = $product->skus()->orderBy('sku')->get();
        if($product_sku != null){
            $product_sku = $product_sku->toArray();
        }
        $image = !empty($product_sku) ? $product_sku[0]['image'] : '';
        $product->image = \HelperImage::storagePath($image);

        $view = View('admin.gift.edit', [
            'title' => "添加礼品",
            'gift_id' => $gift_id,
            'gift' => $gift,
            'product' => $product
        ]);

        return $view;

    }
}