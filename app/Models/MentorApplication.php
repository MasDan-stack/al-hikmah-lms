<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MentorApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_code',
        'full_name',
        'email',
        'phone',
        'birth_date',
        'gender',
        'address',
        'city',
        'education',
        'institution',
        'experience_years',
        'experience_description',
        'specialization',
        'sanad_chain',
        'hifz_total_juz',
        'status',
        'current_stage',
        'final_score',
        'admin_notes',
        'rejection_reason',
        'submitted_at',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'final_score' => 'float',
            'experience_years' => 'integer',
            'hifz_total_juz' => 'integer',
            'current_stage' => 'integer',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MentorApplicationDocument::class, 'application_id');
    }

    public function testSessions(): HasMany
    {
        return $this->hasMany(MentorTestSession::class, 'application_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function mentor(): HasOne
    {
        return $this->hasOne(Mentor::class, 'application_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'submitted' => '<span class="badge bg-secondary">Baru Masuk</span>',
            'document_review' => '<span class="badge bg-info text-dark">Review Berkas</span>',
            'test_scheduled' => '<span class="badge bg-primary">Tes Dijadwalkan</span>',
            'test_completed' => '<span class="badge bg-primary">Tes Selesai</span>',
            'interview_scheduled' => '<span class="badge bg-warning text-dark">Wawancara</span>',
            'interview_completed' => '<span class="badge bg-warning text-dark">Wawancara Selesai</span>',
            'approved' => '<span class="badge bg-success">Diterima</span>',
            'rejected' => '<span class="badge bg-danger">Ditolak</span>',
            'withdrawn' => '<span class="badge bg-dark">Mundur</span>',
            default => '<span class="badge bg-light text-dark">Unknown</span>',
        };
    }
}
