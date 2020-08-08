<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Libs\Service\BannerService;
use \App\Helper\HelperImage;

class BannerController extends BaseController
{

    /**
     * banner列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        //请求数据
        $location = trim($request->location);
        $banner_service = BannerService::getInstance(); 
        //banner数据
        $bannerlist = $banner_service->bannerList($location);
        //返回视图
        $view = view('admin.banner.index', [
            'title' => "banner列表",
            'bannerlist' => $bannerlist,
            'location' => $location
        ]);
        return $view;
    }

    /**
     * 添加banner
     *
     * @return void
    */
    public function add(Request $request)
    {
        $banner_service = BannerService::getInstance(); 
        $message = [];
        $model = null;
        //判断是否是post请求提交
        if($request->isMethod('post')){
            $model = $request->all();
            $file = $request->file('image');
            //创建banner
            $result = $banner_service->createBanner($model, $file);
            //创建成功
            if($result['status'] == true){
                return redirect(route("admin_banner"));
            //创建失败
            } else {
                $message = ['type'=>'error', 'message'=>$result['message']];
            }
        } 
        //返回视图
        $view = view('admin.banner.add', [
            'message' => $message,
            'model' => $model
        ]);
        return $view;
    }

    /**
     * 添加banner
     *
     * @return void
    */
    public function edit(Request $request, $id)
    {
        if(empty($id)){
            return redirect(route("admin_banner"));
        }
        $banner_service = BannerService::getInstance(); 
        //根据id获取banner
        $model = $banner_service->findBanner($id);
        //检查banner是否存在
        if(empty($model)){
            return redirect(route("admin_banner"));
        } 
        $message = [];
        //判断是否是post请求提交
        if($request->isMethod('post')){
            $model = $request->all();
            //更新banner
            $model['id'] = $id;
            $file = $request->file('image');
            $result = $banner_service->updateBanner($model, $file);
            //更新成功
            if($result['status'] == true){
                return redirect(route("admin_banner"));
            //更新失败
            } else {
                $message = ['type' => 'error', 'message' => $result['message']];
            }
        } else{
            $model['image'] = !empty($model['image']) ? \HelperImage::storagePath($model['image']) : '';
        }
        //返回视图
        $view = view('admin.banner.edit', [
            'message' => $message,
            'model' => $model
        ]);
        return $view;
    }

    /**
     * 添加banner
     *
     * @return void
    */
    public function remove(Request $request, $id)
    {
        if(!empty($id)){
            $banner_service = BannerService::getInstance(); 
            $banner_service->removeBanner($id);
        }
        return redirect(route("admin_banner")); 
    }

    
    /**
     * banner位置
     * @return [type] [description]
     */
    public function bannerLocation(){
        $banner_service = BannerService::getInstance(); 
        //banner位置数据
        $banner_location = $banner_service->bannerLocation();
        //返回视图
        $view = view('admin.banner.location', [
            'title' => "banner位置",
            'banner_location' => $banner_location
        ]);
        return $view;
    }

    /**
     * 添加banner位置
     * @param Request $request 
     */
    public function addLocation(Request $request)
    {
        $banner_service = BannerService::getInstance();
        $message = [];
        $model = null;
        //判断是否是post请求提交
        if($request->isMethod('post')){
            $model = $request->all();
            //创建banner
            $result = $banner_service->createBannerLocation($model);
            //创建成功
            if($result['status'] == true){
                return redirect(route("admin_banner_location"));
            //创建失败
            } else {
                $message = ['type'=>'error', 'message'=>$result['message']];
            }
        }
        //返回视图
        $view = view('admin.banner.location_add', [
            'message' => $message,
            'model' => $model
        ]);
        return $view;
    }

    /**
     * 编辑banner位置
     * @param  Request $request 
     * @param  int $id 
     * @return view           
     */
    public function editLocation(Request $request, $id)
    {
        if(empty($id)){
            return redirect(route("admin_banner_location"));
        }
        $banner_service = BannerService::getInstance();
        //根据id获取banner
        $model = $banner_service->findBannerLocation($id);
        //检查banner是否存在
        if(empty($model)){
            return redirect(route("admin_banner_location"));
        } 
        $message = [];
        //判断是否是post请求提交
        if($request->isMethod('post')){
            $model = $request->all();
            //更新banner
            $model['id'] = $id;
            $result = $banner_service->updateBannerLocation($model);
            //更新成功
            if($result['status'] == true){
                return redirect(route("admin_banner_location"));
            //更新失败
            } else {
                $message = ['type' => 'error', 'message' => $result['message']];
            }
        } 
        //返回视图
        $view = view('admin.banner.location_edit', [
            'message' => $message,
            'model' => $model
        ]);
        return $view;
    }
}