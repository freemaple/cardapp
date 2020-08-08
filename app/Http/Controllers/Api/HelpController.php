<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Libs\Service\DocService;
use App\Models\Doc\Doc as DocModel;
use App\Models\Doc\DocCatalog;
use App\Cache\Help as HelpCache;
use Illuminate\Http\Request;

class HelpController extends BaseController
{

     /**
     * 帮助页
     *
     * @return \Illuminate\Http\Response
     */
    public function school()
    {
        $doc_catalog = HelpCache::getDocCatalog();
        $result = ['code' => 'Success'];
        $result['data'] = [
            'title' => '商学院',
            'doc_catalog' => $doc_catalog
        ];
        return response()->json($result);
    }

     /**
     * 帮助页
     *
     * @return \Illuminate\Http\Response
     */
    public function catalogDoc(Request $request)
    {
        $id = $request->id;
        $catalog = DocCatalog::where('enable', '1');
        if(is_numeric($id)){
            $catalog = $catalog->where('id', $id);
        } else {
            $catalog = $catalog->where('url', $id);
        }
        $catalog = $catalog->first();
        if(empty($catalog)){
            
        }
        $doc_list = DocModel::where('catalog_id', $catalog['id'])->where('enable', '1')->get();
        $result = ['code' => 'Success'];
        $result['data'] = [
            'title' => '商学院-' . $catalog['name'],
            'catalog' => $catalog,
            'doc_list' => $doc_list
        ];
        return response()->json($result);
    }

    /**
     * 帮助页
     *
     * @return \Illuminate\Http\Response
     */
    public function help($id)
    {
        $doc = HelpCache::get($id);
        if($doc['description']){
            $doc['description'] = DocService::getInstance()->descriptionImage($doc['description']);
        }
        $result = ['code' => 'Success'];
        $result['data'] = [
            'title' => '商学院-' . $doc['name'],
            'doc' => $doc,
            'share_data' => [
                'title' => $doc['name'],
                'content' => $doc['name'],
                'url' => \Helper::route('help_view', [$doc['id']])
            ]
        ];
        return response()->json($result);
    }
}
