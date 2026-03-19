<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use App\Models\PostTag;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'Vue', 'slug' => 'vue'],
            ['name' => 'Frontend Engineering', 'slug' => 'frontend-engineering'],
            ['name' => 'Backend Architecture', 'slug' => 'backend-architecture'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Performance', 'slug' => 'performance'],
            ['name' => 'UI/UX', 'slug' => 'ui-ux'],
            ['name' => 'Product Thinking', 'slug' => 'product-thinking'],
            ['name' => 'Career', 'slug' => 'career'],
        ];

        foreach ($categories as $category) {
            PostCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }

        $tags = [
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'Vue', 'slug' => 'vue'],
            ['name' => 'TypeScript', 'slug' => 'typescript'],
            ['name' => 'JavaScript', 'slug' => 'javascript'],
            ['name' => 'MySQL', 'slug' => 'mysql'],
            ['name' => 'Redis', 'slug' => 'redis'],
            ['name' => 'Docker', 'slug' => 'docker'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwind-css'],
            ['name' => 'Inertia', 'slug' => 'inertia'],
            ['name' => 'Testing', 'slug' => 'testing'],
            ['name' => 'CI/CD', 'slug' => 'ci-cd'],
            ['name' => 'UX', 'slug' => 'ux'],
            ['name' => 'Performance', 'slug' => 'performance'],
            ['name' => 'Architecture', 'slug' => 'architecture'],
        ];

        foreach ($tags as $tag) {
            PostTag::query()->updateOrCreate(
                ['slug' => $tag['slug']],
                ['name' => $tag['name']],
            );
        }
    }
}
