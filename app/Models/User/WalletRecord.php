<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class WalletRecord extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_wallet_record';
}