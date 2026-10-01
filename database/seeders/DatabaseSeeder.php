<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fetch credentials from env or use defaults
        $adminEmail = config('auth.admin_email', 'admin@example.com');
        $adminPassword = config('auth.admin_password', 'password');

        if (app()->isProduction() && $adminPassword === 'password') {
            throw new \RuntimeException('SEEDER_ADMIN_PASSWORD must be configured with a secure password in production environment.');
        }

        // Ensure the central test user exists for dependent seeders
        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Administrator',
                'password' => Hash::make($adminPassword),
                'timezone' => 'Asia/Jakarta',
            ]
        );

        $this->call(
            [
                // Profile Module
                ProfileSeeder::class,
                SkillSeeder::class,
                HobbySeeder::class,
                SocialSeeder::class,

                // Resume Module
                SetupSeeder::class,
                EducationSeeder::class,
                ExperienceSeeder::class,
                CertificateSeeder::class,

                // Portfolio and Project Module
                PortfolioSeeder::class,
                ProjectSeeder::class,
                CaseStudySeeder::class,
                FeatureSeeder::class,

                // Expertise Module
                ExpertiseSeeder::class,

                // Post Module
                PostSeeder::class,

                // Testimonial Module
                TestimonialSeeder::class,
            ]
        );
    }
}
