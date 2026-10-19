<?php

namespace App\Enums;

enum ExperienceType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Freelance = 'freelance';
    case Contract = 'contract';
    case Internship = 'internship';
    case Volunteer = 'volunteer';
    case SelfEmployed = 'self_employed';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'Full Time',
            self::PartTime => 'Part Time',
            self::Freelance => 'Freelance',
            self::Contract => 'Contract',
            self::Internship => 'Internship',
            self::Volunteer => 'Volunteer',
            self::SelfEmployed => 'Self-Employed',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::FullTime => [
                'id' => 'Penuh Waktu',
                'en' => 'Full Time',
            ],
            self::PartTime => [
                'id' => 'Paruh Waktu',
                'en' => 'Part Time',
            ],
            self::Freelance => [
                'id' => 'Pekerja Lepas',
                'en' => 'Freelance',
            ],
            self::Contract => [
                'id' => 'Kontrak',
                'en' => 'Contract',
            ],
            self::Internship => [
                'id' => 'Magang',
                'en' => 'Internship',
            ],
            self::Volunteer => [
                'id' => 'Sukarelawan',
                'en' => 'Volunteer',
            ],
            self::SelfEmployed => [
                'id' => 'Wirausaha / Mandiri',
                'en' => 'Self-Employed',
            ],
        };
    }
}
