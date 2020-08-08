<?php

namespace App\Models\Equity;

use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;

class EquityRecord extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'equity_record';

}
