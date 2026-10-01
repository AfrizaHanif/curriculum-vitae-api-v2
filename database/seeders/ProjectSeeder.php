<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Feature;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use SeedsLocalFiles;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $projects = [
            [
                'id' => 'PRJ-001',
                'profile_id' => $user->profile->id,
                'title' => 'Web Delta Sari (Indah dan Baru)',
                'slug' => 'web-delta-sari-indah-dan-baru',
                'type' => 'Personal Project',
                'category' => 'Full-Stack Development',
                'image' => '/images/projects/PRJ-001.jpg',
                'start_period' => '2025-03-01',
                'status' => ProjectStatus::Ongoing->value,
                'description' => [
                    'id' => 'Proyek aplikasi web untuk manajemen informasi dan administrasi digital kawasan perumahan Delta Sari Indah dan Delta Sari Baru.',
                    'en' => 'Web application project for managing residential information and digital administrative services for the Delta Sari housing complex.',
                ],
                'tags' => ['Estate'],
                'technology' => ['Laravel', 'PHP'],
                // 'source_code' => '',
                'features' => [
                    [
                        'id' => 'FEA-007',
                        'title' => 'Desain Informatif dan Interaktif',
                        'progress' => 75,
                    ],
                ],
                'demo_url' => 'https://delta-sari.pages.dev',
                'is_private' => true,
            ],
            [
                'id' => 'PRJ-002',
                'profile_id' => $user->profile->id,
                'title' => 'Curriculum Vitae API',
                'slug' => 'curriculum-vitae-api',
                'type' => 'Personal Project',
                'category' => 'Backend Development',
                'image' => '/images/projects/PRJ-002.jpg',
                'start_period' => '2026-03-10',
                'finish_period' => '2026-10-01',
                'status' => ProjectStatus::Completed->value,
                'description' => [
                    'id' => 'Backend engine RESTful API berbasis Laravel Sanctum untuk menyediakan dan mengelola dataset portofolio secara terstruktur dan aman.',
                    'en' => 'RESTful API backend engine built with Laravel Sanctum to manage and deliver curriculum vitae portfolio datasets securely.',
                ],
                'tags' => ['API', 'Backend', 'RESTful'],
                'technology' => ['Laravel', 'MySQL', 'PHP'],
                'source_code' => 'https://github.com/AfrizaHanif/curriculum-vitae-api-v2',
                'is_private' => false,
                'features' => [
                    [
                        'id' => 'FEA-008',
                        'title' => 'Autentikasi Laravel Sanctum',
                        'description' => 'Implementasi sistem autentikasi menggunakan Laravel Sanctum untuk keamanan aplikasi.',
                        'progress' => 100,
                    ],
                    [
                        'id' => 'FEA-009',
                        'title' => 'Endpoint Data API',
                        'description' => 'Implementasi endpoint untuk mengakses dan memanipulasi data melalui API.',
                        'progress' => 100,
                    ],
                ],
            ],
            [
                'id' => 'PRJ-003',
                'profile_id' => $user->profile->id,
                'title' => 'Interactive Curriculum Vitae Web',
                'slug' => 'interactive-curriculum-vitae-web',
                'type' => 'Personal Project',
                'category' => 'Front-End Development',
                'image' => '/images/projects/PRJ-003.jpg',
                'start_period' => '2026-03-22',
                'status' => ProjectStatus::Ongoing->value,
                'description' => [
                    'id' => 'Aplikasi portofolio interaktif modern yang dibangun dengan Next.js, React, dan Bootstrap dengan dukungan bilingual dan navigasi responsif.',
                    'en' => 'Modern, interactive portfolio application developed with Next.js, React, and Bootstrap for dynamic showcase and dual-language CV exploration.',
                ],
                'tags' => ['React', 'Next.js', 'Portfolio'],
                'technology' => ['Next.js', 'React', 'TypeScript', 'Bootstrap'],
                'source_code' => 'https://github.com/AfrizaHanif/curriculum-vitae-react-v2',
                'features' => [
                    [
                        'id' => 'FEA-010',
                        'title' => 'Integrasi Dinamis & Multibahasa',
                        'description' => 'Implementasi sistem switching bahasa, navigasi halus, serta konsumsi data API terpusat.',
                        'progress' => 85,
                    ],
                    [
                        'id' => 'FEA-011',
                        'title' => 'Desain Responsif & Interaktif',
                        'description' => 'Antarmuka pengguna yang responsif, animasi halus, dan navigasi intuitif untuk pengalaman pengguna yang optimal di berbagai perangkat.',
                        'progress' => 90,
                    ],
                    [
                        'id' => 'FEA-012',
                        'title' => 'Konsumsi Data Terpusat',
                        'description' => 'Integrasi dengan CV API untuk menyediakan konten dinamis dan terstruktur di seluruh halaman web.',
                        'progress' => 100,
                    ],
                ],
            ],
        ];

        foreach ($projects as $data) {
            // Separate features from the project attributes
            $features = $data['features'] ?? [];
            unset($data['features']);

            // Create or update the Project
            $project = Project::updateOrCreate(['id' => $data['id']], $data);
            $this->seedFile($project->image ?? null);

            // Save each feature through the polymorphic relationship
            foreach ($features as $feature) {
                if (empty($feature['id'])) {
                    $feature['id'] = (new Feature)->generateCustomId();
                }

                $project->features()->updateOrCreate(
                    ['id' => $feature['id']],
                    $feature
                );
            }
        }
    }
}
