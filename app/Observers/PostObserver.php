<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\Content\MarkdownService;
use Illuminate\Support\Str;

class PostObserver
{
    public function __construct(
        protected MarkdownService $markdown,
    ) {}

    public function saving(Post $post): void
    {
        if (! $post->slug) {
            $post->slug = $this->uniqueSlug($post, (string) $post->title);
        }

        if ($post->isDirty('slug')) {
            $post->slug = $this->uniqueSlug($post, (string) $post->slug);
        }

        if ($post->isDirty('title') && ! $post->isDirty('slug')) {
            $post->slug = $this->uniqueSlug($post, (string) $post->title);
        }

        if ($post->isDirty('content_markdown') || ! $post->content_html) {
            $post->content_html = $this->markdown->renderToHtml((string) $post->content_markdown);
        }

        if ($post->isDirty('content_markdown') || ! $post->excerpt) {
            $post->excerpt = $this->markdown->excerpt((string) $post->content_markdown);
        }

        if ($post->isDirty('content_markdown') || ! $post->reading_time_minutes) {
            $post->reading_time_minutes = $this->markdown->readingTimeMinutes((string) $post->content_markdown);
        }
    }

    protected function uniqueSlug(Post $post, string $value): string
    {
        $base = Str::slug($value) ?: 'post';
        $slug = $base;
        $suffix = 2;

        while (
            Post::query()
                ->where('slug', $slug)
                ->when($post->exists, fn ($q) => $q->whereKeyNot($post->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
