<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

use App\Models\Theme\Background;
use App\Models\Microlink\Microlink;
use App\Models\Post\Post;

class CardAlbum extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'card_album';
}
