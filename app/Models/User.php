<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    protected $table='users';
    protected $guarded = [];
    public $timestamps = false;

    protected $fillable = [
        'name', 'email', 'password', 'fullname', 'sync_user', 'sync_password', 'sync_password_raw', 'facility_id',
        'activation_code', 'status', 'facility_name', 'timezone', 'workgroup_name', 'last_password_changed'
    ];

    protected $appends = ['role'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // Data relationship
    public function facility(){
        return $this->belongsTo('App\Models\Facility');
    }

    public function userLevel(){
        return $this->belongsTo('App\Models\UserLevel');
    }
    
    public function setPasswordAttribute2($password)
    {   
        // in case user choose reset password, no need to use bcrypt
        if (!Str::startsWith($password, '$2y$10$'))
            $this->attributes['password'] = bcrypt($password);
        else 
            $this->attributes['password'] = ($password);
    }

    public function setPasswordAttribute($password)
    {
        // Check if the password needs to be hashed before setting it
        if (Hash::needsRehash($password)) {
            $this->attributes['password'] = Hash::make($password);
        } else {
            $this->attributes['password'] = $password;
        }
    }

    public function getRoleAttribute() {
        return  count($this->getRoleNames()) > 0 ? $this->getRoleNames()->first() : '';
    }

    public function getTimezoneAttribute() {
        // if ($this->facility) {
        //     return $this->facility->timezone;
        // }
        return $this->attributes['timezone']?$this->attributes['timezone']:'UTC';
    }
    
    public function sendPasswordResetNotification($token)
    {   
        $this->notify(new ResetPasswordNotification($token));     
    }
}
