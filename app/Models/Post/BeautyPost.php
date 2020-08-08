<?php

namespace App\Models\Post;

use App\Models\User\User;

use Illuminate\Database\Eloquent\Model;

class BeautyPost extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'beauty_post';

    //用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //文章
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
