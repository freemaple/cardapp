<?php

namespace App\Models\Post;

use App\Models\User\User;
use App\Models\Card\CardPost;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'post';

    //用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //分类
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //图片
    public function images()
    {
        return $this->hasMany(PostImage::class);
    }

    //视频
    public function videos()
    {
        return $this->hasMany(PostVideo::class);
    }

     //分类
    public function cardPost()
    {
        return $this->hasMany(CardPost::class);
    }
}
