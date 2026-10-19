<?php

namespace App\Enums;

enum EducationType: string
{
    case Formal = 'formal';
    case NonFormal = 'non_formal';
    case Informal = 'informal';

    public function label(): string
    {
        return match ($this) {
            self::Formal => 'Formal',
            self::NonFormal => 'Non-Formal',
            self::Informal => 'Informal',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::Formal => [
                'id' => 'Formal',
                'en' => 'Formal',
            ],
            self::NonFormal => [
                'id' => 'Non-Formal (Kursus / Pelatihan)',
                'en' => 'Non-Formal',
            ],
            self::Informal => [
                'id' => 'Informal (Otodidak)',
                'en' => 'Informal (Self-taught)',
            ],
        };
    }
}
