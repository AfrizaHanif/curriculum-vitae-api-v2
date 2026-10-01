<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'profile_id' => Profile::factory(),
            'portfolio_id' => null,
            'title' => substr($title, 0, 100),
            'slug' => Str::slug($title),
            'type' => fake()->randomElement(['Academic Project', 'Personal Project', 'Commercial Project']),
            'category' => fake()->randomElement(['Full-Stack Development', 'Frontend Development', 'Backend Development']),
            'image' => null,
            'gallery' => [],
            'video' => null,
            'start_period' => fake()->date(),
            'finish_period' => fake()->date(),
            'status' => fake()->randomElement(ProjectStatus::cases())->value,
            'description' => fake()->paragraph(),
            'delay_reason' => null,
            'resume_date' => null,
            'tags' => [fake()->word(), fake()->word()],
            'technology' => ['PHP', 'Laravel'],
            'source_code' => fake()->url(),
            'demo_url' => fake()->url(),
            'is_private' => false,
        ];
    }
}
