<?php

namespace App\Enums;

enum ProjectType: string
{
    case Academic = 'academic';
    case Personal = 'personal';
    case Commercial = 'commercial';
    case OpenSource = 'open_source';
    case Freelance = 'freelance';

    public function label(): string
    {
        return match ($this) {
            self::Academic => 'Proyek Akademik',
            self::Personal => 'Proyek Pribadi',
            self::Commercial => 'Proyek Komersial',
            self::OpenSource => 'Sumber Terbuka',
            self::Freelance => 'Pekerja Lepas',
        };
    }

    /** @return array{id: string, en: string} */
    public function labels(): array
    {
        return match ($this) {
            self::Academic => ['id' => 'Proyek Akademik', 'en' => 'Academic Project'],
            self::Personal => ['id' => 'Proyek Pribadi', 'en' => 'Personal Project'],
            self::Commercial => ['id' => 'Proyek Komersial', 'en' => 'Commercial Project'],
            self::OpenSource => ['id' => 'Sumber Terbuka', 'en' => 'Open Source Project'],
            self::Freelance => ['id' => 'Pekerja Lepas', 'en' => 'Freelance Project'],
        };
    }
}
