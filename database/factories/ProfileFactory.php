<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::upper(Str::random(9)),
            'user_id' => User::factory(),
            'fullname' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'current_city' => fake()->city(),
            'current_province' => fake()->randomElement(['East Java', 'Central Java', 'West Java']),
            'email' => fake()->unique()->safeEmail(),
            'birthday' => fake()->date(),
            'tagline' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'philosophy' => fake()->paragraph(),
            'status' => 'Test Status',
            'casual_photo' => null,
            'formal_photo' => null,
            'setup_image' => null,
            'resume' => null,
        ];
    }
}
