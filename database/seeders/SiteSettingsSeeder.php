<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->updateOrCreate(
            ['key' => 'site'],
            ['value' => [
                'site_name' => config('app.name', 'Makogai'),
                'tagline' => 'Full-stack developer focused on product quality and performance.',
                'email' => null,
                'github_url' => null,
                'linkedin_url' => null,
                'x_url' => null,
            ]],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'profile'],
            ['value' => [
                'full_name' => 'Makogai',
                'headline' => 'Full-stack Developer',
                'bio' => 'I design and build modern web products with Laravel, Vue, and a design-first mindset.',
                'portrait_light_path' => null,
                'portrait_dark_path' => null,
                'cover_path' => null,
            ]],
        );
    }
}
