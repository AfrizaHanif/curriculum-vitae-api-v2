<?php

namespace App\Enums;

enum SkillType: string
{
    case Frontend = 'frontend';
    case Backend = 'backend';
    case Fullstack = 'fullstack';
    case Database = 'database';
    case Devops = 'devops';
    case Tool = 'tool';
    case SoftSkill = 'soft_skill';

    public function label(): string
    {
        return match ($this) {
            self::Frontend => 'Frontend',
            self::Backend => 'Backend',
            self::Fullstack => 'Full-Stack',
            self::Database => 'Database',
            self::Devops => 'DevOps',
            self::Tool => 'Tools',
            self::SoftSkill => 'Soft Skills',
        };
    }
}
