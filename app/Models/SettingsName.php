<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingsName extends Model
{
    // Table Name
    protected $table = 'settings_names';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_name', 'setting_value', 'workstation_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->id = ((int) static::max('id')) + 1;
            }
        });
    }
    
    public function workstation(){
        return $this->belongsTo('App\Models\Workstation');
    }
}
