<?php

namespace App\Models\Store;

use App\Models\User\User;
use App\Models\Product\Product;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'store_account';

    //用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //产品
    public function products()
    {
        return $this->belongsToMany(Product::Class, 'store_product', 'store_id', 'product_id')->withTimestamps();
    }

    //产品
    public function certificateImage()
    {
        return $this->hasMany(StoreCertificateImage::Class);
    }
}
