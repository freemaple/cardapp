<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Recharge extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_recharge';

    //用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
