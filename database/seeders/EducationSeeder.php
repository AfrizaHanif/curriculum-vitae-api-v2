<?php

namespace Database\Seeders;

use App\Enums\EducationStatus;
use App\Enums\EducationType;
use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $educations = [
            [
                'id' => 'EDU-001',
                'profile_id' => $user->profile->id,
                'type' => EducationType::Formal->value,
                'institution' => 'Universitas Dinamika',
                'address' => 'Jl. Raya Kedung Baruk No.98, Kedung Baruk, Kec. Rungkut, Surabaya, Jawa Timur 60298',
                'degree' => 'S1',
                'major' => 'Sistem Informasi',
                'gpa' => 3.26,
                'status' => EducationStatus::Graduated->value,
                'description' => [
                    'id' => 'Fokus pada pengembangan perangkat lunak dan analisis sistem, dengan proyek akhir membangun sistem pendukung keputusan untuk evaluasi kinerja karyawan.',
                    'en' => 'Focus on software development and system analysis, with the final project building a decision support system for employee performance evaluation.',
                ],
                'start_period' => '2021-08-01',
                'finish_period' => '2025-03-01',
                'latitude' => -7.311713,
                'longitude' => 112.782191,
            ],
        ];

        foreach ($educations as $education) {
            Education::updateOrCreate(['id' => $education['id']], $education);
        }
    }
}
