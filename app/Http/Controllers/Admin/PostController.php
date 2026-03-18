<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Services\Media\MediaLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function __construct(
        protected MediaLibrary $library,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $posts = Post::query()
            ->latest('updated_at')
            ->paginate(20, [
                'id',
                'title',
                'slug',
                'published_at',
                'is_featured',
                'updated_at',
            ])
            ->withQueryString();

        return Inertia::render('admin/posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/posts/Edit', [
            'post' => null,
            'categories' => PostCategory::query()->orderBy('name')->get(['id', 'name']),
            'tags' => PostTag::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $coverPath = $this->storeCoverImage($request->file('cover_image'), $request->user()?->getKey());

        $post = Post::query()->create([
            'user_id' => $request->user()?->getKey(),
            ...$request->safe()->except(['tag_ids', 'cover_image']),
            'cover_image_path' => $coverPath ?: $request->validated('cover_image_path'),
        ]);

        $post->tags()->sync($request->validated('tag_ids', []));

        return to_route('admin.posts.edit', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): RedirectResponse
    {
        return to_route('admin.posts.edit', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): Response
    {
        $post->loadMissing('tags:id');

        return Inertia::render('admin/posts/Edit', [
            'post' => $post->only([
                'id',
                'title',
                'slug',
                'excerpt',
                'content_markdown',
                'category_id',
                'published_at',
                'archived_at',
                'is_featured',
                'syntax_highlighting_enabled',
                'cover_image_path',
                'seo_title',
                'seo_description',
            ]) + [
                'tag_ids' => $post->tags->pluck('id')->all(),
            ],
            'categories' => PostCategory::query()->orderBy('name')->get(['id', 'name']),
            'tags' => PostTag::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $coverPath = $this->storeCoverImage($request->file('cover_image'), $request->user()?->getKey());

        if ($coverPath) {
            $post->cover_image_path = $coverPath;
        } elseif ($request->filled('cover_image_path')) {
            $post->cover_image_path = $request->validated('cover_image_path');
        }

        $post->fill($request->safe()->except(['tag_ids', 'cover_image', 'cover_image_path']));
        $post->save();

        $post->tags()->sync($request->validated('tag_ids', []));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return to_route('admin.posts.index');
    }

    protected function storeCoverImage(?UploadedFile $file, int|string|null $userId = null): ?string
    {
        if (! $file) {
            return null;
        }

        $media = $this->library->storeImage(
            $file,
            userId: $userId,
            collection: 'covers',
        );

        return $media->path;
    }
}
