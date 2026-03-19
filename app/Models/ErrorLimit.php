<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLimit extends Model
{
    use HasFactory;
    
    protected $table='error_limits';
    protected $guarded = [];
    public $timestamps = false;
    
    public $primaryKey = 'id';
    public $incrementing = false;
    
    function eval($a) {
        if ($this->value == null || $this->value == '') return true;
        switch ($this->operator) {
            case '==':case '=': return $a == $this->value; break;
            case '<=': return floatval($a) <= floatval($this->value); break;
            case '>=': return floatval($a) >= floatval($this->value); break;
            case '<>': return floatval($a) != floatval($this->value); break;
        }
        return false;
    }

    function toString($a) {
        print $this->title . " : " . $a . $this->operator . $this->value;
    }

    public function getIoperatorAttribute() {
        switch ($this->operator) {
            case '==': case '=': return '<>';
            case '<=': return '>';
            case '>=': return '<';
            case '<>': return '=';
        }
        return '';
    }
}
