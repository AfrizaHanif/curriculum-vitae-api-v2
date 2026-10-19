<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planning = 'planning';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Live = 'live';
    case OnHold = 'on_hold';
    case Archived = 'archived';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planning => 'Planning',
            self::Ongoing => 'Ongoing',
            self::Completed => 'Completed',
            self::Live => 'Live',
            self::OnHold => 'On Hold',
            self::Archived => 'Archived',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::Planning => [
                'id' => 'Perencanaan',
                'en' => 'Planning',
            ],
            self::Ongoing => [
                'id' => 'Sedang Berjalan',
                'en' => 'Ongoing',
            ],
            self::Completed => [
                'id' => 'Selesai',
                'en' => 'Completed',
            ],
            self::Live => [
                'id' => 'Rilis / Aktif',
                'en' => 'Live',
            ],
            self::OnHold => [
                'id' => 'Ditunda',
                'en' => 'On Hold',
            ],
            self::Archived => [
                'id' => 'Diarsipkan',
                'en' => 'Archived',
            ],
            self::Cancelled => [
                'id' => 'Dibatalkan',
                'en' => 'Cancelled',
            ],
        };
    }
}
