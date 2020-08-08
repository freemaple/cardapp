<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'password_reset';

    protected $fillable = ['user_id'];
}