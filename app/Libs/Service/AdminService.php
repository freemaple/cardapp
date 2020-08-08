<?php
namespace App\Libs\Service;

use App\Repository\Base as RepositoryBase;
use Auth;

class AdminService
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
     * 后台用户列表
     *
     * @return array()
    */
    public function userList($username, $pageSize){

        $where = [];

        if($username != ""){
            $where[] = ['username', 'like', '%'. sprintf("%s", $username). '%'];
        }

        $orderBy = [["created_at", 'desc']];

        $field = ['id', 'username', 'status', 'created_at'];

        $data = [
            'where' => $where,
            'field' => $field,
            'orderBy' => $orderBy,
            'pageSize' => $pageSize
        ];

        //当前页数据
        $userlist = RepositoryBase::model('Admin\AdminUser')->get($data);

        return $userlist;
    }


    /**
     * 添加用户操作
     *
     * @return array()
    */
    public function addUserItem($data, $user_role = null){

        $result = [];

        $where = [['username', '=', $data['username']]];

        $field = ['id'];

        $user = RepositoryBase::model('Admin\AdminUser')->findOne($where, $field);

        if(!empty($user)){

            $result['code'] = "0x00001";

            $result['msg'] = "对不起,用户名已经存在";

            return $result;
        }

        $user = RepositoryBase::model('Admin\AdminUser')->insert($data);

        if($user != null){
            if(isset($user_role) && is_array($user_role) && $user_role != null){
                $user->roles()->sync($user_role);
            }
        }

        $result['code'] = "0x00000";

        $result['id'] = $user->id;

        $result['msg'] = "添加成功";

        return $result;
    }

    /**
     * 编辑用户操作
     *
     * @return array()
    */
    public function editUserItem($data, $user_role = null){

        $result = [];

        $where = [['username', '=', $data['username']], ['id', '!=', $data['id']]];

        $field = ['id'];

        $user = RepositoryBase::model('Admin\AdminUser')->findOne($where, $field);

        if(!empty($user)){

            $result['code'] = "0x00001";

            $result['msg'] = "对不起,用户名已经存在";

            return $result;
        }

        $where = [['id', '=', $data['id']]];

        RepositoryBase::model('Admin\AdminUser')->update($data, $where);

        $admin_user = RepositoryBase::model('Admin\AdminUser')->findOne([['id', '=', $data['id']]]);

        if(isset($user_role) && is_array($user_role) && $user_role != null){
            if(empty($user_role)){
                $admin_user->roles()->delete();
            } else {
                $admin_user->roles()->sync($user_role);
            }
        }

        $result['code'] = "0x00000";

        $result['msg'] = "保存成功";

        return $result;
    }

    /**
     * 删除用户
     *
     * @return string
    */
    public function remove($id, $ids = null){

        $result = array();

        if(empty($id) && empty($ids)){

            $result['code'] = "0x0000x";
      
            return $result;

        }
        //批量删除
        if(!empty($ids)){
        	$id = explode(",", $ids);
        } 

        RepositoryBase::model('Admin\AdminUser')->destroy($id);

        $result['code'] = "0x00000";
      
        return $result;

    }

    /**
     * 根据id获取用户信息
     *
     * @return void
    */
    public function userItem($id){

        $where = [['id', '=', $id]];

        $field = ['id', 'username', 'status', 'created_at'];

        $user = RepositoryBase::model('Admin\AdminUser')->findOne($where, $field);

        return $user;

    }

    /**
     * 修改密码
     *
     * @return void
    */
    public function alertPwd($data){

    	$result = [];

    	if (!Auth::guard('admin')->attempt(array('id' => $data['userid'], 'password' => $data['old_pwd']))){

            $result['code'] = "0x00001";
            
            $result['msg'] = '对不起,旧密码错误';

        } else {

        	$where = [['id', '=', $data['userid']]];

        	RepositoryBase::model('Admin\AdminUser')->update(['password' => $data['new_pwd']], $where);

        	$result['code'] = "0x00000";

        	$result['msg'] = "保存成功";
        }

        return $result;
    }
}