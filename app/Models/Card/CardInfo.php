<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

class CardInfo extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'card_info';

    //发布内容
    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
