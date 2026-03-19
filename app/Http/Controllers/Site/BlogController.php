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
        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->with(['category:id,name,slug', 'tags:id,name,slug'])
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
            'meta' => [
                'title' => $title,
                'description' => $description,
                'type' => 'article',
                'image_path' => $post->cover_image_path ?: ($siteSettings['default_og_image_path'] ?? null),
            ],
        ]);
    }
}
