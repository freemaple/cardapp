<?php

namespace App\Models\Position;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    
    protected $connection = 'address';
    
    protected $table = 'j_position_city';

}