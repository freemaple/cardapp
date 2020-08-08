<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Doc\Doc;
use App\Libs\Service\DocService;
use Helper;
use Validator;
use Auth;

class DocController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $doc_list = Doc::select('doc.*', 'doc_catalog.name as catalog_name')->leftjoin('doc_catalog', 'doc_catalog.id', '=', 'doc.catalog_id');

        $name = trim($request->name);

        if($name != null){
            $doc_list = $doc_list->where('name', $name);
        }

        $doc_list = $doc_list->get();

        $form = $request->all();

        $view = View('admin.doc.index');

        $view->with("doc_list", $doc_list);

        $view->with("form", $form);

        $view->with("title", "文档");

        return $view;

    }

    public function add(Request $request)
    {
        set_time_limit(0);
        $message = [];
        $form = null;
        $doc_service = DocService::getInstance(); 
        //判断是否是post请求提交
        if($request->isMethod('post')){
            $form = $request->all();
            //创建banner
            $result = $doc_service->create($form, $request);
            //创建成功
            if($result['status'] == true){
                return redirect(route("admin_doc"));
            //创建失败
            } else {
                $message = ['type'=>'error', 'message' => $result['message']];
            }
        } 
        $doc_catalog = $doc_service->getDocCatalog();
        //返回视图
        $view = view('admin.doc.add', [
            'message' => $message,
            'form' => $form,
            'doc_catalog' => $doc_catalog
        ]);
        return $view;
    }

    public static function descriptionImage($description){
        if($description){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc ->loadHTML('<?xml encoding="UTF-8">' . $description);//$str为一段HTML代码
            libxml_clear_errors();
            libxml_use_internal_errors($libxml_previous_state);
            $image = $doc->getElementsByTagName('img');
            $doc->encoding = 'UTF-8';
            if(count($image)){
                foreach ($image as $key => $i) {
                    $src = $i->getAttribute('src');
                    $src = \HelperImage::storagePath($src);
                    $i->setAttribute('src', $src);
                }
                $description = $doc->saveHTML();
            }
        }
        return $description;
    }

    /**
     * 添加banner
     *
     * @return void
    */
    public function edit(Request $request, $id)
    {
        set_time_limit(0);
        if(empty($id)){
            return redirect(route("admin_doc"));
        }
        $doc_service = DocService::getInstance();
        $message = [];
        //判断是否是post请求提交
        if($request->isMethod('post')){
            $form = $request->all();
            //更新course
            $form['id'] = $id;
            $result = $doc_service->update($form, $request);
            //更新成功
            if($result['status'] == true){
                return redirect(route("admin_doc"));
            //更新失败
            } else {
                $message = ['type' => 'error', 'message' => $result['message']];
            }
        } else {
            //根据id获取course
            $form = $doc_service->find([['id', '=', $id]]);
            //检查是否存在
            if(empty($form)){
                return redirect(route("admin_doc"));
            }
        }
        $doc_catalog = $doc_service->getDocCatalog();
        //返回视图
        $view = view('admin.doc.edit', [
            'message' => $message,
            'form' => $form,
            'doc_catalog' => $doc_catalog
        ]);
        return $view;
    }
}