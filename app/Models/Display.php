<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Display extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    const STATUS_OK = 1;
    const STATUS_FAILED = 2;
    
    protected $table='displays';
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
    public function workstation(){
        return $this->belongsTo('App\Models\Workstation');
    }

    public function preferences(){
        return $this->hasMany('App\Models\DisplayPreference');
    }

    public function hours(){
        return $this->hasMany('App\Models\DisplayHour');
    }


    public function histories(){
        return $this->hasMany('App\Models\History');
    }

    public function tasks(){
        return $this->hasMany('App\Models\Task');
    }

    public function qatasks(){
        return $this->hasMany('App\Models\QATask');
    }
    
    public function preference($name) {
        $pref = $this->preferences()->where('name', $name)->first();
        return ($pref != null ? $pref->value : '');
    }

    public function getInventoryNumberAttribute() {
        return $this->preference('InventoryNumber');
    }

    public function getSensorAttribute() {
        $sensor= $this->preference('InternalSensor');
        return $sensor == true? "Internal": "External";
    }
    public function getComportAttribute() {
         
        return "-";
    }
    public function getTypeOfDisplayAttribute() {
        return $this->preference('TypeOfDisplay');
    }
    public function getCommunicationTypeTextAttribute() {
        $communicationValue = (int)$this->preference('CommunicationType');

        
        $workstation_pref =  $this->workstation()->first();
        $setting = $workstation_pref->settings_names()->where('setting_name','CommunicationType')->first();
        if ($setting) {
            $setting_value = $setting->setting_value;
            $communication = json_decode($setting_value,true);

            return $communication[$communicationValue];
        }
        return '';
    }

    public function getErrorAttribute(){
        /*$errors = $this->errors;

        $errors = str_replace(",","<br>",str_replace("\"","",str_replace("}","",str_replace("{","",$errors))));       */
        $a = json_decode($this->errors);

        return (is_array($a))?implode("<br/>", $a):'';
    }

    public function getLinkAttribute() {
        return "<a href='".url('/')."/display-settings/{$this->id}'>{$this->treetext}</a>";
    }


    public function getTreetextAttribute() {
        $res = '';
        if ($this->manufacturer) $res .= $this->manufacturer;
        if ($this->model) $res .= ' '.$this->model;
        if ($this->serial) $res .= ' ('.$this->serial.')';
        return $res;
    }

    public function getLastCalibrationTaskAttribute() {
        $t = $this->histories()->where('type', '2')->orderBy('time', 'DESC')->first();
        return $t;
    }

    public function getLastQATaskAttribute() {
        $t = $this->histories()->where('type', '71')->orderBy('time', 'DESC')->first();
        return $t;
    }

    public function getNextTaskAttribute() {
        //$t = $this->tasks()->orderBy('nextrun', 'ASC')->first();
        //return $t;
    }

    public function getLastErrorTaskAttribute() {
        /*$t = $this->tasks()->where('status', '<>', 0)->orderBy('id', 'DESC')->first();
        if ($t) {
            return $t->link;
        }*/
        return '';
    }

    public function getStatusTextAttribute() {
        return $this->status==1?"<span class='text-success'>OK</span>":"<span class='text-danger'>Failed</span>";
    }
    public function getHistoryLinkAttribute() {
        return "<a href='/histories?p={$this->id}'>{$this->treetext}</a>";
    }
}
