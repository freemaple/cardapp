<?php

namespace App\Models\Post;

use App\Models\User\User;

use Illuminate\Database\Eloquent\Model;

class Reprint extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'post_reprint';

}
