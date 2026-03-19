<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    // table name
    protected $table = 'email_templates';

    // primary key
    public $primaryKey = 'id';

    //
    public $timestamps = true;

    // Data replationship
    protected $fillable = ['title', 'value'];
}
