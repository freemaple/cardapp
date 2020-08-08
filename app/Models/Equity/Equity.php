<?php

namespace App\Models\Equity;

use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;

class Equity extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'equity';


    //接受用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
