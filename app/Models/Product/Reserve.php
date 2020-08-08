<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_reserve';
}