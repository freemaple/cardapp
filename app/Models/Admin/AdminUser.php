<?php

namespace App\Models\Admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

     /**
     * 获取用户对应的角色模型
     * @return role model
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role_users', 'admin_id');
    }

    /**
     * 获取用户对应的有效的角色
     * @return [type] [description]
     */
    public function getRoles(){
        $roles = $this->roles()->where('admin_roles.status', '=', '1')->get();
        return $roles;
    }

    /**
     * 获取用户对应的角色名称数组
     * @return role model
     */
    public function rolesName()
    {
        $role_array = [];
        $roles = $this->getRoles();
        if($roles != null){
            foreach($roles as  $s => $role){
                $role_array[] = $role->name;
            }
        }
        return $role_array;
    }

    /**
     * 是否包含角色
     * @param  [stirng|collection]  $role 
     * @return boolean
     */
    public function hasRole($roleName = null)
    {
        $rolesName = $this->rolesName();
        $is_role = false;
        if(!is_array($roleName)){
            $roleList = [];
            $roleList[] = $roleName;
        } else {
             $roleList = $roleName;
        }
        foreach ($roleList as $key => $role) {
            if(in_array($role, $rolesName)){
                return true;
            }
        }
        return false;
    }

    /**
     * 查询角色
     * @param  [stirng|collection]  $role 
     * @return boolean
     */
    public function findRole($roleName)
    {
        return $this->roles()->where('admin_roles.status', '=', '1')->where('name', '=', $roleName)->first();
    }
}
