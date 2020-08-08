<?php

namespace App\Models\Position;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    
    protected $connection = 'address';
    
    protected $table = 'j_position';

}