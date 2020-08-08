<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{

    const UNPAID = 0; //未支付
    const PAYING = 1; // 支付中
    const PAID = 2; // 已支付
    const REFUND = 3; // 退款
    
    protected $table = 'order_payment';

    public function canPay()
    {
        return $this->status == static::UNPAID;
    }

    public function isActive()
    {
        return $this->status == OrderPayment::PAID || $this->status == OrderPayment::REFUND;
    }
}