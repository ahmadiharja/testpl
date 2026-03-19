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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_name', 'setting_value', 'workstation_id'
    ];
    
    public function workstation(){
        return $this->belongsTo('App\Models\Workstation');
    }
}
