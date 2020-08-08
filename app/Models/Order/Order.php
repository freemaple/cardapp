<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    const ORDER_INIT = 1;

    const PAY_DURATION = 2700; // 默认支付时间倒计时，单位秒

    protected $dates = [
        'payed_at',
        'shipped_at',
        'done_at',
    ]; 

    
    protected $table = 'order';

    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Order\OrderProduct', 'order_id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Order\Reviews', 'order_id');
    }

    public function shipping(){
        return $this->hasOne('App\Models\Order\OrderShipping', 'order_id');
    }

    public function shippings(){
        return $this->hasMany('App\Models\Order\OrderShipping', 'order_id');
    }
    
    public function userinfo()
    {
        return $this->hasOne('App\Models\Order\OrderUserInfo', 'order_id');
    }

    public function refunds()
    {
        return $this->hasMany('App\Models\Order\OrderRefund', 'order_id');
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\Order\OrderPayment', 'paysn', 'paysn');
    }

    public function orderAccountRecord()
    {
        return $this->hasOne('App\Models\Order\OrderAccountRecord', 'order_id');
    }

    /**
     * 是否已取消
     */
    public function isCancel()
    {
        if (in_array($this->order_status, [status_id('CANCEL')])) {
            return true;
        }
        return false;
    }

    /**
     * 订单是否是激活状态
     * @author leesenlen
     * @date   2018-09-28
     * @return boolean    [description]
     */
    public function isActive()
    {
        return in_array($this->order_status, [status_id('SHARING'), status_id('SHIPPED'), status_id('SUCCESS'), status_id('SHIPPING')]);
    }

    /**
     * 可以激活订单
     * @author leesenlen
     * @date   2018-09-10
     * @return boolean    [description]
     */
    public function canActive()
    {
        if (in_array($this->order_status, [status_id('UNPAID')])) {
            return true;
        }
        return false;
    }

    /**
     * 是否可以开始准备发货
     * @author leesenlen
     * @date   2018-09-10
     * @return boolean    [description]
     */
    public function canFufill()
    {
        if ($this->isGroup() && $this->order_status == status_id('SHARING')) {
            return true;
        } 

        if (!$this->isGroup() && $this->order_status == status_id('UNPAID')) {
            return true;
        }

        return false;
    }

    /**
     * 是否已支付
     * @author leesenlen
     * @date   2018-09-10
     * @return boolean    [description]
     */
    public function isPaid()
    {
        if (in_array($this->order_status, static::getUnpaidStatus())) {
            return false;
        }

        return true;
    }

    public function canRefund()
    {
        return $this->isPaid() && $this->order_status!=status_id('SHARING');
    }

    public function canGroupRefund()
    {
        return $this->order_status==status_id('SHARING');
    }

    public function canFulfilled()
    {
       return $this->order_status == status_id('SHIPPING'); 
    }
}