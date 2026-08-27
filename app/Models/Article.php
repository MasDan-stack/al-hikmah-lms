<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articles';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'cover_caption',
        'status',
        'is_featured',
        'views_count',
        'shares_count',
        'reading_time',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'shares_count' => 'integer',
            'reading_time' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title).'-'.Str::random(5);
            }
            if (empty($article->reading_time)) {
                $article->reading_time = $article->calculateReadingTime();
            }
            if ($article->status === 'published' && empty($article->published_at)) {
                $article->published_at = now();
            }
        });

        static::updating(function (Article $article) {
            if ($article->isDirty('content')) {
                $article->reading_time = $article->calculateReadingTime();
            }
            if ($article->isDirty('status') && $article->status === 'published' && empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'article_tag', 'article_id', 'tag_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getCoverUrlAttribute(): string
    {
        if (empty($this->cover_image)) {
            return asset('assets/img/1.jpg');
        }

        if (Str::startsWith($this->cover_image, ['http://', 'https://'])) {
            return $this->cover_image;
        }

        if (Str::startsWith($this->cover_image, ['assets/', 'img/', 'template/'])) {
            return asset($this->cover_image);
        }

        return Storage::disk('public')->url($this->cover_image);
    }

    public function getReadingTimeLabelAttribute(): string
    {
        $minutes = $this->reading_time ?: $this->calculateReadingTime();

        return $minutes.' menit baca';
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? 'Admin AL-HIKMAH';
    }

    public function getPublishedDateAttribute(): string
    {
        return $this->published_at
            ? $this->published_at->translatedFormat('d F Y')
            : $this->created_at->translatedFormat('d F Y');
    }

    public function calculateReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content ?? ''));

        return max(1, (int) ceil($wordCount / 200));
    }
}
