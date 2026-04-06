<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkstationPreference extends Model
{
    // Table Name
    protected $table = 'workstation_preferences';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;
    public $incrementing = false;

    
    // Fillable fields to use with ::create function
    protected $fillable = ['name', 'value', 'workstation_id','sync'];

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
    public function workstation(){
        return $this->belongsTo('App\Models\Workstation');
    }

}
