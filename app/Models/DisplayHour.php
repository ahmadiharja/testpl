<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisplayHour extends Model
{
    // Table Name
    protected $table = 'display_hours';
    // Primary Key
    public $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    // Timestamps
    public $timestamps = true;

    // Fillable fields to use with ::create function
    protected $fillable = ['start', 'end', 'duration', 'display_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->id = ((int) static::max('id')) + 1;
            }
        });
    }

    // Data relationship
    public function display(){
        return $this->belongsTo('App\Models\Display');
    }
}
