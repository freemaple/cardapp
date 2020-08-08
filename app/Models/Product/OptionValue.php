<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'option_value';

    /**
     * 获取属性类型
     */
    public function option()
    {
    	return $this->hasOne(Option::Class);
    }
}