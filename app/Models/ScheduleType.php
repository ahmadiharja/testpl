<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleType extends Model
{
    use HasFactory;

    const STARTUP = 0;
    const ONCE = 1;
    const DAILY = 2;
    const WEEKLY = 3;
    const MONTHLY = 4;
    const QUARTERLY = 5;
    const SEMIANNUAL = 6;
    const ANNUAL = 7;
    
    protected $table='schedule_types';
    protected $guarded = [];
    public $timestamps = false;

    // Relationship
    public function tasks(){
        return $this->hasMany('App\Task',  'schtype','client_id');
    }
}
