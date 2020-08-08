<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Repository\User as UserRepository;

class CustomerService
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
     * 用户分页列表
     * @param  integer $pageSize 每页个数
     * @param  array   $where    查询条件
     * @param  array   $orderBy  排序
     * @param  array   $field    查询字段
     * @return array
     */
    public function userList($data){
        $user_repository = UserRepository::getInstance();
        $user_list = $user_repository->userList($data);
        return $user_list;
    }
}