<?php

namespace App\Enums;

enum CertificateType: string
{
    case Course = 'course';
    case Certification = 'certification';
    case Training = 'training';
    case Internship = 'internship';
    case Workshop = 'workshop';
    case Award = 'award';
    case Assessment = 'assessment';
    case License = 'license';

    public function label(): string
    {
        return match ($this) {
            self::Course => 'Kursus',
            self::Certification => 'Sertifikasi',
            self::Training => 'Pelatihan',
            self::Internship => 'Magang',
            self::Workshop => 'Workshop',
            self::Award => 'Penghargaan',
            self::Assessment => 'Penilaian',
            self::License => 'Lisensi',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::Course => [
                'id' => 'Kursus',
                'en' => 'Course',
            ],
            self::Certification => [
                'id' => 'Sertifikasi',
                'en' => 'Certification',
            ],
            self::Training => [
                'id' => 'Pelatihan',
                'en' => 'Training',
            ],
            self::Internship => [
                'id' => 'Magang',
                'en' => 'Internship',
            ],
            self::Workshop => [
                'id' => 'Workshop',
                'en' => 'Workshop',
            ],
            self::Award => [
                'id' => 'Penghargaan',
                'en' => 'Award',
            ],
            self::Assessment => [
                'id' => 'Penilaian',
                'en' => 'Assessment',
            ],
            self::License => [
                'id' => 'Lisensi',
                'en' => 'License',
            ],
        };
    }
}
