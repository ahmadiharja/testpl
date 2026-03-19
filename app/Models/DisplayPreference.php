<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\TreeChanged;

class DisplayPreference extends Model
{
    // Table Name
    protected $table = 'display_preferences';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;

    // Fillable fields to use with ::create function
    //protected $fillable = ['name', 'value', 'display_id', 'sync', 'visible'];
    protected $guarded = [];

    // Data relationship
    public function display(){
        return $this->belongsTo('App\Models\Display');
    }
    
    public function setValueAttribute($value) {
        if ($this->name == 'InstalationDate') {
            $value = str_replace('-', '.', $value);
        }

        if ($this->name == 'SerialNumber' && $this->display->serial != $value) {
            $this->display->serial = $value;
            $this->display->save();
            event(new TreeChanged($this->display->workstation->workgroup->facility_id));
        }

        if ($this->name == 'Model' && $this->display->model != $value) {
            $this->display->model = $value;
            $this->display->save();
            event(new TreeChanged($this->display->workstation->workgroup->facility_id));
        }

        if ($this->name == 'Manufacturer' && $this->display->manufacturer != $value) {
            $this->display->manufacturer = $value;
            $this->display->save();
            event(new TreeChanged($this->display->workstation->workgroup->facility_id));
        }

        if ($this->name == 'ResolutionHorizontal' && $this->display->width != $value) {
            $this->display->width = $value;
            $this->display->save();
        }

        if ($this->name == 'ResolutionVertical' && $this->display->height != $value) {
            $this->display->height = $value;
            $this->display->save();
        }

        $this->attributes['value'] = $value;
    }

    public function getValueAttribute() {
        if (in_array($this->name, array('temperature', 'temperature_x', 'temperature_y')) ) {
            return round($this->attributes['value'], 2);
        }
        return $this->attributes['value'];
    }
}
