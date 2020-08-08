<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Product extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product';

    protected $fillable = ['spu', 'name', 'description', 'cn_name', 'is_sale', 'admin_id'];

    /**
     * 获取产品子料号
     */
    public function skus()
    {
    	return $this->hasMany(Sku::Class);
    }

     /**
     * 获取属性分类
     * @return permissions model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取产品子料号
     */
    public function images()
    {
        return $this->hasMany(Image::Class);
    }

    /**
     * 获取产品子料号
     */
    public function attribute()
    {
        return $this->hasMany(Attribute::Class);
    }
}