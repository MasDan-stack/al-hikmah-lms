<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorIncentive extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'incentive_type',
        'title',
        'description',
        'amount',
        'certificate_number',
        'certificate_url',
        'period',
        'awarded_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'awarded_at' => 'datetime',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
