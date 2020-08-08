<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Cache\OrderStatus as StatusCache;

class OrderStatus extends Model
{

    protected $table = 'order_status';

    public static function boot()
    {
        static::saving(function(OrderStatus $item){
            StatusCache::forgetList($item['type']);
        });

        static::saved(function(OrderStatus $item){
            StatusCache::forgetList($item['type']);
        });
    }
}