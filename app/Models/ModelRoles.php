<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelRoles extends Model
{
    use HasFactory;

    protected $table='model_has_roles';
    protected $guarded = [];
    public $timestamps = false;
}
