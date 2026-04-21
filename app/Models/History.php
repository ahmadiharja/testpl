<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class History extends Model
{
    // Table Name
    protected $table = 'histories';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;

    
    // Fillable fields to use with ::create function
    protected $fillable = ['id', 'name', 'type', 'display_id', 'time', 'disabled', 
    'result', 'header', 'steps', 'levels', 'regulation', 'classification', 'measurements','scores'];

    protected $appends = ['link', 'resultIcon', 'hide_info'];

    // Data relationship
    public function display(){
        return $this->belongsTo('App\Models\Display');
    }

    public function task(){
        return $this->belongsTo('App\Models\Task');
    }

    public function syncResolution()
    {
        return $this->hasOne(HistorySyncResolution::class, 'history_id');
    }

    // MUTATOR
    public function getResultDescAttribute() {
        switch ($this->result) {
            case 2: return 'OK';
            case 3: return 'Failed';
            case 4: return 'Skipped';
            case 5: return 'Canceled';
        }
    }

    public function getResultTextAttribute() {
        switch ($this->result) {
            case 2: return '<span class="text-success">OK</span>';
            case 3: return '<span class="text-danger">Failed</span>';
            case 4: return '<span class="text-warning">Skipped</span>';
            case 5: return '<span class="text-muted">Canceled</span>';
        }
    }

    public function getResultIconAttribute() {
        switch ($this->result) {
            case 2: return '<span class="badge bg-success badge-circle rounded-circle p-0"><svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.71402 8.17706L2.07022 5.53325C1.92776 5.39079 1.73454 5.31076 1.53307 5.31076C1.3316 5.31076 1.13839 5.39079 0.995929 5.53325C0.85347 5.67571 0.773438 5.86893 0.773438 6.0704C0.773438 6.17015 0.793086 6.26893 0.831261 6.36109C0.869437 6.45326 0.925391 6.537 0.995929 6.60754L4.18069 9.7923C4.47783 10.0894 4.95783 10.0894 5.25498 9.7923L13.3159 1.73135C13.4584 1.58889 13.5384 1.39567 13.5384 1.19421C13.5384 0.992738 13.4584 0.799522 13.3159 0.657062C13.1735 0.514603 12.9803 0.43457 12.7788 0.43457C12.5773 0.43457 12.3841 0.514603 12.2416 0.657062L4.71402 8.17706Z" fill="#27AE60"></path></svg></span>';
            case 3: return '<span class="badge bg-danger badge-circle rounded-circle p-0"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.33398 1.33301L10.6667 10.6657" stroke="#EB5757" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1.33355 10.6657L10.6663 1.33301" stroke="#EB5757" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                </span>';
            case 4: return '<i title="Skipped" class="fa fa-2x fa-pause-circle  '.($this->disabled?'text-muted':'text-warning').'"></i>';
            case 5: return '<i title="Cancelled" class="fa fa-2x fa-minus-circle  '.($this->disabled?'text-muted':'text-warning').'"></i>';
        }
    }
    public function getStatusTextAttribute() {
        switch ($this->result) {
            case 2: return 'OK';
            case 3: return 'Failed';
            case 4: return 'Skipped';
            case 5: return 'Canceled';
        }
    }
    public function getHeaderAttribute($value) {
        $a = json_decode($value, true);
        return (isset($a['elements']) ? $a['elements'] : []);
    }

    public function getHeader($name) {
        return isset($this->header[$name]) ? $this->header[$name] : '';
        return $this->header[$name]?$this->header[$name]:'';
    }

    public function getStepsAttribute() {
        if ($this->attributes['steps']!=null) {
            return json_decode($this->attributes['steps'], true);
        }
        return array();
    }

    public function getTimeDisplay() {
        return Carbon::createFromTimestampUTC((int) $this->time)->format('d/m/Y H:i');
    }
   
    public function getPerformDateAttribute() {
        return Carbon::createFromTimestampUTC((int) $this->attributes['time'])->format('d/m/Y');
    }
    public function getPerformDateTimeAttribute() {
        return Carbon::createFromTimestampUTC((int) $this->attributes['time'])->format('Y.m.d H:i:s');
    }

    public function getHideinfoAttribute() {
        return $this->type == 71?1:0;
    }

    public function getLinkAttribute() {
        return $this->hideInfo ? $this->name : "<a href='".url('/')."/histories/{$this->id}'>{$this->name}</a>";
    }

    public function getRegulationNameAttribute() {
        $ws = $this->display->workstation;
        return $ws?$ws->getRegulationName($this->regulation):'';
    }

    public function getClassificationNameAttribute() {
        $ws = $this->display->workstation;
        return $ws?$ws->getClassificationName($this->regulation, $this->classification):'';
    }
    public function getScoresAttribute() {
        if ($this->attributes['scores']!=null) {
            return json_decode($this->attributes['scores'], true);
        }
        return array();
    }
    public function getLevelsAttribute() {
        if ($this->attributes['levels']!=null) {
            return json_decode($this->attributes['levels'], true);
        }
        return array();
    }

    public function getStepsMeasured($step_name) {
        $target = array_filter($this->steps, 
                function($a) {
                    return $a['name'] == 'Target & Results';
                }
        );
        $scores = reset($target)['scores'];
        if (!$scores)  return '';
        $res = array_filter($scores, function($a) use ($step_name) {
            return $a['name'] == $step_name;
        });
        $val = reset($res)['measured'];
        $val = str_replace("<sup>2</sup>", "²", $val);
        return $val;
    }

    public function getStepsLimit($step_name) {
        $target = array_filter($this->steps, 
                function($a) {
                    return $a['name'] == 'Target & Results';
                }
        );
        $scores = reset($target)['scores'];
        if (!$scores)  return '';
        $res = array_filter($scores, function($a) use ($step_name) {
            return $a['name'] == $step_name;
        });
        return reset($res)['limit'];
    }

}
