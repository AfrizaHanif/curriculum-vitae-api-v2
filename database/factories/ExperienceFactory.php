<?php

namespace Database\Factories;

use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
use App\Models\Experience;
use App\Models\Profile;
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
        return [
            'profile_id' => Profile::factory(),
            'title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'type' => fake()->randomElement(ExperienceType::cases())->value,
            'address' => fake()->address(),
            'status' => fake()->randomElement(ExperienceStatus::cases())->value,
            'start_period' => fake()->date(),
            'finish_period' => fake()->date(),
            'description' => [fake()->sentence(), fake()->sentence()],
            'latitude' => (string) fake()->latitude(),
            'longitude' => (string) fake()->longitude(),
        ];
    }
}
