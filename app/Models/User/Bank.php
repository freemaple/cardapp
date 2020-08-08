<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_bank';
}