<?php

namespace Database\Factories;

use App\Enums\EducationDegree;
use App\Enums\EducationStatus;
use App\Enums\EducationType;
use App\Models\Education;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
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
            'institution' => fake()->company().' University',
            'type' => fake()->randomElement(EducationType::cases())->value,
            'address' => fake()->address(),
            'degree' => fake()->randomElement(EducationDegree::cases())->value,
            'academic_title' => fake()->randomElement(['S.Kom', 'B.Sc', 'M.Kom', null]),
            'major' => [
                'id' => fake()->words(2, true).' Engineering',
                'en' => fake()->words(2, true).' Engineering',
            ],
            'gpa' => fake()->randomFloat(2, 3.0, 4.0),
            'max_gpa' => 4.0,
            'status' => fake()->randomElement(EducationStatus::cases())->value,
            'start_period' => fake()->date(),
            'finish_period' => fake()->date(),
            'description' => [fake()->sentence(), fake()->sentence()],
            'latitude' => (string) fake()->latitude(),
            'longitude' => (string) fake()->longitude(),
        ];
    }
}
