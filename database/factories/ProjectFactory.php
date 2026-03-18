<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);
        $markdown = implode("\n\n", [
            "# {$title}",
            fake()->paragraphs(2, true),
            '## Highlights',
            '- '.fake()->sentence(),
            '- '.fake()->sentence(),
        ]);

        return [
            'user_id' => User::factory(),
            'title' => ucfirst($title),
            'slug' => null,
            'description' => fake()->sentence(18),
            'content_markdown' => $markdown,
            'content_html' => null,
            'cover_image_path' => null,
            'gallery' => [],
            'tech_stack' => fake()->randomElements(
                ['Laravel', 'Vue', 'Inertia', 'Tailwind', 'MySQL', 'Redis', 'Docker'],
                rand(3, 6),
            ),
            'repo_url' => 'https://github.com/',
            'demo_url' => null,
            'is_featured' => false,
            'published_at' => now()->subDays(rand(1, 180)),
            'seo_title' => null,
            'seo_description' => null,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => [
            'is_featured' => true,
        ]);
    }
}
