<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisplayHour extends Model
{
    // Table Name
    protected $table = 'display_hours';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;

    // Fillable fields to use with ::create function
    protected $fillable = ['start', 'end', 'duration', 'display_id'];

    // Data relationship
    public function display(){
        return $this->belongsTo('App\Display');
    }
}
