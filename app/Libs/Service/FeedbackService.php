<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Repository\Base as BaseRepository;

class FeedbackService
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
     * 保存用户基本信息
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public function add($data = []){
        $result = [];
      	//数据校验
        $validator = Validator::make($data, [
            'fullname' => 'required',
            'email' => 'required',
            'content' => 'required'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $feedback_repository = BaseRepository::model("User\Feedback");
        //插入数据
      	$feedback_repository->insert($data);
      	$result['code'] = "0x0000";
        $result['message'] = '提交成功！';
        return $result;
    }
}