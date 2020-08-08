<?php

namespace App\Models\Wx;

use App\Models\Order\PaymentMethodCountryRestriction;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'wx_user';
}
