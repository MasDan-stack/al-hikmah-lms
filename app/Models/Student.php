<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'full_name',
        'age',
        'gender',
        'location',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(Mentor::class, 'mentor_student')
            ->withPivot(['day_assigned', 'time_assigned', 'is_active'])
            ->withTimestamps();
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'student_program')
            ->withPivot('status', 'enrolled_at')
            ->withTimestamps();
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getDisplayName(): string
    {
        return $this->user?->name ?? $this->full_name ?? 'Santri';
    }

    public function getParentNameAttribute(): ?string
    {
        return $this->parent?->user?->name ?? 'Orang Tua';
    }

    public function getParentPhoneAttribute(): ?string
    {
        return $this->parent?->user?->phone ?? $this->parent?->emergency_phone ?? '-';
    }
}
