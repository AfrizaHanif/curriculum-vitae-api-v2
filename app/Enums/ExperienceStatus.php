<?php

namespace App\Enums;

enum ExperienceStatus: string
{
    case Active = 'active';
    case Finished = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Currently Employed',
            self::Finished => 'Finished',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::Active => [
                'id' => 'Masih Bekerja',
                'en' => 'Currently Employed',
            ],
            self::Finished => [
                'id' => 'Selesai',
                'en' => 'Finished',
            ],
        };
    }
}
