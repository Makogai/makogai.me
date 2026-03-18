<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        /** @var array<string, mixed> $siteValue */
        $siteValue = Setting::query()->where('key', 'site')->value('value') ?? [];

        /** @var array<string, mixed> $profileValue */
        $profileValue = Setting::query()->where('key', 'profile')->value('value') ?? [];

        return Inertia::render('admin/settings/Edit', [
            'site' => array_merge([
                'site_name' => config('app.name'),
                'tagline' => null,
                'email' => null,
                'github_url' => null,
                'linkedin_url' => null,
                'x_url' => null,
            ], $siteValue),
            'profile' => array_merge([
                'full_name' => null,
                'headline' => null,
                'bio' => null,
                'portrait_light_path' => null,
                'portrait_dark_path' => null,
                'cover_path' => null,
            ], $profileValue),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        Setting::query()->updateOrCreate(
            ['key' => 'site'],
            ['value' => $request->validated()],
        );

        return back();
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $profile */
        $profile = Setting::query()->where('key', 'profile')->value('value') ?? [];

        Setting::query()->updateOrCreate(
            ['key' => 'profile'],
            ['value' => array_merge($profile, $request->validated())],
        );

        return back();
    }
}
