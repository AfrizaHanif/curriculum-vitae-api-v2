<?php

namespace App\Enums;

enum SkillType: string
{
    case Frontend = 'frontend';
    case Backend = 'backend';
    case Fullstack = 'fullstack';
    case Mobile = 'mobile';
    case Database = 'database';
    case Devops = 'devops';
    case Tool = 'tool';
    case Design = 'design';
    case AiMl = 'ai_ml';
    case Testing = 'testing';
    case SoftSkill = 'soft_skill';

    public function label(): string
    {
        return match ($this) {
            self::Frontend => 'Frontend',
            self::Backend => 'Backend',
            self::Fullstack => 'Full-Stack',
            self::Mobile => 'Mobile',
            self::Database => 'Database',
            self::Devops => 'DevOps',
            self::Tool => 'Tools',
            self::Design => 'UI/UX & Design',
            self::AiMl => 'AI / ML',
            self::Testing => 'QA & Testing',
            self::SoftSkill => 'Soft Skills',
        };
    }

    /**
     * @return array{id: string, en: string}
     */
    public function labels(): array
    {
        return match ($this) {
            self::Frontend => [
                'id' => 'Frontend',
                'en' => 'Frontend',
            ],
            self::Backend => [
                'id' => 'Backend',
                'en' => 'Backend',
            ],
            self::Fullstack => [
                'id' => 'Full-Stack',
                'en' => 'Full-Stack',
            ],
            self::Mobile => [
                'id' => 'Aplikasi Seluler',
                'en' => 'Mobile',
            ],
            self::Database => [
                'id' => 'Basis Data',
                'en' => 'Database',
            ],
            self::Devops => [
                'id' => 'DevOps',
                'en' => 'DevOps',
            ],
            self::Tool => [
                'id' => 'Alat & Perkakas',
                'en' => 'Tools',
            ],
            self::Design => [
                'id' => 'Desain & UI/UX',
                'en' => 'UI/UX & Design',
            ],
            self::AiMl => [
                'id' => 'Kecerdasan Buatan (AI/ML)',
                'en' => 'AI / ML',
            ],
            self::Testing => [
                'id' => 'Pengujian & QA',
                'en' => 'QA & Testing',
            ],
            self::SoftSkill => [
                'id' => 'Keterampilan Interpersonal',
                'en' => 'Soft Skills',
            ],
        };
    }
}
