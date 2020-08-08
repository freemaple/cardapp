<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_attribute';

    /**
	 * 获取属性分类
	 * @return permissions model
	 */
	public function option()
	{
	    return $this->belongsTo(Option::class);
	}

	/**
	 * 获取属性分类
	 * @return permissions model
	 */
	public function optionValue()
	{
	    return $this->belongsTo(OptionValue::class);
	}
}