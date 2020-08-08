<?php

namespace App\Models\User;

use App\Models\Card\Card;
use App\Models\Post\Post;
use App\Models\Message\Message;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'manager_id', 'director_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //名片
    public function card()
    {
        return $this->hasMany(Card::class);
    }


    //发布内容
    public function post()
    {
        return $this->hasMany(Post::class);
    }

    //消息
    public function message()
    {
        return $this->hasMany(Message::Class, 'receive_user_id');
    }

    //钱包
    public function wallet()
    {
        return $this->hasOne(Wallet::Class, 'user_id');
    }

    //积分
    public function integral()
    {
        return $this->hasOne(Integral::Class, 'user_id');
    }

    //积分记录
    public function integralRecord()
    {
        return $this->hasMany(IntegralRecord::Class, 'user_id');
    }

    //钱包记录
    public function walletRecord()
    {
        return $this->hasMany(WalletRecord::Class, 'user_id');
    }

    //赏金
    public function reward()
    {
        return $this->hasOne(Reward::Class);
    }

    //赏金记录
    public function rewardRecord()
    {
        return $this->hasMany(RewardRecord::Class, 'user_id');
    }

    //地址
    public function address()
    {
        return $this->hasOne(Address::Class);
    }

    //地址
    public function banks()
    {
        return $this->hasMany(Bank::Class);
    }

    //佣金
    public function Commission()
    {
        return $this->hasOne(Commission::Class);
    }

    //数字资产
    public function gold()
    {
        return $this->hasOne(Gold::Class);
    }

}
