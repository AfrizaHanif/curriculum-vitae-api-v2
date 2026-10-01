<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
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
            'title' => fake()->sentence(3),
            'type' => fake()->randomElement(['Kursus', 'Sertifikasi']),
            'issuer' => fake()->company(),
            'issued_date' => fake()->date(),
            'credential_url' => fake()->url(),
            'file' => null,
        ];
    }
}
