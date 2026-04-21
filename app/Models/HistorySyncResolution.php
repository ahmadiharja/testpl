<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorySyncResolution extends Model
{
    protected $table = 'history_sync_resolutions';

    protected $fillable = [
        'history_id',
        'workstation_id',
        'requested_client_id',
        'resolved_display_id',
        'method',
        'confidence',
        'notes',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function history()
    {
        return $this->belongsTo(History::class);
    }

    public function workstation()
    {
        return $this->belongsTo(Workstation::class);
    }

    public function display()
    {
        return $this->belongsTo(Display::class, 'resolved_display_id');
    }
}
