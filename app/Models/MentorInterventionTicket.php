<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorInterventionTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'feedback_id',
        'mentor_id',
        'student_id',
        'parent_id',
        'session_id',
        'severity',
        'complaint_category',
        'sentiment_label',
        'parent_comment',
        'detected_keywords',
        'status',
        'action_plan',
        'resolution_notes',
        'handled_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'detected_keywords' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(MentorFeedback::class, 'feedback_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'escalated_to_mutation']);
    }

    public function getSeverityBadgeClass(): string
    {
        return match ($this->severity) {
            'critical' => 'bg-danger text-white',
            'high' => 'bg-warning text-dark',
            'medium' => 'bg-info text-dark',
            default => 'bg-secondary text-white',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-danger text-white',
            'in_progress' => 'bg-warning text-dark',
            'resolved' => 'bg-success text-white',
            'escalated_to_mutation' => 'bg-dark text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getCategoryLabel(): string
    {
        return match ($this->complaint_category) {
            'attendance_late' => 'Ketepatan Waktu & Kehadiran',
            'attitude_pedagogy' => 'Sikap & Metode Mengajar',
            'dissatisfaction' => 'Ketidakpuasan Umum',
            default => 'Keluhan Lainnya',
        };
    }
}
