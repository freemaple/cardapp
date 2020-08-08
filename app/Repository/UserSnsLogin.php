<?php
namespace App\Repository;

use App\Models\User\User as UserModel;
use App\Repository\Base as BaseRepository;

class UserSnsLogin
{

	private static $SnsRepository;

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
        static::$SnsRepository = BaseRepository::model("User\UserSnsLogin");
        return self::$_instance;    
    }  

	/**
	 * 查找sns登录数据
	 * @param  array  $where 查询条件
	 * @param  array  $field 查询列
	 * @return UserSnsLogin model 
	 */
	public function find($where = [], $field = [])
	{
		return static::$SnsRepository->findOne($where, $field);
	}

	/**
	 * 插入sns登录数据
	 * @param  array $data 插入数据
	 * @return UserSnsLogin model       
	 */
	public function insert($data)
	{
		return static::$SnsRepository->insert($data);
	}

	/**
	 * 删除sns登录数据
	 * @param  array $data 删除数据条件
	 * @return boolean
	 */
	public function dalete($data)
	{
		return static::$SnsRepository->delete($data);
	}
}	
?>