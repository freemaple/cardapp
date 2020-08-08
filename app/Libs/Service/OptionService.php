<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use DB;
use HelperBase;
use App\Repository\Base as BaseRepository;

class OptionService
{
  /**
     * @var Singleton reference to singleton instance
     */
  private static $_instance;  

  
  /**
     * 构造函数私有，不允许在外部实例化
     *
    */
  private function __construct(){
  }

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
     * 产品sku
     * @param  integer $pageSize 每页个数
     * @param  array   $where    查询条件
     * @param  array   $orderBy  排序
     * @param  array   $field    查询字段
     * @return array
     */
    public function findOption($where = []){
        $option_repository = BaseRepository::model("Product\Option");
        $option = $option_repository->findOne($where);
        return $option;
    }

      /**
     * 产品sku
     * @param  integer $pageSize 每页个数
     * @param  array   $where    查询条件
     * @param  array   $orderBy  排序
     * @param  array   $field    查询字段
     * @return array
     */
    public function optionList($data = []){
        $option_value_repository = BaseRepository::model("Product\Option");
        $option_list = $option_value_repository->get($data);
        return $option_list;
    }


    /**
     * 产品sku
     * @param  integer $pageSize 每页个数
     * @param  array   $where    查询条件
     * @param  array   $orderBy  排序
     * @param  array   $field    查询字段
     * @return array
     */
    public function optionValueList($data = []){
        $option_value_repository = BaseRepository::model("Product\OptionValue");
        $option_value_list = $option_value_repository->get($data);
        return $option_value_list;
    }

    /**
     * 产品sku
     * @param  integer $pageSize 每页个数
     * @param  array   $where    查询条件
     * @param  array   $orderBy  排序
     * @param  array   $field    查询字段
     * @return array
     */
    public function findOptionValue($where = []){
        $option_value_repository = BaseRepository::model("Product\OptionValue");
        $option_value = $option_value_repository->findOne($where);
        return $option_value;
    }

    /**
     * 添加Option
     * @param  array $data 
     * @return array
     */
    public function createOption($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required',
            'admin_id' => 'required'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $option_repository = BaseRepository::model('Product\Option');

        $name_option = $option_repository->findOne([['name', '=', $data['name']]]);

        if($name_option != null){
            $result['message'] = '对不起，名称已存在！';
        }

        //上传成功,插入数据
        $insert_data = [
            'admin_id' => $data['admin_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'enable' => $data['enable']
        ]; 
       
        $option_repository->insert($insert_data);
        $result['status'] = true;
        $result['message'] = '保存成功';
        return $result;
    }

    /**
     * updateOption
     * @param  [array] $data 
     * @return array
     */
    public function updateOption($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $option_repository = BaseRepository::model('Product\Option');

        $name_option = $option_repository->findOne([['name', '=', $data['name']]]);

        if($name_option != null){
            $result['message'] = '对不起，名称已存在！';
        }

        //更新数据
        $update_data = [
            'name' => $data['name'],
            'description' => $data['description'],
            'enable' => $data['enable']
        ]; 
       
        $product = $option_repository->update($update_data, [['id', '=', $data['id']]]);
        $result['status'] = true;
        $result['message'] = '保存成功';
        return $result;
    }
}