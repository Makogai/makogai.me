<?php

namespace Database\Factories;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $started = fake()->dateTimeBetween('-6 years', '-6 months');
        $isCurrent = fake()->boolean(35);
        $ended = $isCurrent ? null : fake()->dateTimeBetween($started, 'now');

        return [
            'user_id' => User::factory(),
            'company' => fake()->company(),
            'role' => fake()->jobTitle(),
            'location' => fake()->optional()->city(),
            'employment_type' => fake()->randomElement(['Full-time', 'Contract', 'Part-time']),
            'summary' => fake()->sentence(18),
            'highlights' => fake()->randomElements([
                'Shipped a new onboarding flow that improved conversion.',
                'Built a design system and component library.',
                'Optimized core pages for Lighthouse performance.',
                'Owned CI/CD and observability improvements.',
            ], rand(2, 4)),
            'started_on' => $started->format('Y-m-d'),
            'ended_on' => $ended?->format('Y-m-d'),
            'is_current' => $isCurrent,
            'company_url' => fake()->optional(0.4)->url(),
            'company_logo_path' => null,
            'sort_order' => 0,
            'is_featured' => true,
        ];
    }
}
