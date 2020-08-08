<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderShippingPackage extends Model
{
    protected $connection = 'order';

    protected $table = 'order_shipping_package';

    protected $fillable = ['order_shipping_id', 'order_id', 'order_product_id', 'goods_id', 'goods_sku', 'quantity'];
}