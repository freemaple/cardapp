<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Hash;
use Validator;
use Auth;
use Helper;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{

    /**
     * 登录页面
     *
     * @return void
     */
    public function index()
    {
        $view = View('admin.auth.login');

        $view->with("title", "登录");

        return $view;

    }

    /**
     * 登录操作
     *
     * @return string
     */
    public function login(Request $request)
    {

        $result = array();

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'pwd' => 'required'
        ]);

        if($validator->fails()){

          $result['code'] = "0x00001";

          $result['errors'] = json_encode($validator->errors());
       
          return json_encode($result);

        }

        $username = Helper::inputStr(trim($request->username));

        $pwd = Helper::inputStr(trim($request->pwd));

        //登录校验
        if(Auth::guard('admin')->attempt(array('username' => $username, 'password' => $pwd, 'status' => '1'))){
  
          $result['code'] = "0x00000";
       
          return json_encode($result);
            
        } else {

          $result['code'] = "0x00001";
 
          $result['msg'] = "用户名或密码错误";
      
          return json_encode($result);

        }

    }
}