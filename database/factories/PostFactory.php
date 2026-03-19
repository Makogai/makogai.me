<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(rand(4, 7));
        $markdown = implode("\n\n", [
            "# {$title}",
            $this->faker->paragraphs(2, true),
            '## Notes',
            '- '.$this->faker->sentence(),
            '- '.$this->faker->sentence(),
            "```php\n".'<?php'."\n\necho 'hello world';\n```",
        ]);

        return [
            'user_id' => User::factory(),
            'category_id' => PostCategory::factory(),
            'title' => $title,
            'slug' => null,
            'excerpt' => null,
            'content_markdown' => $markdown,
            'content_html' => null,
            'syntax_highlighting_enabled' => true,
            'reading_time_minutes' => 0,
            'cover_image_path' => null,
            'is_featured' => false,
            'published_at' => now()->subDays(rand(1, 90)),
            'seo_title' => null,
            'seo_description' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => [
            'is_featured' => true,
        ]);
    }
}
