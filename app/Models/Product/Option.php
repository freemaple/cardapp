<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    /**
     * 关联到属性模型的数据表
     *
     * @var string
     */
    protected $table = 'option';
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['name', 'admin_id'];

    /**
     * 获取产品子料号
     */
    public function optionValue()
    {
        return $this->hasMany(OptionValue::Class);
    }
    
}
