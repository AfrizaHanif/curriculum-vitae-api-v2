<?php

namespace App\Enums;

enum EducationStatus: string
{
    case Enrolled = 'enrolled';
    case Graduated = 'graduated';
    case DroppedOut = 'dropped_out';
    case OnLeave = 'on_leave';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Enrolled => 'Enrolled',
            self::Graduated => 'Graduated',
            self::DroppedOut => 'Dropped Out',
            self::OnLeave => 'On Leave',
            self::Withdrawn => 'Withdrawn',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::Enrolled => [
                'id' => 'Sedang Menempuh',
                'en' => 'Enrolled',
            ],
            self::Graduated => [
                'id' => 'Lulus',
                'en' => 'Graduated',
            ],
            self::DroppedOut => [
                'id' => 'Dikeluarkan / Drop Out',
                'en' => 'Dropped Out',
            ],
            self::OnLeave => [
                'id' => 'Cuti Akademik',
                'en' => 'On Leave',
            ],
            self::Withdrawn => [
                'id' => 'Mengundurkan Diri',
                'en' => 'Withdrawn',
            ],
        };
    }
}
