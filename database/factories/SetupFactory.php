<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Setup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setup>
 */
class SetupFactory extends Factory
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
            'name' => fake()->word(),
            'category' => fake()->randomElement(['Hardware', 'Software', 'Desk']),
            'description' => fake()->sentence(),
            'reason' => fake()->sentence(),
        ];
    }
}
