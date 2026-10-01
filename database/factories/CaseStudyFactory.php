<?php

namespace Database\Factories;

use App\Models\CaseStudy;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseStudy>
 */
class CaseStudyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portfolio_id' => Portfolio::factory(),
            'role' => fake()->jobTitle(),
            'problems' => [fake()->sentence(), fake()->sentence()],
            'goals' => [fake()->sentence()],
            'responsibilities' => [fake()->sentence()],
            'diagrams' => [],
            'solutions' => [fake()->sentence()],
            'benefits' => [fake()->sentence()],
            'results' => [fake()->sentence()],
            'process' => [fake()->sentence()],
            'challenges' => [fake()->sentence()],
            'lessons' => [fake()->sentence()],
        ];
    }
}
