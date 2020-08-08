<?php
namespace App\Libs\Service;

use App\Models\Message\Message as MessageModel;
use Validator;

class MessageService
{

    /**
     * 加载消息
     */
    public static function loadMessage($where)
    {
        $message = MessageModel::where($where)->first();
        return $message;
    }

    /**
     * 加载消息
     */
    public static function getUserMessage($user_id, $pagesize)
    {
        $messages = MessageModel::where('user_id', '=', $user_id)
        ->orderBy('id','desc')
        ->paginate($pagesize);
        return $messages;
    }

    /**
     * 添加
     * @param  array $data 
     * @return array
     */
    public static function insert($insert_data)
    {
        $message = new MessageModel();
        foreach ($insert_data as $key => $value) {
            $message->$key = $value;
        }
        $result = $message->save();
        return $result;
    }
}