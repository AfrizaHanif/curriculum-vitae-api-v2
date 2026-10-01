<?php

namespace App\Enums;

enum ExperienceType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Freelance = 'freelance';
    case Contract = 'contract';
    case Internship = 'internship';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'Full Time',
            self::PartTime => 'Part Time',
            self::Freelance => 'Freelance',
            self::Contract => 'Contract',
            self::Internship => 'Internship',
        };
    }
}
