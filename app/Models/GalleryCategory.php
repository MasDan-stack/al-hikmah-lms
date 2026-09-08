<?php

namespace App\Models;

use Database\Factories\GalleryCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class GalleryCategory extends Model
{
    /** @use HasFactory<GalleryCategoryFactory> */
    use HasFactory, SoftDeletes;

    public const GROUPS = [
        'Kategori Utama' => 'Kategori Utama (Core Activities)',
        'Acara Khusus' => 'Acara Khusus & Event',
        'Prestasi & Kolaborasi' => 'Prestasi & Kolaborasi',
        'Lainnya' => 'Lainnya (Umum)',
    ];

    public const BADGE_OPTIONS = [
        'bg-success' => 'Hijau Utama (bg-success)',
        'bg-primary' => 'Biru Utama (bg-primary)',
        'bg-info text-dark' => 'Biru Muda (bg-info)',
        'bg-warning text-dark' => 'Kuning / Emas (bg-warning)',
        'bg-danger' => 'Merah (bg-danger)',
        'bg-secondary' => 'Abu-abu (bg-secondary)',
        'bg-dark text-white' => 'Gelap / Hitam (bg-dark)',
        'bg-success-subtle text-success border border-success' => 'Hijau Lembut (bg-success-subtle)',
        'bg-primary-subtle text-primary border border-primary' => 'Biru Lembut (bg-primary-subtle)',
    ];

    public const ICON_OPTIONS = [
        'bi-book' => 'Buku / Pembelajaran (bi-book)',
        'bi-mortarboard' => 'Topi Toga / Santri (bi-mortarboard)',
        'bi-person-video3' => 'Pengajar / Mentor (bi-person-video3)',
        'bi-house-door' => 'Rumah / Home Visit (bi-house-door)',
        'bi-laptop' => 'Laptop / Bimbingan Online (bi-laptop)',
        'bi-balloon' => 'Balon / Acara Spesial (bi-balloon)',
        'bi-trophy' => 'Piala / Prestasi (bi-trophy)',
        'bi-people' => 'Orang / Kolaborasi (bi-people)',
        'bi-images' => 'Galeri Foto (bi-images)',
        'bi-star' => 'Bintang / Unggulan (bi-star)',
        'bi-heart' => 'Hati / Peduli (bi-heart)',
        'bi-journal-bookmark' => 'Jurnal / Kurikulum (bi-journal-bookmark)',
    ];

    protected $fillable = [
        'name',
        'slug',
        'group',
        'icon',
        'badge_class',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (GalleryCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name, '_');
            }
        });
    }

    /**
     * Relasi ke seluruh dokumentasi galeri dalam kategori ini
     *
     * @return HasMany<Gallery, $this>
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'category_id');
    }

    // ==========================================
    // 🔍 ELOQUENT SCOPES
    // ==========================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
