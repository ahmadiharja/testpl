<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use HasFactory;
    
    protected $table='tasks';
    protected $guarded = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    public static function boot()
    {
        parent::boot();
        Task::observe(new \App\Observers\TaskObserver);

        static::creating(function (Task $task) {
            if (!empty($task->id)) {
                return;
            }

            $nextId = (int) DB::table($task->getTable())->max('id') + 1;
            $task->id = max(1, $nextId);
        });
    }

    // Data relationship
    public function display(){
        return $this->belongsTo('App\Models\Display');
    }


    public function taskType() {
        return $this->belongsTo('App\Models\TaskType', 'type', 'key');
    }

    public function testPattern() {
        return $this->belongsTo('App\Models\TestPattern', 'testpattern', 'value');
    }

    public function ScheduleType() {
        return $this->belongsTo('App\Models\ScheduleType', 'schtype', 'client_id');
    }

    public function setNthflagAttribute($value) {
        $this->attributes['nthflag'] = (''.$value == 'true' || ''.$value=='1') ? 1 : 0;
    }


    public function getStatusTextAttribute() {
        switch ($this->status) {
            case 0: return '<span class="text-success">OK</span>';
            case 1: return '<span class="text-danger">Failed</span>';
            case 2: return '<span class="text-danger">Run error</span>';
        }
        return '';
    }

    public function getStatusEventColorAttribute() {
        switch ($this->status) {
            case 0: return 'event-green';
            case 1: return 'event-red';
            case 2: return 'event-red';
        }
        return '';
    }

    public function getDisabledTextAttribute() {
        return $this->disabled?'<span class="text-warning">Disabled</span>':'<span class="text-info">Enabled</span>';
    }

    public function getNextrunDisplayAttribute() {
        return $this->nextrun;
    }
    public function getNextrunString() {
        $faketask = task::find($this->id);
        $timezone = $faketask->display->workstation->workgroup->facility->timezone;    
        return Carbon::createFromTimeStamp($this->nextrun)->format('Y-m-d\TH:i:s');
    }
    public function getDaysofweekdisplayAttribute(){
        return explode(";",$this->daysofweek);
    }

    public function getDayofmonthdisplayAttribute(){
        return explode(";",$this->dayofmonth);
    }
 
    public function getMonthesdisplayAttribute(){
        return explode(";",$this->monthes);
    }
    
   /* public function getStartDateAttribute(){
        $a = explode('.', $this->attributes['startdate']);
        if (count($a) > 2) {
            return Carbon::createFromDate($a[0], $a[1], $a[2]);
        }
        return Carbon::createFromTimeStamp(0);
    }*/
    public function getStartdateAttribute(){
        if(!isset($this->attributes['startdate'])) return '';
        return str_replace(".","-",$this->attributes['startdate']);
    }

    public function getStartdatedisplayAttribute(){
        if(!isset($this->attributes['startdate'])) return '';
        return str_replace(".","-",$this->attributes['startdate']);
    }

    public function getStartdatetimeAttribute() {
        $faketask = task::find($this->id);
        $timezone = $faketask->display->workstation->workgroup->facility->timezone;    
        
        if($this->startdate == null)
            return Carbon::createFromTimestamp(0, $timezone);

        return Carbon::createFromFormat('Y-m-d H:i', $this->startdate.' '.$this->starttime, $timezone);
    }
    // Get due date
    public function getStartdatetimedisplayAttribute() {
        $faketask = task::find($this->id);
        $timezone = $faketask->display->workstation->workgroup->facility->timezone;

        if ($this->disabled) {
            return 'Disabled';
        }

        if ($this->schtype == ScheduleType::STARTUP) {
            return 'Start-up';
        }

        if (!$this->startdate) {
            return 'Never';
        }

        return Carbon::createFromFormat('Y.m.d H:i', str_replace('-', '.', $this->startdate).' '.$this->starttime)->format('d/m/Y H:i');
        // return $this->start_date;
    }
    public function getTimezoneAttribute() {
        if ($this->display && $this->display->workstation && $this->display->workstation->workgroup && $this->display->workstation->workgroup->facility) {
            return $this->display->workstation->workgroup->facility->timezone;
        } 
        return '';
    }

    public function getDuedateAttribute() {
        $faketask = task::find($this->id);
        //$timezone = $faketask->display->workstation->workgroup->facility->timezone;

        if ($this->disabled) {
            return 'Disabled';
        }

        if ($this->schtype == 'Startup') {
            return 'Start-up';
        }

        if (!$this->startdate1) {
            return 'Never';
        }

        if (!$this->nextrun || $this->nextrun == 0) {
            return 'Never';
        }

        return Carbon::createFromTimestamp($this->nextrun)->format('d/m/Y H:i');
        
    }

    public function setDayOption($request) {
        $nthflag = $everynday = $dayofmonth = $weekofmonth = $daysofweek = null;

        if ($request->input('dailytask')) {

            if ($request->input('dailytask') == 1){  // every day
                $daysofweek = '1;2;3;4;5;6;7';
                $nthflag = 1;
            } else if ($request->input('dailytask') == 2){ // working day
                $daysofweek = '1;2;3;4;5';
                $nthflag = 1;
            } else { // every n day 
                $nthflag = 0;
                $everynday = $request->input('dayinmonth');
            }
        }

        if ($request->get('weekdays')) {
            $daysofweek = implode(';', $request->get('weekdays'));
        }
        if ($request->input('rdayinmonth')) {
            if ($request->input('rdayinmonth') == 1)
            {
                $nthflag = 1;
                $dayofmonth = $request->input('dayofmonth');
            } else {
                $nthflag = 0;
                $weekofmonth = $request->input('week_of_month');
                $daysofweek = $request->input('dayofweek');
            }
        }

        $this->daysofweek = $daysofweek;
        $this->everynday = $everynday;
        $this->nthflag = $nthflag;
        $this->daysofweek = $daysofweek;
        $this->dayofmonth = $dayofmonth;
        $this->weekofmonth = $weekofmonth;
        $this->monthes = $request->get('monthly') ? implode(';', $request->get('monthly')) : null;
        $this->everynweek = $request->input('week') ? $request->input('week') : null;
    }

    public function getNdaysAttribute() {
        $ndays = -1;
        if ($this->schtype == ScheduleType::DAILY && $this->nthflag == 0) {
            $ndays = $this->everynday? $this->everynday: -1;
        }
        return $ndays;
    }

    public function getmonthdayAttribute() {
        $monthday = -1;
        if ($this->schtype == ScheduleType::MONTHLY && $this->nthflag == 1) {
            $monthday = $this->dayofmonth? $this->dayofmonth: -1;
        }
        return $monthday;
    }

    public function getdayofweekAttribute() {
        $dayofweek = array();
        if (($this->schtype == ScheduleType::DAILY && $this->nthflag == 1)
            ||($this->schtype == ScheduleType::WEEKLY)
            ||($this->schtype == ScheduleType::MONTHLY && $this->nthflag == 0)) {
            
            $dayofweek = explode(';', $this->daysofweek); 
        }

        return $dayofweek;
    }

    public function getnweeksAttribute() {
        $nweeks = -1;
        if ($this->schtype == ScheduleType::WEEKLY) {
            $nweeks = $this->everynweek? $this->everynweek: -1;
        }

        return $nweeks;
    }

    public function getmonthweekAttribute() {
        $monthweek = -1;
        if ($this->schtype == ScheduleType::MONTHLY) {
            $monthweek = $this->weekofmonth? $this->weekofmonth: -1;
            //if ($monthweek < 0)  $monthweek = 0;
        }

        return $monthweek;
    }

    public function getnmonthesAttribute() {
        $nmonthes = -1;

        if ($this->schtype == ScheduleType::QUARTERLY) $nmonthes = 3;
        if ($this->schtype == ScheduleType::SEMIANNUAL) $nmonthes = 6;
        if ($this->schtype == ScheduleType::ANNUAL) $nmonthes = 12;

        return $nmonthes;
    }

    public function getlmonthesAttribute() {
        $monthes = array();

        if ($this->schtype == ScheduleType::MONTHLY) {
            $monthes = explode(';', $this->monthes);
        }

        return $monthes;
    }
}
