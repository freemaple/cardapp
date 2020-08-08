<?php
namespace App\Libs\Service;

use App\Repository\Banner as RepositoryBanner;
use App\Repository\Base as RepositoryBase;
use Validator;
use Storage;
use App\Libs\Service\CacheService;
use App\Cache\Banner as BannerCache;
use App\Libraries\Storage\Banner as BannerStorage;

class BannerService
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
    
    /**
     *banner列表
     *
     * @return array()
    */
    public function bannerList($enable = ''){
        $banner_list = RepositoryBanner::getInstance()->bannerList($enable);
        if($banner_list != null){
            $banner_list = $banner_list->toArray();
        }
        return $banner_list;
    }

    /**
     * 根据ID查询banner
     * @param  int $id 
     * @return array
     */
    public function findBanner($id){
        if(empty($id)){
            return null;
        }
        $banner = RepositoryBanner::getInstance()->find([['id', '=', $id]]);
        if($banner != null){
            $banner = $banner->toArray();
        }
        return $banner;
    }

    /**
     * 获取banner位置数据
     * @return array
     */
    public function bannerLocation(){
        $banner_location = RepositoryBanner::getInstance()->bannerLocation();
        if($banner_location != null){
            $banner_location = $banner_location->toArray();
        }
        return $banner_location;
    }

    /**
     * 根据id查询banner位置数据
     * @param  int $id
     * @return array
     */
    public function findBannerLocation($id){
        if(empty($id)){
            return null;
        }
        $banner = RepositoryBanner::getInstance()->findLocation([['id', '=', $id]]);
        if($banner != null){
            $banner = $banner->toArray();
        }
        return $banner;
    }

    /**
     * 添加banner
     * @param  array $data banner数据
     * @param  object $file 表单文件上传数据
     * @return array
     */
    public function createBanner($data, $file = null)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        if(stripos($data['url'], 'http://') === false && stripos($data['url'], 'https://') === false){
            $result['message'] = '请输入完整的链接地址';
            return $result;
        }

        if($file != null){
            //文件上传
            $upload_file_path = static::uploadBannerImage($file);
            if($upload_file_path == null){
                $result['message'] = '文件上传失败';
                return $result;
            }
        } else{
            $upload_file_path = isset($data['image'])? $data['image'] : '';
        }

        if($upload_file_path == null){
            $result['message'] = '图片地址不能为空';
            return $result;
        }

        //上传成功,插入数据
        $insert_data = [
             'location' => $data['location'],
            'image' =>  $upload_file_path,
            'url' => $data['url'],
            'alt' => $data['alt'],
            'sort' => intval($data['sort']) ? $data['sort'] : '1',
            'enable' => (isset($data['enable']) && $data['enable'] == '1') ? '1' :'0'
        ]; 
        RepositoryBanner::getInstance()->insert($insert_data);
        BannerCache::clearHome();
        $result['status'] = true;
        return $result;
    }

    /**
     * 更新banner
     * @param  array $data banner数据
     * @param  object $file 表单文件上传数据
     * @return array
     */
    public function updateBanner($data, $file)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        if(stripos($data['url'], 'http://') === false && stripos($data['url'], 'https://') === false){
            $result['message'] = '请输入完整的链接地址';
            return $result;
        }

        if(!empty($file)){
            $upload_file_path = $this->uploadBannerImage($file);
        } else {
            $upload_file_path = isset($data['image'])? $data['image'] : '';
        }

        //更新数据
        $update_data = [
            'location' => $data['location'],
            'url' => $data['url'],
            'alt' => $data['alt'],
            'sort' => intval($data['sort']) ? $data['sort'] : '1',
            'enable' => (isset($data['enable']) && $data['enable'] == '1') ? '1' :'0',
        ]; 

        if($upload_file_path != null){
            $update_data['image'] = $upload_file_path;
        }
        RepositoryBanner::getInstance()->update($update_data, [['id', '=', $data['id']]]);
        BannerCache::clearHome();
        $result['status'] = true;
        return $result;
    }
    /**
     * banner文件上传
     * @param  object $file 表单文件上传数据
     * @return string
     */
    private function uploadBannerImage($file){
        if ($file->isValid()){
            //文件原名
            $originalName = $file->getClientOriginalName();
            //临时文件的绝对路径
            $realPath = $file->getRealPath();
            $type = $file->getClientMimeType();
            if(!in_array(strtolower($type),array('image/jpg','image/gif','image/jpeg','image/png','image/bmp'))){
                $result['message'] = '文件格式只能为jpg/gif/jpeg/png/bmp';
            }
            $BannerStorage = new BannerStorage('banner');
            $filepath = $BannerStorage->saveUpload($file);
            return $filepath;
        }
        return null;
    }

    /**
     * 删除banner
     * @param  int $id 
     * @return boolean
     */
    public function removeBanner($id = null)
    {
        if($id == null){
            return false;
        }
        return RepositoryBanner::getInstance()->dalete([['id', '=', $id]]);
        BannerCache::clearHome();
    }

    /**
     * 创建banner位置数据
     * @param  array $data banner位置数据
     * @return array
     */
    public function createBannerLocation($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'location' => 'required',
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }
        //插入数据
        $insert_data = [
            'location' => $data['location'],
            'description' => isset($data['description']) ? $data['description'] : ''
        ]; 
        RepositoryBanner::getInstance()->insertBannerLocation($insert_data);
        $result['status'] = true;
        return $result;
    }

    /**
     * 更新banner位置数据
     * @param  $data banner位置数据
     * @return array
     */
    public function updateBannerLocation($data)
    {
        //返回信息
        $result = ['status' => false, 'message' => ''];

        //数据校验
        $validator = Validator::make($data, [
            'location' => 'required',
        ]);

        //校验失败
        if($validator->fails()){
            $errors = $validator->errors()->all();
            $result['message'] = implode(' ', $errors);
            return $result;
        }

        $update_data = [
            'location' => $data['location'],
            'description' => isset($data['description']) ? $data['description'] : ''
        ]; 
        RepositoryBanner::getInstance()->updateBannerLocation($update_data, [['id', '=', $data['id']]]);
        $result['status'] = true;
        return $result;
    }
}