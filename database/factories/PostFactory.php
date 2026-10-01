<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'profile_id' => Profile::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => fake()->randomElement(['Tech', 'Tutorial', 'Career']),
            'author' => fake()->name(),
            'tags' => [fake()->word(), fake()->word()],
            'summary' => fake()->sentence(),
            'image' => null,
            'content' => fake()->paragraphs(3, true),
            'is_featured' => fake()->boolean(20),
        ];
    }
}
