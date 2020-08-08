<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;

class Message extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'message';


    //接受用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
