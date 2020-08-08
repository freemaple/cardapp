<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Helper\Base as HelperBase;
use Hash;
use Validator;
use Auth;
use App\Libs\Service\ProductCategoryService;

class ProductCategoryController extends BaseController
{

    /**
     * 产品分类列表
     *
     * @return string
     */
    public function index(Request $request)
    {
        $ProductCategory_service = ProductCategoryService::getInstance();
        $orderBy = [['id', 'desc']];
        $form = $request->all();
        $where = [];
        $pid = $request->pid;
        $allProductCategory = $ProductCategory_service->allProductCategory($where, $orderBy);
        $ProductCategory_tree = $ProductCategory_service->getTreeProductCategory($allProductCategory, '-1', true, $pid);
        $name = $request->name;
        if($pid > 0){
            $ProductCategory_list = $ProductCategory_service->getChildProductCategorys($pid);
        } else {
            $ProductCategory_list = $ProductCategory_service->treeAll($allProductCategory);
        }
        if($name != null){
            $search_ProductCategory_list = [];
            foreach ($ProductCategory_list as $key => $value) {
                if($value['name'] == $name){
                    $search_ProductCategory_list[] = $value;
                }
            }
            $ProductCategory_list = $search_ProductCategory_list;
        }
        if($request->isMethod('post')){
            $view =  view('admin.productcategory.block.productcategory_list', ['productcategory_list' => $ProductCategory_list]);
            $result = ['code' => '200', 'view' => $view->render()];
            return json_encode($result);
        }

        $ProductCategory_select_list = $ProductCategory_service->treeAll($allProductCategory);

        return view('admin.productcategory.index', [
            'title' => '产品分类',
            'productcategory_list' => $ProductCategory_list,
            'productcategory_select_list' => $ProductCategory_select_list,
            'form' => $form,
            'productcategory_tree' => $ProductCategory_tree
        ]);
    }

    /**
     * 加载组织
     *
     * @return string
     */
    public function load(Request $request)
    {
        $result = ['message' => ''];
        $ProductCategory_service = ProductCategoryService::getInstance();
        $id = $request->id;
        $ProductCategory = $ProductCategory_service->find($id);
        if($ProductCategory == null){
            $result['message'] = '对不起,分类不存在！';
        } else {
            $result['code'] = '200';
            $result['data'] = $ProductCategory->toArray();
        }
        return json_encode($result);
    }

    /**
     * 保存组织
     *
     * @return string
     */
    public function save(Request $request)
    {
        $result = ['message' => ''];
        $save_type = $request->save_type;
        $ProductCategory_service = ProductCategoryService::getInstance();
        $post_data = $request->all();
        $admin_user = \Auth::guard('admin')->User();
        $post_data['admin_id'] = $admin_user->id;
        switch ($save_type) {
            //添加
            case '0':
                $post_data['pid'] = !empty($post_data['pid']) && intval($post_data['pid']) ? $post_data['pid'] : 0;
                $result = $ProductCategory_service->addProductCategory($post_data);
                break;
            //编辑
            case '1':
                $result = $ProductCategory_service->editProductCategory($post_data);
                break;
            default:
                break;
        }
        return json_encode($result);
    }
}