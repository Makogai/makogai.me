<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'category_id',
    'title',
    'slug',
    'excerpt',
    'content_markdown',
    'content_html',
    'syntax_highlighting_enabled',
    'reading_time_minutes',
    'cover_image_path',
    'is_featured',
    'published_at',
    'archived_at',
    'seo_title',
    'seo_description',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<PostCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    /**
     * @return BelongsToMany<PostTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(PostTag::class, 'post_post_tag');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNull('archived_at')
            ->whereNotNull('published_at');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'bool',
            'syntax_highlighting_enabled' => 'bool',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
            'reading_time_minutes' => 'int',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
