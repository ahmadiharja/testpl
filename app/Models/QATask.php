<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QATask extends Model
{
    use SoftDeletes;

    // Table Name
    protected $table = 'qa_tasks';
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
        'display_id', 'name', 'taskKey', 'freq', 
        'freqCodes', 'lastrundate', 'nextdate', 'nextdateFixed',
         'taskStatus', 'exceptions', 'stepsIds', 'deleted'
    ];

    // Data relationship
    public function display(){
        return $this->belongsTo('App\Models\Display');
    }

    public function getStatusTextAttribute() {
        switch ($this->taskStatus) {
            case 0: return '<span class="text-success">OK</span>';
            case 1: return '<span class="text-danger">Failed</span>';
            case 2: return '<span class="text-danger">Run error</span>';
        }
        return '';
    }

    public function getStatusEventColorAttribute() {
        switch ($this->taskStatus) {
            case 0: return 'event-green';
            case 1: return 'event-red';
            case 2: return 'event-red';
        }
        return '';
    }

    public function getDisabledTextAttribute() {
        return '<span class="text-info">Enabled</span>';
    }

    public function getDuedateAttribute() {
        $timezone = $this->display->workstation->workgroup->facility->timezone;
        return Carbon::createFromTimestamp($this->attributes['nextdate'], $timezone)->format('d/m/Y H:i');
    }

    public function getNextdateAttribute() {
        $timezone = $this->display->workstation->workgroup->facility->timezone;    
        return Carbon::createFromTimeStamp($this->attributes['nextdate'], $timezone)->format('Y-m-d');
    }

    public function getNextdateTimestampAttribute() {
        return $this->attributes['nextdate'];
    }
    // public function setNextdateAttribute($value) {
    //     //$timezone = $this->display->workstation->workgroup->facility->timezone;    
    //     $this->attributes['nextdate'] = Carbon::createFromFormat('Y-m-d', $value)->timestamp;
    // }
    
    // Get due date
    public function getStartdatetimeAttribute() {
        $timezone = $this->display->workstation->workgroup->facility->timezone;

        if (!$this->lastrundate) {
            return 'Never';
        }

        return Carbon::createFromTimestamp($this->lastrundate)->format('d/m/Y H:i');
        // return $this->start_date;
    }

    public function setLastrundateAttribute($value) {
        $this->attributes['lastrundate'] = ($value == 'Never' || $value == '') ? null : $value;
    }
}
