<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Post::query()
            ->published()
            ->latest('published_at')
            ->with(['category:id,name,slug', 'tags:id,name,slug']);

        $search = (string) $request->query('q', '');
        $categorySlug = (string) $request->query('category', '');
        $tagSlug = (string) $request->query('tag', '');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('excerpt', 'like', '%'.$search.'%')
                    ->orWhere('content_markdown', 'like', '%'.$search.'%');
            });
        }

        if ($categorySlug !== '') {
            $query->whereHas('category', function ($q) use ($categorySlug): void {
                $q->where('slug', $categorySlug);
            });
        }

        if ($tagSlug !== '') {
            $query->whereHas('tags', function ($q) use ($tagSlug): void {
                $q->where('slug', $tagSlug);
            });
        }

        $posts = $query
            ->paginate(10, [
                'id',
                'title',
                'slug',
                'excerpt',
                'reading_time_minutes',
                'cover_image_path',
                'category_id',
                'published_at',
            ])
            ->withQueryString();

        $siteSettings = $request->attributes->get('settings.site') ?? [];

        $title = $siteSettings['default_seo_title'] ?? ($siteSettings['site_name'] ?? config('app.name'));
        $description = $siteSettings['default_seo_description'] ?? ($siteSettings['tagline'] ?? null);

        return Inertia::render('blog/Index', [
            'posts' => $posts,
            'filters' => [
                'q' => $search,
                'category' => $categorySlug,
                'tag' => $tagSlug,
            ],
            'meta' => [
                'title' => $title.' – Blog',
                'description' => $description,
                'type' => 'website',
                'image_path' => $siteSettings['default_og_image_path'] ?? null,
            ],
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->published_at && ! $post->archived_at, 404);

        $post->loadMissing(['category:id,name,slug', 'tags:id,name,slug']);

        $related = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when(
                $post->category,
                fn ($q) => $q->where('category_id', $post->category_id),
            )
            ->when(
                $post->tags?->count(),
                fn ($q) => $q->orWhereHas('tags', function ($t) use ($post): void {
                    $t->whereIn('post_tags.id', $post->tags->pluck('id'));
                }),
            )
            ->latest('published_at')
            ->limit(4)
            ->get([
                'id',
                'title',
                'slug',
                'excerpt',
                'cover_image_path',
                'published_at',
            ]);

        $siteSettings = request()->attributes->get('settings.site') ?? [];

        $title = $post->seo_title ?: ($post->title.' – Blog');
        $description = $post->seo_description ?: ($post->excerpt ?: ($siteSettings['default_seo_description'] ?? null));

        return Inertia::render('blog/Show', [
            'post' => $post->only([
                'id',
                'title',
                'slug',
                'excerpt',
                'reading_time_minutes',
                'cover_image_path',
                'content_html',
                'published_at',
                'seo_title',
                'seo_description',
                'syntax_highlighting_enabled',
            ]) + [
                'category' => $post->category,
                'tags' => $post->tags,
            ],
            'related' => $related,
            'meta' => [
                'title' => $title,
                'description' => $description,
                'type' => 'article',
                'image_path' => $post->cover_image_path ?: ($siteSettings['default_og_image_path'] ?? null),
            ],
        ]);
    }
}
