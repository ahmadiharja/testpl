<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPatternImage extends Model
{
    protected $table = 'test_pattern_images';
    public $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['name', 'url'];
}
