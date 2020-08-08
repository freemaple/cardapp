<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\BaseController;
use App\Libs\Service\DocService;
use App\Models\Doc\Doc as DocModel;
use App\Models\Doc\DocCatalog;
use App\Cache\Help as HelpCache;

class HelpController extends BaseController
{
     /**
     * 帮助页
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $view = view('doc.index')->with([
            'title' => '关于我们'
        ]);
        return $view;
    }

     /**
     * 帮助页
     *
     * @return \Illuminate\Http\Response
     */
    public function school()
    {
        $doc_catalog = HelpCache::getDocCatalog();
        $view = view('doc.school')->with([
            'title' => '商学院',
            'doc_catalog' => $doc_catalog
        ]);
        return $view;
    }

     /**
     * 帮助页
     *
     * @return \Illuminate\Http\Response
     */
    public function catalogDoc($id)
    {
        $catalog = DocCatalog::where('enable', '1');
        if(is_numeric($id)){
            $catalog = $catalog->where('id', $id);
        } else {
            $catalog = $catalog->where('url', $id);
        }
        $doc_list = [];
        $catalog = $catalog->first();
        if(!empty($catalog)){
            $doc_list = DocModel::where('catalog_id', $catalog['id'])->where('enable', '1')->get();
        }
        $view = view('doc.catalog_doc')->with([
            'title' => '商学院-' . $catalog['name'],
            'catalog' => $catalog,
            'doc_list' => $doc_list
        ]);
        return $view;
    }

    /**
     * 帮助页
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $id)
    {
        $doc = HelpCache::get($id);
        if($doc['description']){
            $doc['description'] = DocService::getInstance()->descriptionImage($doc['description']);
        }
        if(!empty($doc)){
            $share_data = [
                'title' => $doc['name'],
                'content' => $doc['name'],
                'url' => \Helper::route('help_view', [$doc['id']]),
            ];
            $view = view('doc.view')->with('doc', $doc)->with('share_data', $share_data);
        }
        else{
            $view = view('errors.404');
        }
        return $view;
    }
}
