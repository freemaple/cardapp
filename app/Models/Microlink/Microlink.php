<?php

namespace App\Models\Microlink;

use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;

use App\Models\Icon\Icon;

class Microlink extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'microlink';

    //user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //user
    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
