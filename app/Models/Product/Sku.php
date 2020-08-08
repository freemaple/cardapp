<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_sku';

    /**
     * 获取属性类型
     */
    public function attribute()
    {
        return $this->hasMany(Attribute::Class, 'product_sku_id');
    }
}