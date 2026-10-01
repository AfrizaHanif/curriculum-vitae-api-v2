<?php

namespace Database\Factories;

use App\Models\Portfolio;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'profile_id' => Profile::factory(),
            // 'parent_id' => null,
            'title' => $title,
            'slug' => Str::slug($title),
            'type' => fake()->randomElement(['Academic Project', 'Personal Project', 'Commercial Project']),
            'category' => fake()->randomElement(['Full-Stack Development', 'Frontend Development', 'Backend Development']),
            'image' => null,
            'gallery' => [],
            'video' => null,
            'start_period' => fake()->date(),
            'finish_period' => fake()->date(),
            'description' => fake()->paragraph(),
            'tags' => [fake()->word(), fake()->word()],
            'technology' => ['Laravel', 'React', 'TypeScript'],
            'repositories' => [['name' => 'github', 'url' => fake()->url()]],
            'demo_url' => fake()->url(),
        ];
    }
}
