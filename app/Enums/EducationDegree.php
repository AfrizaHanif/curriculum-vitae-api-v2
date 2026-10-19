<?php

namespace App\Enums;

enum EducationDegree: string
{
    case HighSchool = 'high_school';
    case Associate = 'associate';
    case Bachelor = 'bachelor';
    case AppliedBachelor = 'applied_bachelor';
    case Master = 'master';
    case Doctorate = 'doctorate';

    public function label(): string
    {
        return match ($this) {
            self::HighSchool => 'High School',
            self::Associate => 'Associate Degree',
            self::AppliedBachelor => "Applied Bachelor's Degree",
            self::Bachelor => "Bachelor's Degree",
            self::Master => "Master's Degree",
            self::Doctorate => 'Doctoral Degree',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::HighSchool => [
                'id' => 'SMA / SMK',
                'en' => 'High School',
            ],
            self::Associate => [
                'id' => 'Diploma (D1 - D3)',
                'en' => 'Associate Degree',
            ],
            self::AppliedBachelor => [
                'id' => 'Sarjana Terapan (D4)',
                'en' => "Applied Bachelor's Degree",
            ],
            self::Bachelor => [
                'id' => 'Sarjana (S1)',
                'en' => "Bachelor's Degree",
            ],
            self::Master => [
                'id' => 'Magister (S2)',
                'en' => "Master's Degree",
            ],
            self::Doctorate => [
                'id' => 'Doktor (S3)',
                'en' => 'Doctoral Degree (Ph.D)',
            ],
        };
    }
}
