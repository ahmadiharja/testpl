<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingName extends Model
{
    use HasFactory;
    
    protected $table='settings_names';
    protected $guarded = [];
    public $timestamps = false;
}
