<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryRelation extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_category_relation';
}