<?php
namespace App\Http\Controllers\Admin\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Helper\Base as HelperBase;
use Auth;
use Excel;
use App\Libs\Service\OptionService;

class OptionController extends Controller
{

     /**
     * 添加属性分类
     *
     * @return void
    */
    public function addOption(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $model = $request->all();
        $admin_user = \Auth::guard('admin')->User();
        $model['admin_id'] = $admin_user->id;
        $option_service = OptionService::getInstance();
        $result = $option_service->createOption($model);
        if($result['status'] == true){
            $result['code'] = '200';
        } 
        return json_encode($result);
    }

    /**
     * 编辑属性分类
     *
     * @return void
    */
    public function editOption(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $model = $request->all();
        $admin_user = \Auth::guard('admin')->User();
        $model['admin_id'] = $admin_user->id;
        $option_service = OptionService::getInstance();
        $result = $option_service->updateOption($model);
        if($result['status'] == true){
            $result['code'] = '200';
        } 
        return json_encode($result);
    }

    /**
     * 加载属性分类
     *
     * @return void
    */
    public function loadOption(Request $request)
    {
        $result = ['code' => '2x1', 'message' => ''];
        $id = $request->id;
        $option_service = OptionService::getInstance();
        $option = $option_service->findOption([['id', '=', $id]]);
        if($option == null){
            $result['message'] = '属性分类不存在！';
            return json_encode($result);
        }
        //创建成功
        $result['code'] = '200';
        $result['data'] = $option->toArray();
        return json_encode($result);
    }
}