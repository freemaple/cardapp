<?php
namespace App\Libs\Service;

use App\Repository\Base as BaseRepository;
use App\Models\Doc\DocCatalog;
use Validator;
use Storage;
use App\Libs\Service\CacheService;
use  App\Libraries\Storage\Common as CommonStorage;
use App\Cache\Help as HelpCache;

class DocService
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


    public static function getDocCatalog(){
        $DocCatalog = DocCatalog::where('enable', '1')->get();
        if($DocCatalog != null){
            $DocCatalog = $DocCatalog->toArray();
        }
        return $DocCatalog;
    }  
    
    /**
     *列表
     *
     * @return array()
    */
    public function docList($fullname = '' , $pageSize = '')
    {
        $where = [];
        if($fullname != ""){
            $where[] = ['fullname', 'like', '%'. sprintf("%s", $fullname). '%'];
        }
        $doc_repository = BaseRepository::model("Doc\Doc");
        $data = [
            'where' => $where,
            'pageSize' => $pageSize
        ];
        $doc_list = $doc_repository->get($data);
        return $doc_list;
    }

    /**
     * 根据ID查询
     * @param  int $id 
     * @return array
     */
    public function find($where){
        if(empty($where)){
            return null;
        }
        $doc_repository = BaseRepository::model("Doc\Doc");
        $doc = $doc_repository->findOne($where);
        if($doc != null){
            $doc = $doc->toArray();
        }
        $description = $doc['description'];
        if(strpos($description, '<img') !== false){
            $doc['description'] = $this->descriptionImage($description);
        }
        return $doc;
    }

    /**
     * 添加
     * @param  array $data 
     * @return array
     */
    public function create($data, $request)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required'
        ]);
        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        if($data['url'] != null){
            $where = [['url', '=', $data['url']]];
            $u = BaseRepository::model('Doc\Doc')->findOne($where);
            if(!empty($u)){
                $result['message'] = "对不起,地址已经存在";
                return $result;
            }
        }

        $description = $this->descriptionHTML($data['description']);
        
        $insert_data = [
            'name' => $data['name'],
            'description' => $description,
            'url' => $data['url'],
            'enable' => (isset($data['enable']) && $data['enable'] == '1') ? '1' :'0'
        ];
        if(isset($data['catalog_id'])){
            $insert_data['catalog_id'] = $data['catalog_id'] ? $data['catalog_id'] : 0;
        }
        if(isset($data['meta_title'])){
            $insert_data['meta_title'] = $data['meta_title'];
        }
        if(isset($data['meta_description'])){
            $insert_data['meta_description'] = $data['meta_description'];
        }
        if(isset($data['meta_keyword'])){
            $insert_data['meta_keyword'] = $data['meta_keyword'];
        }
        $doc = BaseRepository::model('Doc\Doc')->insert($insert_data);
        $video = $request->file('video');
        if($video && $video->isValid()){
            //获取上传文件的大小
            $size = $video->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 50*1024*1024){
                $result['code'] = '2x1';
                $result['message'] = '上传文件不能超过50M';
                return $result;
            }
            $CommonStorage = new CommonStorage('doc_video');
            $filepath = $CommonStorage->saveUpload($video);
            $doc->video = $filepath;
            $doc->save();
        }
        HelpCache::clearHelpCache($doc['id']);
        HelpCache::clearHelpCache($doc['url']);
        $result['status'] = true;
        return $result;
    }

    public function descriptionHTML($description){
        if($description){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc ->loadHTML('<?xml encoding="UTF-8">' . $description);//$str为一段HTML代码
            libxml_clear_errors();
            libxml_use_internal_errors($libxml_previous_state);
            $image = $doc->getElementsByTagName('img');
            if(count($image)){
                foreach ($image as $key => $i) {
                    $src = $i->getAttribute('src');
                    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $src, $matches)){
                        $res = static::base64upload($src);
                        $image = $res['filepath'];
                        $i->setAttribute('src', $image);
                    }
                }
                $doc->encoding = 'UTF-8';
                $body = $doc->getElementsByTagName('body')->item(0);
                $description = $doc->saveHTML($body);
                $description = str_replace('<body>', '',$description);
                $description = str_replace('</body>', '',$description);
            }
        }
        return $description;
    }

    /**
     * 上传产品图片
     */
    public static function base64upload($base64_image_content, $directory = 'post') {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $matches)){
            //图片路径地址    
            $fullpath = 'storage/' . $directory;
            if(!is_dir($fullpath)){
                mkdir($fullpath, 0777, true);
            }
            $image_file = \Image::make($base64_image_content);
            $width = $image_file->width();
            $height = $image_file->height();
            if($width > 1024){
                $h = 1024 / $width * $height;
                $image_file = $image_file->resize(1024, $h);
                $base64_image_content = $image_file->encode('data-url');
            }
            $type = $matches[2];
            $content_arr = explode($matches[0], $base64_image_content);
            $img = base64_decode($content_arr[1]);
            $filename = md5(date('YmdHis').rand(1000, 999999)). '.jpg';
            $filepath = 'doc/' . $filename;
            $savepath = storage_path() . '/app/static/' . $filepath;
        
            //服务器文件存储路径
            if (file_put_contents($savepath, $img)){
                $result['status'] = 1;
                $result['filename'] = $filename;
                $result['filepath'] = $filepath;
                $result['filelink'] = \HelperImage::storagePath($filepath);
                return $result;
            }else{
                $result['status'] = 0;
                $result['message'] = '保存失败';
                return $result;
            }
        } else {
            $result['status'] = 0;
            $result['message'] = '不是有效的图片';
            return $result;
        }
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
                $body = $doc->getElementsByTagName('body')->item(0);
                $description = $doc->saveHTML($body);
                $description = str_replace('<body>', '',$description);
                $description = str_replace('</body>', '',$description);
            }
        }
        return $description;
    }

    /**
     * 更新
     * @param  array $data 
     * @return array
     */
    public function update($data, $request)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required',
            'description' => 'required'
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }
        $doc = BaseRepository::model('Doc\Doc')->find($data['id']);
        if(empty($doc)){
            $result['message'] = "对不起,文档不存在";
            return $result;
        }
        $url = $doc['url'];
        if(isset($data['url'])){
            if(!empty($data['url'])){
                $where = [['url', '=', $data['url']], ['id', '!=', $doc['id']]];
                $u = BaseRepository::model('Doc\Doc')->findOne($where);
                if(!empty($u)){
                    $result['message'] = "对不起,地址已经存在";
                    return $result;
                }
            }
            $url = $data['url'];
        }
        $description = $this->descriptionHTML($data['description']);
        $update_data = [
            'name' => $data['name'],
            'description' => $description
        ];
        if(isset($data['url'])){
            $update_data['url'] = $data['url'];
        }
        if(isset($data['catalog_id'])){
            $update_data['catalog_id'] = $data['catalog_id'] ? $data['catalog_id'] : 0;
        }
        if(isset($data['meta_title'])){
            $update_data['meta_title'] = $data['meta_title'];
        }
        if(isset($data['meta_description'])){
            $update_data['meta_description'] = $data['meta_description'];
        }
        if(isset($data['meta_keyword'])){
            $update_data['meta_keyword'] = $data['meta_keyword'];
        }
        if(isset($data['enable'])){
            $update_data['enable'] = (isset($data['enable']) && $data['enable'] == '1') ? '1' :'0';
        }
        $old_video = $doc['video'];
        BaseRepository::model('Doc\Doc')->update($update_data, [['id', '=', $doc['id']]]);
        $video = $request->file('video');
        if($video && $video->isValid()){
            //获取上传文件的大小
            $size = $video->getSize();
            //这里可根据配置文件的设置，做得更灵活一点
            if($size > 50*1024*1024){
                $result['code'] = '2x1';
                $result['message'] = '上传文件不能超过50M';
                return $result;
            }
            $CommonStorage = new CommonStorage('doc_video');
            $filepath = $CommonStorage->saveUpload($video);
            $doc->video = $filepath;
            $doc->save();
            if($filepath && !empty($old_video)){
                if($old_video != $filepath){
                    $CommonStorage->deleteFile($old_video);
                }
            }
        }
        $result['status'] = true;
        $result['message'] = '保存成功';
        HelpCache::clearHelpCache($doc['id']);
        HelpCache::clearHelpCache($url);
        return $result;
    }

    /**
     * 删除
     * @param  int $id 
     * @return boolean
     */
    public function removedoc($id = null)
    {
        if($id == null){
            return false;
        }
        $doc = BaseRepository::model('Doc\Doc')->find($id);
        if(empty($doc)){
            $result['message'] = "对不起,文档不存在";
            return $result;
        }
        $url = $doc['url'];
        $doc_repository = BaseRepository::model("Doc\Doc");
        $doc_repository->delete([['id', '=', $id]]);
        HelpCache::clearHelpCache($id);
        HelpCache::clearHelpCache($url);
        return true;
    }
}