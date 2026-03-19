<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Models\Experience;
use App\Models\Media;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use App\Services\Media\MediaLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function __construct(
        protected MediaLibrary $library,
    ) {}

    public function index(): Response
    {
        $media = Media::query()
            ->latest('id')
            ->paginate(48)
            ->withQueryString();

        return Inertia::render('admin/media/Index', [
            'media' => $media->through(fn (Media $m) => [
                'id' => $m->id,
                'path' => $m->path,
                'disk' => $m->disk,
                'alt' => $m->alt,
                'mime_type' => $m->mime_type,
                'size_bytes' => $m->size_bytes,
                'url' => $m->disk === 'public' ? "/storage/{$m->path}" : null,
                'created_at' => $m->created_at?->toDateTimeString(),
            ]),
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse|JsonResponse
    {
        $media = $this->library->storeImage(
            $request->file('file'),
            userId: $request->user()?->getKey(),
            collection: 'library',
            alt: $request->validated('alt'),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $media->id,
                'path' => $media->path,
                'alt' => $media->alt,
                'url' => $media->disk === 'public' ? "/storage/{$media->path}" : null,
            ]);
        }

        return back();
    }

    public function library(): JsonResponse
    {
        $media = Media::query()
            ->latest('id')
            ->paginate(24);

        return response()->json([
            'data' => $media->getCollection()->map(fn (Media $m) => [
                'id' => $m->id,
                'path' => $m->path,
                'alt' => $m->alt,
                'url' => $m->disk === 'public' ? "/storage/{$m->path}" : null,
            ])->values(),
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
        ]);
    }

    public function usage(Media $media): JsonResponse
    {
        $path = (string) $media->path;

        $usedInSettings = [];
        /** @var array<string, mixed> $profile */
        $profile = Setting::query()->where('key', 'profile')->value('value') ?? [];
        foreach (['portrait_light_path', 'portrait_dark_path', 'cover_path'] as $key) {
            if (($profile[$key] ?? null) === $path) {
                $usedInSettings[] = [
                    'type' => 'setting',
                    'label' => "Profile: {$key}",
                    'url' => '/admin/settings',
                ];
            }
        }

        $postsCover = Post::query()
            ->where('cover_image_path', $path)
            ->limit(50)
            ->get(['id', 'title']);

        $postsMarkdown = Post::query()
            ->where('content_markdown', 'like', '%'.$path.'%')
            ->limit(50)
            ->get(['id', 'title']);

        $projectsCover = Project::query()
            ->where('cover_image_path', $path)
            ->limit(50)
            ->get(['id', 'title']);

        $projectsMarkdown = Project::query()
            ->where('content_markdown', 'like', '%'.$path.'%')
            ->limit(50)
            ->get(['id', 'title']);

        $experiencesLogo = Experience::query()
            ->where('company_logo_path', $path)
            ->limit(50)
            ->get(['id', 'company', 'role']);

        $items = [
            ...$usedInSettings,
            ...$postsCover->map(fn (Post $p) => [
                'type' => 'post',
                'label' => "Post cover: {$p->title}",
                'url' => "/admin/posts/{$p->id}/edit",
            ])->all(),
            ...$postsMarkdown->map(fn (Post $p) => [
                'type' => 'post',
                'label' => "Post markdown: {$p->title}",
                'url' => "/admin/posts/{$p->id}/edit",
            ])->all(),
            ...$projectsCover->map(fn (Project $p) => [
                'type' => 'project',
                'label' => "Project cover: {$p->title}",
                'url' => "/admin/projects/{$p->id}/edit",
            ])->all(),
            ...$projectsMarkdown->map(fn (Project $p) => [
                'type' => 'project',
                'label' => "Project markdown: {$p->title}",
                'url' => "/admin/projects/{$p->id}/edit",
            ])->all(),
            ...$experiencesLogo->map(fn (Experience $e) => [
                'type' => 'experience',
                'label' => 'Experience logo: '.Str::limit($e->company.' — '.$e->role, 80),
                'url' => "/admin/experience/{$e->id}/edit",
            ])->all(),
        ];

        return response()->json([
            'path' => $path,
            'count' => count($items),
            'items' => $items,
        ]);
    }

    public function destroy(Media $media): RedirectResponse
    {
        if ($media->disk === 'public') {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return back();
    }
}
