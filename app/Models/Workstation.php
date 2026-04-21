<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Workstation extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    
    protected $table='workstations';
    protected $guarded = [];
    public $timestamps = false;
    public $incrementing = false;

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
    public function workgroup() {
        return $this->belongsTo('App\Models\Workgroup');
    }

    public function displays() {
        return $this->hasMany('App\Models\Display');
    }
    
    public function tasks()
    {
        return $this->hasManyDeep('App\Models\Task', ['App\Models\Display']);
    }

    public function qatasks()
    {
        return $this->hasManyDeep('App\Models\QATask', ['App\Models\Display']);
    }

    public function preferences(){
        return $this->hasMany('App\Models\WorkstationPreference');
    }

    public function settings_names(){
        return $this->hasMany('App\Models\SettingsName');
    }

    public function histories() {
        return $this->hasManyThrough('App\Models\History', 'App\Models\Display');
    }

    public function preference($name) {
        $pref = $this->preferences()->where('name', $name)->first();
        return ($pref != null ? $pref->value : '');
    }

    public function getSleepTimeAttribute() {
        if ($this->preference('PutDisplaysToEnergySaveMode') != 'true') {
            return 'Off';
        }

        return $this->preference('StartEnergySaveMode').' - '.$this->preference('EndEnergySaveMode');
    }
    
    public function getLinkAttribute() {
        return "<a href='".url('/')."/workstations-info/{$this->id}'>{$this->name}</a>";
    }
    public function getLastConnectedAttribute() {
        $timezone = $this->workgroup?->facility?->timezone ?? config('app.timezone', 'UTC');   
        return Carbon::parse($this->attributes['last_connected'])->setTimezone($timezone)->format('Y-m-d H:i:s');
    }

    public function getClientVersionAttribute() {
        $version = $this->preference('appversion');
        return $this->preference('appname') . ($version?"(${version})":"");
    }

    public function setting($name) {
        $setting = $this->settings_names()->where('setting_name', $name)->first();
        return ($setting != null ? $setting->setting_value : '');
    }

    public function getRegulationName($regulationId) {
        $setting = json_decode($this->setting('UsedRegulation'), true);
        return (is_array($setting) && isset($setting[$regulationId]))?$setting[$regulationId]:'';
    }

    public function getClassificationName($regulationId, $classId) {
        $setting = json_decode($this->setting($regulationId), true);
        
        return (is_array($setting) && isset($setting[$classId]))?$setting[$classId]:'';
    }

    // Update workstation preference, 
    // Return false if unchanged
    public function updatePreference($pref_name, $value, $force = false) {
        //print $pref_name.'-val='.$value;
        //if ($value!=null) return false;
        $pref = WorkstationPreference::firstOrNew(['name' => $pref_name, 'workstation_id' => $this->id]);
 //print ($pref->value .' vs '.$value.'*******');
        // If not force then update only when changed
        if (!$force) {
           
            if ($pref->value != $value || !$pref->value) {
                $pref->value = $value;
                $pref->sync = 0;
                $pref->save();
                return true;
            } 
        } else {
            $pref->value = $value;
            $pref->sync = 0;
            $pref->save();
            return true;
        }


        return false;
    }

    public function addToListPreference($pref_name, $value) {
        $setting = WorkstationPreference::firstOrNew(['name' => $pref_name, 'workstation_id' => $this->id]);
        $old = $setting->value;
        $setting_values = explode('|', $setting->value);
        
        if (!in_array($value, $setting_values)) {
            $setting_values[] = $value;
        }

        $setting->value = implode('|', $setting_values);
        if ($setting->value != $old) {
            $setting->sync = 0;
            $setting->save();
            return true;
        }

        return false;
    }
}
