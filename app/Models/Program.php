<?php

namespace App\Models;

use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'icon',
        'description',
        'duration_weeks',
        'price',
        'level',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'duration_weeks' => 'integer',
            'price' => 'decimal:2',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope untuk kategori program anak
     */
    public function scopeAnak($query)
    {
        return $query->where('category', 'anak');
    }

    /**
     * Scope untuk kategori program dewasa & muslimah
     */
    public function scopeDewasa($query)
    {
        return $query->where('category', 'dewasa');
    }

    /**
     * Scope untuk kategori bahasa arab
     */
    public function scopeBahasaArab($query)
    {
        return $query->where('category', 'bahasa_arab');
    }

    /**
     * Format harga ke rupiah
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_program')
            ->withPivot('status', 'enrolled_at')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
