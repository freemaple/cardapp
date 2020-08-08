<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Wish extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_wish';

    /**
	 * 获取产品
	 * @return permissions model
	 */
	public function product()
	{
	    return $this->belongsTo(Product::class);
	}

	/**
	 * 获取用户
	 * @return permissions model
	 */
	public function user()
	{
	    return $this->belongsTo(User::class);
	}
}