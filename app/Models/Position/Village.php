<?php

namespace App\Models\Position;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    
    protected $connection = 'address';
    
    protected $table = 'j_position_village';

}