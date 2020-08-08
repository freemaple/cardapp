<?php

namespace App\Models\Reviews;

use Illuminate\Database\Eloquent\Model;

class ReviewsImage extends Model
{
    protected $table = 'reviews_image';

    protected $fillable = ['reviews_id', 'order_product_id', 'path'];
}