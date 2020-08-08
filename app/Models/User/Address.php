<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_address';
}