<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasFactory;

    protected $table = 'blog_tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BlogTag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function (BlogTag $tag) {
            if ($tag->isDirty('name') && ! $tag->isDirty('slug')) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag', 'tag_id', 'article_id');
    }
}
