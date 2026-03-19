<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingSMTP extends Model
{
    use HasFactory;

    protected $table='smtp_settings';
    protected $guarded = [];
    public $timestamps = false;
}
