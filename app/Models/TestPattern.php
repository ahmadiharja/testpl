<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPattern extends Model
{
    // Table Name
    protected $table = 'test_patterns';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;
}
