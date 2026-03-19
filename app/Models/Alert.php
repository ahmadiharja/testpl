<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Alert extends Model
{
    use HasFactory, Notifiable;
    protected $table='alerts';
    protected $guarded = [];
    public $timestamps = false;
    
    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    // Private 
    private function sendEmail($result) {
        
    }
    
    public function facility(){
        return $this->belongsTo('App\Models\Facility');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
