<?php
namespace App\Repository;

use App\Models\User\User as UserModel;
use App\Repository\Base as BaseRepository;

class User
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
	 * [用户分页数据]
	 * @param  array  $where 查询条件 [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @param  array  $field 查询列
	 * @return Model User
	 */
	public function userList($data)
	{
		$user = BaseRepository::model("User\User")->get($data);
		return $user;
	}

	/**
	 * [查找用户数据]
	 * @param  array  $where 查询条件 [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @param  array  $field 查询列
	 * @return Model User
	 */
	public function findUser($where = [], $field = [])
	{
		$user = BaseRepository::model("User\User")->findOne($where, $field);
		return $user;
	}

	/**
	 * 通过id查询用户数据
	 * @param  int $user_id 用户id
	 * @param  array  $field   查询列
	 * @return Model User
	 */
	public function find($user_id, $field = [])
	{
		$user = BaseRepository::model("User\User")->find($user_id, $field);
		return $user;
	}

	/**
	 * 通过email查询用户数据
	 * @param  string $email 用户邮箱
	 * @param  array  $field 查询列
	 * @return Model User
	 */
	public function findByEmail($user_email, $field = [])
	{
		$user = BaseRepository::model("User\User")->findOne([["email", '=', $user_email]], $field);
		return $user;
	}

	/**
	 * 更新用户数据
	 * @param  array $data  更新数据
	 * @param  array $where [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @return boolean
	 */
	public function updateUser($data, $where)
	{
		return BaseRepository::model('User\User')->update($data, $where);
	}

	/**
	 * 插入用户数据
	 * @param  array $data 插入数据 ['key1' => 'value1', 'key2' => 'value2']
	 * @return Model User | null
	 */
	public function insertUser($data = [])
	{
		if($data == null){
			return null;
		}
		return BaseRepository::model("User\User")->insert($data);
	}

	/**
	 * 查找用户激活数据
	 * @param  array  $where 查询条件 [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @param  array  $field 查询列
	 * @return UserActive model
	 */
	public function findUserActive($where = [], $field = [])
	{
		$model = BaseRepository::model("User\UserActive")->findOne($where, $field);
		return $model;
	}

	/**
	 * 插入用户激活数据
	 * @param  [type] $data 插入数据
	 * @return UserActive model
	 */
	public function insertUserActive($data = [])
	{
		if($data == null){
			return null;
		}
		return BaseRepository::model("User\UserActive")->insert($data);
	}

	/**
	 * 删除用户激活数据
	 * @param  array $where 查询条件 [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @return boolean
	 */
	public function daleteUserActive($where)
	{
		return BaseRepository::model("User\UserActive")->delete($where);
	}

	/**
	 * 查找忘记密码数据
	 * @param  array  $where 查询条件 [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @param  array  $field 查询列
	 * @return PasswordReset model
	 */
	public function findUserReset($where = [], $field = [])
	{
		return BaseRepository::model("User\PasswordReset")->findOne($where, $field);
	}

	/**
	 * 插入忘记密码数据
	 * @param  [type] $data 插入数据
	 * @return PasswordReset model
	 */
	public function insertUserReset($data = [])
	{
		if($data == null){
			return null;
		}
		return BaseRepository::model("User\PasswordReset")->insert($data);
	}

	/**
	 * 删除忘记密码数据
	 * @param  array  $where 查询条件 [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @return boolean
	 */
	public function daleteUserReset($where)
	{
		return BaseRepository::model("User\PasswordReset")->delete($where);
	}
}	
?>