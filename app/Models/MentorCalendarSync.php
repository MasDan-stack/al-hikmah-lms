<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorCalendarSync extends Model
{
    protected $fillable = [
        'mentor_id',
        'provider',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'calendar_id',
        'sync_token',
        'last_synced_at',
        'busy_slots_cache',
        'ical_token',
        'privacy_mode',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'busy_slots_cache' => 'array',
            'token_expires_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'privacy_mode' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
