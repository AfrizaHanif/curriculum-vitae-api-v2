<?php

namespace App\Enums;

enum EducationStatus: string
{
    case Enrolled = 'enrolled';
    case Graduated = 'graduated';
    case DroppedOut = 'dropped_out';

    public function label(): string
    {
        return match ($this) {
            self::Enrolled => 'Enrolled',
            self::Graduated => 'Graduated',
            self::DroppedOut => 'Dropped Out',
        };
    }
}
