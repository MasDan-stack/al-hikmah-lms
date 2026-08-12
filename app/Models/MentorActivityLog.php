<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'action',
        'description',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public static function log(?int $mentorId, string $action, string $description): ?self
    {
        if (! $mentorId) {
            return null;
        }

        return self::create([
            'mentor_id' => $mentorId,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
