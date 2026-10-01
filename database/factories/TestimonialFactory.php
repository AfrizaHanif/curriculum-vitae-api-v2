<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
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
            'name' => fake()->name(),
            'role' => fake()->jobTitle().' at '.fake()->company(),
            'content' => fake()->paragraph(),
        ];
    }
}
