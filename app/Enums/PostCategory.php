<?php

namespace App\Enums;

enum PostCategory: string
{
    case Announcement = 'announcement';
    case Tutorial = 'tutorial';
    case Tech = 'tech';
    case Career = 'career';

    public function label(): string
    {
        return match ($this) {
            self::Announcement => 'Pengumuman',
            self::Tutorial => 'Tutorial',
            self::Tech => 'Teknologi',
            self::Career => 'Karir',
        };
    }

    /** @return array{id: string, en: string} */
    public function labels(): array
    {
        return match ($this) {
            self::Announcement => ['id' => 'Pengumuman', 'en' => 'Announcement'],
            self::Tutorial => ['id' => 'Tutorial', 'en' => 'Tutorial'],
            self::Tech => ['id' => 'Teknologi', 'en' => 'Tech'],
            self::Career => ['id' => 'Karir', 'en' => 'Career'],
        };
    }
}
