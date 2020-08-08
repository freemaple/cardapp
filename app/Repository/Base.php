<?php
namespace App\Repository;

use Illuminate\Database\Eloquent\Model;
use App;

class Base
{
	/**
	 * 注入ORM对象
	 * 
	 * @var object
	 */
	private static $model;
	
	/**
	 * 已经加载的model数据
	 * @var array
	 */
	private static $_model_load = [];

	/**
     * @var Singleton reference to singleton instance
     */
	private static $_instance;  
	
	/**
     * 构造函数私有，不允许在外部实例化
     *
    */
	private function __construct()
	{
		
	}

	/**
     * 防止对象实例被克隆
     *
     * @return void
    */
	private function __clone() {

	}
	
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
	 * 注入ORM model
	 * 
	 * @param array $data
	 * @return obj
	 */
	public static function model($model_name)
	{
		if(empty(self::$_model_load[$model_name])){
			self::$model = App::make('\App\Models\\'.$model_name);
			self::$_model_load[$model_name] = self::$model;
		}else{
			self::$model = self::$_model_load[$model_name];
		}
		return self::getInstance();
	}
	
	/**
	 * 添加一条新记录
	 * 
	 * @param array $data
	 * @return obj
	 */
	public function insert($data)
	{
		$new_model = new self::$model;
		foreach($data as $k => $value){
			$new_model->$k = $value;
		}
		$new_model->save();
		return $new_model;
	}
	/**
	 * 批量赋值
	 * @param array $data
	 * @return obj
	 */
	public function created($data)
	{
		$new_model = self::$model;
		return $new_model->create($data);
		
	}
	/**
	 * 主键删除数据
	 * 
	 * @param int 主键ID
	 * @return boolean
	 */
	public function destroy($key)
	{
		$new_model = self::$model;
		//通过主键删除数据
		$result = $new_model->destroy($key);
		return $result;
	}
	
	/**
	 * 简单查询删除数据
	 * 
	 * @param array where查询条件
	 * @param string $key
	 * @return boolean
	 */
	public function delete($where)
	{
		$new_model = self::$model;
		//通过where简单条件删除
		foreach ($where as $w){
			$new_model = $new_model->where($w[0], $w[1], $w[2]);
		} 
		$result = $new_model->delete(); 
		return $result;
	}
	/**
	 *简单更新数据
	 *
	 * @param array
	 * @param array
	 * @return boolean
	 */
	public function update($data, $where = [])
	{
		$new_model = self::$model;
		//通过where简单条件更新
		foreach ($where as $w){
			$new_model = $new_model->where($w[0], $w[1], $w[2]);
		}
		$result = $new_model->update($data);
		return $result;
	}
	/**
	 * 通过主键获取模型
	 * @param int $key
	 * @param array 查询字段
	 * @return Object|boolean
	 */
	public function find($key, $field = array())
	{
		$new_model = self::$model;
		//查询指定字段
		if(!empty($field)){
			$new_model = $new_model->select($field);
		}
		//通过主键获取单个模型		
		$result = $new_model->find($key);
		return $result;
	}
	/**
	 * 通过匹配条件获取单个模型
	 * @param array $where
	 * @param array 查询字段
	 * @return object|boolean
	 */
	public function findOne($where, $field = array())
	{
		$new_model = self::$model;
		//查询指定字段
		if(!empty($field)){
			$new_model = $new_model->select($field);
		}
		//通过where简单条件匹配
		foreach ($where as $w){
			$new_model = $new_model->where($w[0], $w[1], $w[2]);
		}
		$result = $new_model->first();
		
		return $result;
	}
	/**
	 * 查询指定数量数据，为空查询全部
	 * @param array $where
	 * @param array $orderBy  排序
	 * @param array 查询字段
	 * @param number $pageSize 每页展示数量  0表示不分页
	 * @return object|boolean
	 */
	public function get($data)
	{
		$result = $this->_query($data);
		return $result;
	}
	/**
	 * 统计数量
	 * @param array $where
	 * @return int|boolean
	 */
	public function count($where, $whereIn = [], $between = [])
	{
		$new_model = self::$model;
		//通过where简单条件匹配
		foreach ($where as $w){
			$new_model = $new_model->where($w[0], $w[1], $w[2]);
		}
		//通过wherein简单匹配
		if(!empty($whereIn)){
			$new_model = $new_model->whereIn($whereIn[0], $whereIn[1]);
		}
		
		if(!empty($between)){
			$new_model = $new_model->whereBetween($between[0], $between[1]);
		}
		
		$result = $new_model->count();
		return $result;
	}
	private function _query($data)
	{
		$new_model = self::$model;
		//查询指定字段
		if(!empty($data['field'])){
			$new_model = $new_model->select($data['field']);
		}
		//通过where简单条件匹配
		if(!empty($data['where'])){
			foreach ($data['where'] as $w){
				$new_model = $new_model->where($w[0], $w[1], $w[2]);
			}
		}
		//通过wherein简单匹配
		if(!empty($data['whereIn'])){
			foreach ($data['whereIn'] as $i){
				$new_model = $new_model->whereIn($i[0], $i[1]);
			}
		}

		if(isset($data['leftjoin']) && $data['leftjoin'] != null){
			foreach ($data['leftjoin'] as $l){
				$new_model = $new_model->leftJoin($l[0], $l[1], $l[2], $l[3]);
			}
		}  

		//排序
		if(!empty($data['orderBy'])){
			foreach ($data['orderBy'] as $o){
				$new_model = $new_model->orderBy($o[0], $o[1]);
			}
		}
		//分组
		if(!empty($data['groupBy'])){
			foreach ($data['groupBy']as $g){
				$new_model = $new_model->groupBy($g);
			}
		}
		
		if(isset($data['limit']) && $data['limit'] != null){
			$new_model = $new_model->take($data['limit']);
		}

		if(isset($data['offset']) && $data['offset'] !== null){
			$new_model = $new_model->skip($data['offset']);
		}
		
		//分页获取数据
		if(isset($data['pageSize']) && $data['pageSize']){
			$result = $new_model->paginate($data['pageSize']);
		}
		else{
			$result = $new_model->get();
		}
		return $result;
	}
}