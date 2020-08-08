<?php

namespace App\Models\Queue;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'failed_jobs';
}
