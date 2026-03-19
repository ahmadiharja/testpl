<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Display_preference extends Model
{
    use HasFactory;
    
    protected $table='display_preferences';
    protected $guarded = [];
    public $timestamps = false;
    
    
}
