<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{

	protected $table = 'order_product';

    public function order()
    {
        return $this->belongsTo('App\Models\Order\Order', 'order_id');
    }

    public function sku()
    {
        return $this->belongsTo('App\Models\Product\Sku', 'sku_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product', 'product_id');
    }
}