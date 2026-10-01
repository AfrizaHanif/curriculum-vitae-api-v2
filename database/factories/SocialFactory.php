<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Social;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Social>
 */
class SocialFactory extends Factory
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
            'name' => fake()->randomElement(['GitHub', 'LinkedIn', 'Twitter', 'Instagram']),
            'url' => fake()->url(),
            'icon' => fake()->randomElement(['github', 'linkedin', 'twitter', 'instagram']),
        ];
    }
}
