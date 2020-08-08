<?php

namespace App\Models\Gift;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Gift extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'gift';

}