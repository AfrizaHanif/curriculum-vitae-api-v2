<?php

namespace Database\Factories;

use App\Enums\SkillType;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Skill>
     */
    protected $model = Skill::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::upper(Str::random(9)),
            'profile_id' => Profile::factory(),
            'name' => fake()->word(),
            'type' => fake()->randomElement(SkillType::cases())->value,
            'display_order' => fake()->numberBetween(0, 100),
        ];
    }
}
