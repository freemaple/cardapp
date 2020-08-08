<?php

namespace App\Libs\Service;

use App\Repository\Base as BaseRepository;
use App\Models\Product\Category as ProductCategoryModel;
use Validator;

class ProductCategoryService 
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

    public function getTopCategoryList($field = '*')
    {
        $productcategory_list = ProductCategoryModel::select($field)->where('pid', '=', '0')
        ->where('is_enable', '1')->where('is_deleted','=', '0')
        ->orderBy('id')
        ->get();

        if(count($productcategory_list) > 0){
            $productcategory_list = $productcategory_list->toArray();
        }

        return $productcategory_list;
    } 


    public function getList($where = [], $orderBy = [], $pageSize = null)
    {
        $data = ['where' => $where, 'pageSize' => $pageSize, 'orderBy' => $orderBy];

        $ProductCategoryRepository = BaseRepository::model("Product\Category");

        $productcategory_list = $ProductCategoryRepository->get($data);

        return $productcategory_list;
    }

    public function find($id)
    {
        $result = ProductCategoryModel::find($id);
        return $result;
    }

    public function findOne($where = [])
    {
        $result = ProductCategoryModel::where($where)->first();
        return $result;
    }

    public function addProductCategory($data)
    {
         //返回信息
        $result = ['status' => false, 'code' => '2x1', 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required',
            'position' => 'numeric'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $product_category_repository = BaseRepository::model("Product\Category");

        $where = [['name', '=', $data['name']]];

        $product_category = $product_category_repository->findOne( $where);
        if(!empty($ProductCategory)){
            $result['message'] = '分类名称已经存在';
            $result['code'] = '2x1';
        }

        //上传成功,插入数据
        $insert_data = [
            'admin_id' => isset($data['admin_id']) ? $data['admin_id'] : '0',
            'name' => $data['name'],
            'pid' => isset($data['pid']) ? $data['pid'] : '0',
            'description' =>  $data['description']
        ]; 
        $product_category_repository = BaseRepository::model("Product\Category");
        $product = $product_category_repository->insert($insert_data);
        $result['status'] = true;
        $result['code'] = '200';
        return $result;
    }

    public function editProductCategory($data)
    {
        //返回信息
        $result = ['status' => false, 'code' => '2x1', 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required',
            'position' => 'numeric'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $product_category_repository = BaseRepository::model("Product\Category");

        $where = [['name', '=', $data['name']]];

        $product_category = $product_category_repository->findOne($where);
        if(!empty($ProductCategory) && $data['id'] != $product_category['id']){
            $result['message'] = '分类名称已经存在';
            $result['code'] = '2x1';
        }

        //更新数据
        $update_data = []; 

        if(isset($data['name'])){
            $update_data['name'] = $data['name'];
        }
        if(isset($data['pid'])){
            $update_data['pid'] = intval($data['pid']) ? intval($data['pid']) : '0';
        }
        if(isset($data['description'])){
            $update_data['description'] = $data['description'];
        }
        if(isset($data['position'])){
            $update_data['position'] = intval($data['position']) ? intval($data['position']) : '0';
        }
        if(isset($data['is_enable'])){
            $update_data['is_enable'] = intval($data['is_enable']) ? intval($data['is_enable']) : '0';
        }
        $product_category_repository->update($update_data, [['id', '=', $data['id']]]);
        $result['status'] = true;
        $result['code'] = '200';
        return $result;
    }

    public function tree($arr, $p_id = '0', $is_top = false, $select_id = null, $top_name = '') {
        $tree = array();
        if($is_top){
            if($select_id == null){
                $top_select = true;
            } else {
                $top_select = false;
            }
            $top_name = $top_name ? $top_name : '分类';
            $arr[] = ['name' => $top_name, 'id' => '0', 'pid' => '-1',  
            'state' => ["opened" =>  true, 'selected' => $top_select]];
        }
        foreach($arr as $row){
            if($row['id'] == $select_id){
                $row['state']['selected'] = true;
                $row['state']["opened"] =  true;
            }
            if($row['pid'] == $p_id){
                $tmp = $this->tree($arr,$row['id'], false, $select_id, $top_name);
                if($tmp){
                    $row['children'] = $tmp;
                }
                $row['text'] = $row['name'];
                $tree[] = $row;    
            }
        }
        return $tree;        
    }


    public function treeAll($arr, $p_id = '0', $is_top = false , &$treeAll = [], $c_row = null, $select_id = null) {
        $tree = array();
        if($is_top){
            if($select_id == null){
                $top_select = true;
            } else {
                $top_select = false;
            }
            $arr[] = ['name' => '分类', 'id' => '0', 'pid' => '-1',  
            'state' => ["opened" =>  true, 'selected' => $top_select]];
            $treeAll = $arr;
        }
        foreach($arr as $row){
            if($row['id'] == $select_id){
                $row['selected'] = true;
            }
            if($row['pid'] == $p_id){
                if($row['pid'] == '0'){
                    $row['level'] = 0;
                } else {
                    $level = $c_row['level'] + 1;
                    $row['level'] = $level;
                }
                $treeAll[] = $row;
                $this->treeAll($arr, $row['id'], false, $treeAll, $row, $select_id);
                $tree[] = $row;    
            }
        }
        return $treeAll;        
    }

    public function allProductCategory($where = null, $orderBy = null) {
        $where[] = ['is_deleted', '=', '0'];
        $orderBy = [['id', 'desc']];
        $allProductCategory = $this->getList($where, $orderBy);
        if($allProductCategory != null){
            $allProductCategory = $allProductCategory->toArray();
        } else {
            $allProductCategory = [];
        }
        return $allProductCategory;
    }

    public function getTreeProductCategory($allProductCategory, $pid = '0', $is_top = false, $select_id = null, $top_name = '') {
        return $this->tree($allProductCategory, $pid, $is_top, $select_id, $top_name);
    }

    /**
     * 获取子级
     */
    public function getChildProductCategory($id = 0) {
        $where = [];
        $where[] = ['is_deleted', '=', '0'];
        $where[] = ['pid', '=', $id];
        $data = ['where' => $where];
        $allProductCategory = $this->getList($data);
        if($allProductCategory != null){
            $allProductCategory = $allProductCategory->toArray();
        } else {
            $allProductCategory = [];
        }
        return $allProductCategory;
    }

    /**
     * 获取子级
     */
    public function getChildProductCategorys($id = 0) {
        $category = $this->find($id);
        $result = [];
        if($category != null){
            $category = $category->toArray();
            $allProductCategory = $this->allProductCategory();
            $category['level'] = 0;
            $treeAll = [];
            $allProductCategory = $this->treeAll($allProductCategory, 0, false, $treeAll, $category);
            $result[] = $category;
            $this->getChildCategory($allProductCategory, $category, $result);
        }
        return $result;
    }

    /**
     * 获取子级
     */
    public function getChildProductCategoryIds($id = 0) {
        $ProductCategory_ids = $this->getChildProductCategorys($id);
        $result = [];
        foreach ($ProductCategory_ids as $key => $value) {
            $result[] = $value['id'];
        }
        return $result;
    }

    private function getChildCategory($cate, $category, &$r){
        foreach($cate as $key => $val){
            if($val['pid'] == $category['id']){
                $r[] = $val;
                $this->getChildCategory($cate, $val, $r);
            }
        }
        return $r;
    }


    /**
     * 查询父级分类
     * @param int $category_id
     * @return array
     */
    public function getParentCategory($ProductCategory_id, $self = true)
    {
        $cache_key = CacheService::cacheKey('parent_ProductCategory', ['ProductCategory_id' => $ProductCategory_id, 'self' => $self]);
        $data = CacheService::cacheGet($cache_key);
        if($data == null){
            $data = [];
            //查询当前分类
            $ProductCategory = $this->findOne([['id', '=', $ProductCategory_id], ['is_enable', '=', '1']]);
            if(!empty($ProductCategory)){
                if($self){
                    array_unshift($data, $ProductCategory);
                }
                do{
                    //查询父级分类
                    $parent = $this->findOne([['id', '=', $ProductCategory['pid']], ['is_enable', '=', '1']]);
                    if (!empty($parent)){
                        array_unshift($data, $parent);
                        $ProductCategory = $parent;
                    }
                }
                while (!empty($parent));
            }
            CacheService::cachePut($cache_key, $data);
        }
        return $data;
    }

    public function getCategoryTree(){
        $category_list = $this->getList([['is_deleted', '=', '0']], [['id', 'desc']]);
        return $this->getTree($category_list, '0');
    }

    public function getTree($data, $pId)
    {
        $html = '';
        $request = Yii::$app->request;
        $cate = $request->get('cate');
        foreach($data as $k => $v)
        {
            if($v['pid'] == $pId)
            { 
                if($v['id'] == $cate){
                    $class = 'current';
                } else {
                    $class = '';
                }
                //父亲找到儿子
                $html .= "<li class='cate_item $class'><a href='/product?cate=" . $v['id'] ."'>".$v['name'].'</a>';
                $html .= $this->getTree($data, $v['id']);
                $html = $html."</li>";  
            }
        }
        return $html ? '<ul>'.$html.'</ul>' : $html ;
    }
}
