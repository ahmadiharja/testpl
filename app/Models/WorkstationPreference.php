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

    
    // Fillable fields to use with ::create function
    protected $fillable = ['name', 'value', 'workstation_id','sync'];

    // Data relationship
    public function workstation(){
        return $this->belongsTo('App\Models\Workstation');
    }

}
