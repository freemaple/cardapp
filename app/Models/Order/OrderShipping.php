<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{

    protected $table = 'order_shipping';

    protected $fillable = ['order_id', 'shipping_method', 'tracknumber', 'upload_time'];
}