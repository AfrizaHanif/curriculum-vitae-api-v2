<?php

namespace App\Enums;

enum EducationType: string
{
    case Formal = 'formal';
    case NonFormal = 'non_formal';

    public function label(): string
    {
        return match ($this) {
            self::Formal => 'Formal',
            self::NonFormal => 'Non-Formal',
        };
    }
}
