<?php

namespace App\Enums;

enum EducationDegree: string
{
    case HighSchool = 'high_school';
    case Associate = 'associate';
    case Bachelor = 'bachelor';
    case Master = 'master';
    case Doctorate = 'doctorate';

    public function label(): string
    {
        return match ($this) {
            self::HighSchool => 'High School',
            self::Associate => 'Associate Degree',
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
                'id' => 'Diploma (D1 - D4)',
                'en' => 'Associate Degree',
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
