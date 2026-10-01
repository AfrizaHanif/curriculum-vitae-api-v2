<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planning = 'planning';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Live = 'live';
    case OnHold = 'on_hold';

    public function label(): string
    {
        return match ($this) {
            self::Planning => 'Planning',
            self::Ongoing => 'Ongoing',
            self::Completed => 'Completed',
            self::Live => 'Live',
            self::OnHold => 'On Hold',
        };
    }
}
