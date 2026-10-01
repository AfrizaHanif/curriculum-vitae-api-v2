<?php

namespace Database\Factories;

use App\Models\Expertise;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expertise>
 */
class ExpertiseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'title' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'icon' => 'code',
        ];
    }
}
