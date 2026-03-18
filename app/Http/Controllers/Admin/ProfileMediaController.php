<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileMediaRequest;
use App\Models\Setting;
use App\Services\Media\MediaLibrary;
use Illuminate\Http\RedirectResponse;

class ProfileMediaController extends Controller
{
    public function __construct(
        protected MediaLibrary $library,
    ) {}

    public function __invoke(UpdateProfileMediaRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $profile */
        $profile = Setting::query()->where('key', 'profile')->value('value') ?? [];

        $updates = [];

        foreach (['portrait_light', 'portrait_dark', 'cover'] as $key) {
            if (! $request->hasFile($key)) {
                continue;
            }

            $file = $request->file($key);

            $path = $this->library->storeImage(
                $file,
                userId: $request->user()?->getKey(),
                collection: 'profile',
            )->path;

            $updates[$key.'_path'] = $path;
        }

        $merged = array_merge($profile, $updates);

        Setting::query()->updateOrCreate(
            ['key' => 'profile'],
            ['value' => $merged],
        );

        return back();
    }
}
