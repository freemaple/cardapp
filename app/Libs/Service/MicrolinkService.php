<?php
namespace App\Libs\Service;

use App\Models\User\User as UserModel;
use App\Models\Microlink\Microlink;

class MicrolinkService
{
    /**
     * 列表
     * @param  Request $request 
     * @return string           
     */
    public static function getList($user_id){
        $microlinks = Microlink::select('microlink.id', 'icon.svg', 'microlink.name', 'microlink.icon_id')
        ->leftjoin('icon', 'icon.id', '=', 'microlink.icon_id')
        ->where('user_id', '=', $user_id)
        ->get();
        return $microlinks;
    }

    public static function find($id){
        $microlink = Microlink::select('id', 'name', 'icon_id', 'link', 'user_id')
        ->where('id', '=', $id)
        ->first();
        return $microlink;
    }
}