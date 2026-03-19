<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SiteSettingsSeeder::class,
            TaxonomySeeder::class,
        ]);

        $shouldSeedDemoContent = app()->environment(['local', 'testing'])
            || filter_var(env('APP_SEED_DEMO_DATA', false), FILTER_VALIDATE_BOOL);

        if ($shouldSeedDemoContent) {
            $this->call([
                DemoContentSeeder::class,
            ]);
        }
    }
}
