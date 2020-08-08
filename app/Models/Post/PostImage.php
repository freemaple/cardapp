<?php

namespace App\Models\Post;

use App\Models\User\User;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'post_image';
}
