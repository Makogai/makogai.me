<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Models\Media;
use App\Services\Media\MediaLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $this->library->storeImage(
            $request->file('file'),
            userId: $request->user()?->getKey(),
            collection: 'library',
            alt: $request->validated('alt'),
        );

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

    public function destroy(Media $media): RedirectResponse
    {
        if ($media->disk === 'public') {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return back();
    }
}
