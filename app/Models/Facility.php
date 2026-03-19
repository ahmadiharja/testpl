<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Camroncade\Timezone\Timezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    
    protected $table='facilities';
    protected $guarded = [];
    public $timestamps = false;

    public function workgroups(){
        return $this->hasMany('App\Models\Workgroup');
    }
    public function users(){
        return $this->hasMany('App\Models\User');
    }
    public function workstations()
    {
        return $this->hasManyThrough('App\Models\Workstation','App\Models\Workgroup');
    }

    public function displays()
    {
        return $this->hasManyDeep('App\Models\Display', ['App\Models\Workgroup', 'App\Models\Workstation']);
    }
    public function preference()
    {
        return $this->hasManyDeep('App\Models\WorkstationPreference', ['App\Models\Workgroup', 'App\Models\Workstation']);
    }
    public function settingname()
    {
        return $this->hasManyDeep('App\Models\SettingsName', ['App\Models\Workgroup', 'App\Models\Workstation']);
    }
    public function tasks()
    {
        return $this->hasManyDeep('App\Models\Task', ['App\Models\Workgroup', 'App\Models\Workstation', 'App\Models\Display']);
    }

    public function qatasks()
    {
        return $this->hasManyDeep('App\Models\QATask', ['App\Models\Workgroup', 'App\Models\Workstation', 'App\Models\Display']);
    }

    public function histories()
    {
        return $this->hasManyDeep('App\Models\History', ['App\Models\Workgroup', 'App\Models\Workstation', 'App\Models\Display']);
    }

    public function getCurrentTimeAttribute() {
        return Carbon::now($this->timezone);
    }

    public function getTimezoneDisplayAttribute() {
        return array_search($this->timezone, (new Timezone)->timezoneList);
    }

    public function getLinkAttribute() {
        return "<a href='".url('/')."/facility-info/{$this->id}'>{$this->name}</a>";
    }
}
