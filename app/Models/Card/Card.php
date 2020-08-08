<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

use App\Models\Theme\Background;
use App\Models\Microlink\Microlink;
use App\Models\Post\Post;

class Card extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'card';

    //信息
    public function info()
    {
        return $this->hasMany(CardInfo::class);
    }

    //背景
    public function background()
    {
        return $this->belongsTo(Background::class);
    }

     //背景
    public function microlinks()
    {
        return $this->belongsToMany(Microlink::Class, 'card_microlink', 'card_id', 'microlink_id')->withTimestamps();
    }

     //背景
    public function post()
    {
        return $this->belongsToMany(Post::Class, 'card_post', 'card_id', 'post_id')->withTimestamps();
    }

     //背景
    public function albums()
    {
        return $this->hasMany(CardAlbum::class);
    }
}
