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

        return Inertia::render('blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->published_at && ! $post->archived_at, 404);

        $post->loadMissing(['category:id,name,slug', 'tags:id,name,slug']);

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
        ]);
    }
}
