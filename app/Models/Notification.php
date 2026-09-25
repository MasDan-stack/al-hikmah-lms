<?php

namespace App\Models;

use App\Enums\NotificationType;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'category',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Helper untu mendapatkan enum NotificationType dengan fallback tipe string lama
     */
    public function getTypeEnum(): NotificationType
    {
        $rawType = $this->type instanceof NotificationType ? $this->type->value : (string) $this->type;

        return match ($rawType) {
            'success', 'enrollment_accepted', 'payment_success' => NotificationType::SUCCESS,
            'warning', 'schedule_offer', 'payment_reminder' => NotificationType::WARNING,
            'danger', 'error', 'cancelled' => NotificationType::DANGER,
            default => NotificationType::INFO,
        };
    }

    /**
     * Scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
