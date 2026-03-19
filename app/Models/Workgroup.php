<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workgroup extends Model
{
    use HasFactory;
    
    protected $table='workgroups';
    protected $guarded = [];
    public $timestamps = false;

    // Data relationship
    public function facility(){
        return $this->belongsTo('App\Models\Facility');
    }

    public function workstations() {
        return $this->hasMany('App\Models\Workstation');
    }

    public function displays() {
        return $this->hasManyThrough('App\Models\Display', 'App\Models\Workstation');
    }

    public function preference(){
        return $this->hasManyThrough('App\Models\WorkstationPreference','App\Models\Workstation');
    }

    public function settingname()
    {
        return $this->hasManyThrough('App\Models\SettingsName',  'App\Models\Workstation');
    }

    public function getLinkAttribute() {
        return "<a href='".url('/')."/workgroups-info/{$this->id}'>{$this->name}</a>";
    }
}
