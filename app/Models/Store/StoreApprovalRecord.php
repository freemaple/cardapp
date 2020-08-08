<?php

namespace App\Models\Store;

use App\Models\User\User;
use App\Models\Product\Product;

use Illuminate\Database\Eloquent\Model;

class StoreApprovalRecord extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'store_approval_record';

    //用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
