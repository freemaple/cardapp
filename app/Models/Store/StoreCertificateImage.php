<?php

namespace App\Models\Store;

use App\Models\User\User;

use Illuminate\Database\Eloquent\Model;

class StoreCertificateImage extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'store_certificate_image';
}