<?php
namespace App\Repository;

use App\Models\Banner\Banner as BanerModel;
use App\Repository\Base as BaseRepository;

class Banner
{

	private static $BannerRepository;

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
        static::$BannerRepository = BaseRepository::model("Banner\Banner");
        return self::$_instance;    
    }

    /**
	 * banner列表
	 * @param  array  $where 查询条件
	 * @param  array  $field 查询列
	 * @return UserSnsLogin model 
	 */
	public function bannerList($enable = '', $pageSize = '0')
	{
		$where = [];
        if($enable !=null){
            $where[] = ['banner.enable', '=', $enable];
        }
        $data = ['where' => $where, 'orderBy' => [["id", 'desc'], ["sort", 'desc']]];
        if($pageSize > 0){
        	$data['pageSize'] = $pageSize;
        }
        $banner = static::$BannerRepository->get($data);
        return $banner;
	}

	/**
	 * 查找数据
	 * @param  array  $where 查询条件
	 * @param  array  $field 查询列
	 * @return UserSnsLogin model 
	 */
	public function find($where = [], $field = [])
	{
		return static::$BannerRepository->findOne($where, $field);
	}

	/**
	 * 插入数据
	 * @param  array $data 插入数据
	 * @return UserSnsLogin model       
	 */
	public function insert($data = null)
	{
		if($data == null){
			return null;
		}
		return static::$BannerRepository->insert($data);
	}

	/**
	 * 更新数据
	 * @param  array $data  更新数据
	 * @param  array $where [['key1', '=', 'value1'], ['key2', '=', 'value2']]
	 * @return boolean
	 */
	public function update($data, $where)
	{
		return static::$BannerRepository->update($data, $where);
	}

	/**
	 * 删除数据
	 * @param  array $data 删除数据条件
	 * @return boolean
	 */
	public function dalete($data)
	{
		return static::$BannerRepository->delete($data);
	}

	/**
	 * 查找数据
	 * @param  array  $where 查询条件
	 * @param  array  $field 查询列
	 * @return UserSnsLogin model 
	 */
	public function findLocation($where = [], $field = [])
	{
		return BaseRepository::model("Banner\BannerLocation")->findOne($where, $field);
	}

	/**
	 * banner位置数据
	 * @return boolean
	 */
	public function bannerLocation()
	{
		return BaseRepository::model("Banner\BannerLocation")->get();
	}

	public function insertBannerLocation($data = null){
		if($data == null){
			return null;
		}
		return BaseRepository::model("Banner\BannerLocation")->insert($data);
	}

	public function updateBannerLocation($data, $where){
		return BaseRepository::model("Banner\BannerLocation")->update($data, $where);
	}

	public function daleteBannerLocation($data){
		return BaseRepository::model("Banner\BannerLocation")->delete($data);
	}
}	
?>