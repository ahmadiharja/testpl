<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncDisplayMapping extends Model
{
    protected $table = 'sync_display_mappings';

    protected $fillable = [
        'workstation_id',
        'requested_client_id',
        'signal_name',
        'signal_regulation',
        'signal_classification',
        'signal_hash',
        'resolved_display_id',
        'confidence',
        'hit_count',
        'last_matched_at',
    ];

    protected $casts = [
        'last_matched_at' => 'datetime',
    ];

    public function workstation()
    {
        return $this->belongsTo(Workstation::class);
    }

    public function display()
    {
        return $this->belongsTo(Display::class, 'resolved_display_id');
    }
}
