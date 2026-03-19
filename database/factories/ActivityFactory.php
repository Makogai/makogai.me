<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['feature', 'fix', 'idea']),
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->optional()->sentence(14),
            'happened_at' => $this->faker->dateTimeBetween('-120 days', 'now')->format('Y-m-d'),
            'url' => $this->faker->optional(0.2)->url(),
            'meta' => [],
        ];
    }
}
