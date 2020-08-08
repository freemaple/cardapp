<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $table = 'order_reviews';

    /**
     * 所属订单
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order\Order','order_id');
    }

    /**
     * 用户
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**
     * 所属订单
     */
    public function orderProduct()
    {
        return $this->belongsTo('App\Models\Order\OrderProduct','order_product_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product', 'product_id');
    }
}