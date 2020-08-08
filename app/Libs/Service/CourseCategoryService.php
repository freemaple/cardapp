<?php
namespace App\Libs\Service;

use App\Repository\Base as BaseRepository;
use Validator;
use Storage;
use Helper;
use Auth;
use App\Libs\Service\CacheService;
use App\Jobs\CourseView;

class CourseCategoryService
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
     *分类列表
     *
     * @return array()
    */        

    public function getList($data){
        $course_category_repository = BaseRepository::model("Course\Category");
        $course_category_list = $course_category_repository->get($data);
        return $course_category_list;
    }

    /**
     * 根据ID查询课程
     * @param  int $id 
     * @return array
     */
    public function find($id){
        if(empty($id)){
            return null;
        }
        $course_category_repository = BaseRepository::model("Course\Category");
        $course = $course_category_repository->find($id);
        return $course;
    }

    /**
     * 根据ID查询课程
     * @param  int $id 
     * @return array
     */
    public function findOne($where){
        $course_category_repository = BaseRepository::model("Course\Category");
        $course_category = $course_category_repository->findOne($where);
        return $course_category;
    }

    /**
     * 添加课程
     * @param  array $data 数据
     * @param  object $file 表单文件上传数据
     * @return array
     */
    public function create($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required'
        ]);
        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }
        //上传成功,插入数据
        $insert_data = [
            'name' => trim($data['name']),
            'description' => trim($data['description'])
        ]; 

        if(isset($data['pid'])){
            $insert_data['pid'] = trim($data['pid']);
        }

        if(isset($data['enable'])){
            $insert_data['enable'] = trim($data['enable']);
        }

        $course_category_repository = BaseRepository::model("Course\Category");
        $course_category_repository->insert($insert_data);
        $result['status'] = true;
        return $result;
    }

    /**
     * 更新课程
     * @param  array $data 数据
     * @param  object $file 表单文件上传数据
     * @return array
     */
    public function update($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        //更新数据
        $update_data = [
            'name' => trim($data['name']),
            'description' => trim($data['description'])
        ]; 

        if(isset($data['pid'])){
            $update_data['pid'] = trim($data['pid']);
        }

        if(isset($data['enable'])){
            $insert_data['enable'] = trim($data['enable']);
        }
        
        $course_category_repository = BaseRepository::model("Course\Category");
        $course_category_repository->update($update_data, [['id', '=', $data['id']]]);
        $result['status'] = true;
        return $result;
    }

    /**
     * 删除课程
     * @param  int $id 
     * @return boolean
     */
    public function remove($id = null)
    {
        if($id == null){
            return false;
        }
        $course_category_repository = BaseRepository::model("Course\Category");
        return $course_category_repository->delete([['id', '=', $id]]);
    }

    public function tree($arr, $p_id = '0') {
        $tree = array();
        foreach($arr as $row){
            if($row['pid'] == $p_id){
                $tmp = $this->tree($arr,$row['id']);
                if($tmp){
                    $row['children'] = $tmp;
                }
                $tree[] = $row;                
            }
        }
        return $tree;        
    }

    public function allCategory() {
        $where = [['enable', '=', '1']];
        $data = ['where' => $where];
        $allCategory = $this->getList($data);
        if($allCategory != null){
            $allCategory = $allCategory->toArray();
        } else {
            $allCategory = [];
        }
        return $allCategory;
    }

    public function getTreeCategory() {
        $allCategory = $this->allCategory();
        return $this->tree($allCategory);
    }

    /**
     * 获取子级
     */
    public function getChildCategory($id = 0) {
        $where = [['enable', '=', '1'], ['pid', '=', $id]];
        $data = ['where' => $where];
        $allCategory = $this->getList($data);
        if($allCategory != null){
            $allCategory = $allCategory->toArray();
        } else {
            $allCategory = [];
        }
        return $allCategory;
    }

    /**
     * 查询父级分类
     * @param int $category_id
     * @return array
     */
    public function getParentCategory($category_id, $self = true)
    {
        $cache_key = CacheService::cacheKey('parent_category', ['category_id' => $category_id, 'self' => $self]);
        $data = CacheService::cacheGet($cache_key);
        if($data == null){
            $data = [];
            //查询当前分类
            $category = $this->findOne([['id','=',$category_id],['enable','=','1']]);
            if(!empty($category)){
                if($self){
                    array_unshift($data, $category);
                }
                do{
                    //查询父级分类
                    $parent = $this->findOne([['id','=',$category['pid']],['enable','=','1']]);
                    if (!empty($parent)){
                        array_unshift($data, $parent);
                        $category = $parent;
                    }
                }
                while (!empty($parent));
            }
            CacheService::cachePut($cache_key, $data);
        }
        return $data;
    }
}