<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_image';
}