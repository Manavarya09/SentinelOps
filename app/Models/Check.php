<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Check extends Model
{
    protected $fillable = [
        'monitor_id',
        'status_code',
        'response_time',
        'is_success',
        'error_message',
        'ssl_days_left',
        'checked_at',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
