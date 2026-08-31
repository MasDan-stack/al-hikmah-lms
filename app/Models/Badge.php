<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'category',
        'points_reward',
        'criteria_json',
        'is_active',
    ];

    protected $casts = [
        'criteria_json' => 'array',
        'points_reward' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_badges')
            ->withPivot(['earned_at', 'trigger_data', 'announced_to_parent'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<StudentBadge, $this>
     */
    public function studentBadges(): HasMany
    {
        return $this->hasMany(StudentBadge::class);
    }
}
