<?php

namespace App\Models;

use Database\Factories\GalleryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Gallery extends Model
{
    /** @use HasFactory<GalleryFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Daftar Kategori Resmi AL-HIKMAH
     */
    public const CATEGORIES = [
        // 1. Kategori Utama (Core Activities)
        'kegiatan_belajar_mengajar' => [
            'label' => 'Kegiatan Belajar Mengajar',
            'group' => 'Kategori Utama',
            'icon' => 'bi-book',
            'badge_class' => 'bg-success',
        ],
        'kegiatan_santri' => [
            'label' => 'Kegiatan Santri',
            'group' => 'Kategori Utama',
            'icon' => 'bi-mortarboard',
            'badge_class' => 'bg-primary',
        ],
        'kegiatan_mentor' => [
            'label' => 'Kegiatan Mentor / Pengajar',
            'group' => 'Kategori Utama',
            'icon' => 'bi-person-video3',
            'badge_class' => 'bg-info text-dark',
        ],
        'home_visit_offline' => [
            'label' => 'Home Visit / Bimbingan Offline',
            'group' => 'Kategori Utama',
            'icon' => 'bi-house-door',
            'badge_class' => 'bg-warning text-dark',
        ],
        'bimbingan_online' => [
            'label' => 'Bimbingan Online',
            'group' => 'Kategori Utama',
            'icon' => 'bi-laptop',
            'badge_class' => 'bg-secondary',
        ],

        // 2. Kategori Acara Khusus (Events)
        'acara_spesial' => [
            'label' => 'Acara Spesial',
            'group' => 'Acara Khusus',
            'icon' => 'bi-balloon',
            'badge_class' => 'bg-danger',
        ],
        'prestasi_santri' => [
            'label' => 'Prestasi Santri',
            'group' => 'Acara Khusus',
            'icon' => 'bi-trophy',
            'badge_class' => 'bg-success-subtle text-success border border-success',
        ],
        'kunjungan_kolaborasi' => [
            'label' => 'Kunjungan & Kolaborasi',
            'group' => 'Acara Khusus',
            'icon' => 'bi-people',
            'badge_class' => 'bg-primary-subtle text-primary border border-primary',
        ],
    ];

    /**
     * Rekomendasi Tag Standar
     */
    public const DEFAULT_TAGS = [
        'Anak',
        'Dewasa',
        'Muslimah',
        'Online',
        'Offline',
        'Home Visit',
        'Tahsin',
        'Tahfidz',
        'Wisuda',
        'Ujian',
        'Talaqqi',
        'Adab',
    ];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'category_id',
        'program_id',
        'image_url',
        'caption',
        'description',
        'event_date',
        'location',
        'tags',
        'is_featured',
        'is_published',
        'sort_order',
        'views_count',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'tags' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'event_date' => 'date',
            'sort_order' => 'integer',
            'views_count' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Gallery $gallery) {
            if (empty($gallery->slug)) {
                $gallery->slug = Str::slug($gallery->title).'-'.Str::random(5);
            }
        });
    }

    /**
     * @return BelongsTo<GalleryCategory, $this>
     */
    public function categoryItem(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==========================================
    // 🔍 ELOQUENT SCOPES
    // ==========================================

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (! empty($category) && $category !== 'all') {
            return $query->where(function ($q) use ($category) {
                $q->where('category', $category)
                    ->orWhereHas('categoryItem', function ($sub) use ($category) {
                        $sub->where('slug', $category)->orWhere('id', $category);
                    });
            });
        }

        return $query;
    }

    public function scopeProgramFilter(Builder $query, $programId): Builder
    {
        if (! empty($programId) && $programId !== 'all') {
            return $query->where('program_id', $programId);
        }

        return $query;
    }

    public function scopeTagFilter(Builder $query, ?string $tag): Builder
    {
        if (! empty($tag) && $tag !== 'all') {
            return $query->whereJsonContains('tags', $tag)
                ->orWhere('tags', 'like', "%\"{$tag}\"%");
        }

        return $query;
    }

    // ==========================================
    // 🎨 ACCESSORS & HELPERS
    // ==========================================

    public function getCategoryMetaAttribute(): array
    {
        if ($this->categoryItem) {
            return [
                'label' => $this->categoryItem->name,
                'group' => $this->categoryItem->group ?? 'Kategori Utama',
                'icon' => $this->categoryItem->icon ?? 'bi-images',
                'badge_class' => $this->categoryItem->badge_class ?? 'bg-success',
            ];
        }

        return self::CATEGORIES[$this->category] ?? [
            'label' => ucfirst(str_replace('_', ' ', $this->category ?? 'Umum')),
            'group' => 'Umum',
            'icon' => 'bi-images',
            'badge_class' => 'bg-secondary',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category_meta['label'];
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date
            ? $this->event_date->translatedFormat('d F Y')
            : $this->created_at->translatedFormat('d F Y');
    }

    public function getAssetUrlAttribute(): string
    {
        if (empty($this->image_url)) {
            return 'https://placehold.co/800x600/0d7a3e/ffffff?text=Dokumentasi+AL-HIKMAH';
        }

        if (Str::startsWith($this->image_url, ['http://', 'https://'])) {
            return $this->image_url;
        }

        if (Str::startsWith($this->image_url, 'assets/')) {
            return asset($this->image_url);
        }

        if (Str::startsWith($this->image_url, 'storage/')) {
            return asset($this->image_url);
        }

        return asset('storage/'.$this->image_url);
    }
}
