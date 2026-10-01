<?php

namespace Database\Seeders;

use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $experiences = [
            [
                'id' => 'EXP-001',
                'profile_id' => $user->profile->id,
                'type' => ExperienceType::Internship->value,
                'title' => 'Intern Web Developer',
                'company' => 'BPS Jawa Timur',
                'description' => [
                    'id' => [
                        'Membantu dalam desain dan pengembangan aplikasi web manajemen perpustakaan menggunakan Laravel (PHP framework) dan MySQL untuk fungsionalitas backend.',
                        'Mengimplementasikan operasi CRUD untuk data buku dan pengguna, memastikan manajemen basis data yang terstruktur.',
                        'Berkolaborasi dalam pengembangan frontend dengan Bootstrap, menciptakan layout responsif dan antarmuka yang ramah pengguna.',
                        'Mendapatkan pengalaman langsung dalam arsitektur MVC, integrasi basis data, serta workflow deployment aplikasi web.',
                        'Bekerja dalam lingkungan tim, menerapkan keterampilan pemecahan masalah (problem solving) dan mempelajari praktik terbaik industri untuk pengembangan full-stack.',
                    ],
                    'en' => [
                        'Assisted in the design and development of a library management web application using Laravel (PHP framework) and MySQL for backend functionality.',
                        'Implemented CRUD operations for book records and user data, ensuring structured database management.',
                        'Collaborated on frontend development with Bootstrap, creating responsive layouts and user-friendly interfaces.',
                        'Gained hands-on experience in MVC architecture, database integration, and web application deployment workflows.',
                        'Worked in a team environment, applying problem-solving skills and learning industry best practices for full-stack development.',
                    ],
                ],
                'address' => 'Jl. Raya Kendangsari Industri No.43-44, Kendangsari, Kec. Tenggilis Mejoyo, Surabaya, Jawa Timur 60292',
                'status' => ExperienceStatus::Finished->value,
                'start_period' => '2023-01-01',
                'finish_period' => '2023-03-01',
                'latitude' => -7.328969,
                'longitude' => 112.745766,
            ],
            [
                'id' => 'EXP-002',
                'profile_id' => $user->profile->id,
                'type' => ExperienceType::Internship->value,
                'title' => 'Intern Frontend Developer',
                'company' => 'Kominfo Jawa Timur',
                'description' => [
                    'id' => [
                        'Berkontribusi dalam debugging dan troubleshooting masalah frontend pada aplikasi web, meningkatkan stabilitas dan performa.',
                        'Menerapkan fundamental JavaScript untuk mengidentifikasi dan menyelesaikan inkonsistensi UI/UX.',
                        'Bekerja sama dengan tim untuk menguji dan memvalidasi perbaikan bug, memastikan interaksi pengguna lebih lancar.',
                        'Memperkuat pengetahuan tentang framework frontend melalui pembelajaran mandiri, menyelesaikan kursus online di React, dan JavaScript modern (Udemy).',
                        'Mendapatkan pengalaman praktis dalam workflow pengembangan web, version control, serta pemecahan masalah (problem solving) di lingkungan profesional.',
                    ],
                    'en' => [
                        'Contributed to debugging and troubleshooting frontend issues in a web application, improving stability and performance.',
                        'Applied JavaScript fundamentals to identify and resolve UI/UX inconsistencies.',
                        'Collaborated with team members to test and validate bug fixes, ensuring smoother user interactions.',
                        'Strengthened knowledge of frontend frameworks through self-study, completing online courses in React, and modern JavaScript (Udemy).',
                        'Gained practical exposure to web development workflows, version control, and problem-solving in a professional environment.',
                    ],
                ],
                'address' => 'Jl. Ahmad Yani No.242-244, Gayungan, Kec. Gayungan, Surabaya, Jawa Timur 60235',
                'status' => ExperienceStatus::Finished->value,
                'start_period' => '2025-09-01',
                'finish_period' => '2026-01-31',
                'latitude' => -7.336187,
                'longitude' => 112.729142,
            ],
        ];

        foreach ($experiences as $experience) {
            Experience::updateOrCreate(['id' => $experience['id']], $experience);
        }
    }
}
