<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Helper\Base as HelperBase;
use Hash;
use Validator;
use Auth;
use App\Models\Admin\Role;

class RoleController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $name = trim($request->name);

        $form = $request->all();

        $role_model = Role::orderBy('created_at', 'desc');

        if(isset($request->name) && $name !== ''){
             $role_model =  $role_model->where('name', '=', $name);
        }

        //当前页数据
        $rolelist = $role_model->get();

        $view = View('admin.role.index');

        $view->with("rolelist", $rolelist);

        $view->with("form", $form);

        $view->with("title", "角色");

        return $view;

    }

    /**
     * 删除用户
     *
     * @return string
    */
    public function remove(Request $request, $id)
    {
        if($id == nnull){
            return redirect()->back()->withInput()->with('message', '角色不存在');
        }

        $role = Role::find($id);

        if($role == null){
            return redirect()->back()->withInput()->with('message', '角色不存在');
        }

        $role->delete();

        return redirect()->back()->withInput()->with('message', '删除角色成功');
    }

    /**
     * 添加角色
     *
     * @return void
    */
    public function add(Request $request){
        $view = View("admin.role.add");
        $form = $request->all();
        if($request->isMethod('post')){ 
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'display_name' => 'required',
                'description' => 'required'
            ]);
            if($validator->fails()){
                $message = ['type' => 'error'];
                $errors = $validator->errors()->all();
                $message['message'] = implode(' ', $errors);
                $view->with('message', $message);
            } else {
                $role = new Role();
                $role->name = trim($request->name);
                $role->display_name = trim($request->display_name);
                $role->description = trim($request->description);
                $role->save();
                return redirect(route("admin_role"));
            }
        }
        $view->with("title", "添加角色");
        $view->with("form", $form);
        return $view;
    }

    /**
     * 编辑角色
     *
     * @return void
    */
    public function edit(Request $request, $id){
        if(empty($id)){
            return redirect(route("admin_role"));
        }
        $role = Role::find($id);
        if(empty($role)){
            return redirect(route("admin_role"));
        }
        $form = $role->toArray();
        if($request->isMethod('post')){ 
            $form = $request->all();
            $validator_role = [];
            if(isset($request->name)){
                $role->name = trim($request->name);
                $validator_role['name'] = 'required';
            }
            if(isset($request->display_name)){
                $role->display_name = trim($request->display_name);
                $validator_role['display_name'] = 'required';
            }
            if(isset($request->description)){
                $role->description = trim($request->description);
                $validator_role['description'] = 'required';
            }
            if(isset($request->status)){
                $role->status = trim($request->status);
                $validator_role['status'] = 'integer';
            }
            $validator = Validator::make($request->all(), $validator_role);
            if($validator->fails()){
                $message = ['type' => 'error'];
                $errors = $validator->errors()->all();
                $message['message'] = implode(' ', $errors);
                $view->with('message', $message);
            } else {
                $role->save();
            }
            return redirect(route("admin_role"));
        }

        $view = View("admin.role.edit");

        $view->with("title", "编辑角色");

        $view->with("form", $form);

        return $view;

    }
}