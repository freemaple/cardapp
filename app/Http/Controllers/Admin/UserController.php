<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;
use App\Libs\Service\AdminService;
use App\Models\Admin\Role;

class UserController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $admin_service = AdminService::getInstance();

        $username = Helper::inputStr(trim($request->username));

        $pageSize = 20;

        //当前页数据
        $userlist = $admin_service->userList($username, $pageSize);

        $userlist->appends($request->all());

        $pager = $userlist->links();

        $view = View('admin.user.index');

        $view->with("userlist", $userlist);

        $view->with("username", $username);

        $view->with("pager", $pager);

        $view->with("title", "用户");

        return $view;

    }

    /**
     * 删除用户
     *
     * @return string
    */
    public function remove(Request $request){

        $admin_service = AdminService::getInstance();

        $id = intval(trim($request->id)) ? intval(trim($request->id)) : "";

        $ids = Helper::inputStr(trim($request->ids));

        $result = $admin_service->remove($id, $ids);
      
        return json_encode($result);

    }

    /**
     * 添加用户
     *
     * @return void
    */
    public function add(Request $request){

        $roles = Role::get();

        $view = View("admin.user.add");

        $view->with("title", "添加用户");

        $view->with("roles", $roles);

        return $view;

    }

    /**
     * 编辑用户
     *
     * @return void
    */
    public function edit(Request $request, $id){

        if(empty($id)){

            return redirect(route("admin_user"));
        }

        $roles = Role::get();

        $admin_service = AdminService::getInstance();

        $user = $admin_service->userItem($id);

        $user_role = $user->roles()->get();

        $roleid = [];

        foreach($user_role as $r){

            $roleid[] = $r->id;
        }


        $view = View("admin.user.edit");

        $view->with("title", "添加用户");

        $view->with("user", $user);

        $view->with("roles", $roles);

        $view->with("roleid", $roleid);

        return $view;

    }

    /**
     * 保存用户
     *
     * @return string
    */
    public function save(Request $request){

        $admin_service = AdminService::getInstance();

        $id = Helper::inputStr(trim($request->id));

        $username = Helper::inputStr(trim($request->username));

        $password = Helper::inputStr(trim($request->userpwd));

        $status = intval(trim($request->status)) ? intval(trim($request->status)) : 0;

        $validator = Validator::make($request->all(), [
          'username' => 'required|max:50',
          'userpwd' => 'min:6|max:50'
        ]);

        if($validator->fails()){

            $result = array();

            $result['code'] = "0x00001";

            return json_encode($result);
        }

        $data=array(
            'username' => $username,
            'status' => $status
        );

        if($password != null){
            $data['password'] = Hash::make($password);
        }

        $user_role = null;

        if(isset($request->user_role)){
            if(is_array($request->user_role)){
                $user_role = [];
                foreach ($request->user_role as $roleid => $value) {
                    if($value){
                       $user_role[] = $roleid;
                    }
                }
            }
        }

        if(empty($id)){
            $result = $admin_service->addUserItem($data, $user_role);
        } else {
        	$data['id'] =  $id;
            $result = $admin_service->editUserItem($data, $user_role);
        }
        return json_encode($result);
    }

    /**
     * 注销
     *
     * @return void
    */
    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect(route("admin_auth"));
    }

    /**
     * 修改密码
     *
     * @return void
    */
     public function alertpwd(Request $request)
     {
        if($request->ajax()){

            $admin_service = AdminService::getInstance();

            $old_pwd = Helper::inputStr(trim($request->old_pwd));

            $new_pwd = Helper::inputStr(trim($request->new_pwd));

            $validator = Validator::make($request->all(), [
                'old_pwd' => 'required',
                'new_pwd' => 'required',
            ]);

            if($validator->fails()){

                $result = array();

                $result['code'] = "0x00001";

                exit(json_encode($result));
            }

            $user = Auth::guard('admin')->user();

            $data = array(
                'userid' => $user->id,
                'old_pwd' => $old_pwd,
                'new_pwd' => Hash::make($new_pwd)
            );

            $result = $admin_service->alertPwd($data);

            return json_encode($result);

        }

        $view = View("admin.user.alertpwd");

        $view->with("title", "修改密码");
     
        return $view;
    }
}