<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderRechargeRefund extends Model
{

    const PENDING = 0; // 待退款
    const REFUNDED = 1; // 已退款
    const REJECTED = 2; // 被拒绝
    const FAILED = 3; // 退款失败
    
    protected $table = 'order_recharge_refund';

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\UserAuth', 'user_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order\OrderRecharge', 'order_id');
    }
}