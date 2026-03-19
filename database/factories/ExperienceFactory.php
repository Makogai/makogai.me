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
        $started = $this->faker->dateTimeBetween('-6 years', '-6 months');
        $isCurrent = $this->faker->boolean(35);
        $ended = $isCurrent ? null : $this->faker->dateTimeBetween($started, 'now');

        return [
            'user_id' => User::factory(),
            'company' => $this->faker->company(),
            'role' => $this->faker->jobTitle(),
            'location' => $this->faker->optional()->city(),
            'employment_type' => $this->faker->randomElement(['Full-time', 'Contract', 'Part-time']),
            'summary' => $this->faker->sentence(18),
            'highlights' => $this->faker->randomElements([
                'Shipped a new onboarding flow that improved conversion.',
                'Built a design system and component library.',
                'Optimized core pages for Lighthouse performance.',
                'Owned CI/CD and observability improvements.',
            ], rand(2, 4)),
            'started_on' => $started->format('Y-m-d'),
            'ended_on' => $ended?->format('Y-m-d'),
            'is_current' => $isCurrent,
            'company_url' => $this->faker->optional(0.4)->url(),
            'company_logo_path' => null,
            'sort_order' => 0,
            'is_featured' => true,
        ];
    }
}
