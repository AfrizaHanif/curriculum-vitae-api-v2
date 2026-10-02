<?php

namespace Database\Seeders;

use App\Enums\SkillType;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrFail();

        $skills = [
            [
                'id' => 'SKI-001',
                'name' => 'Bootstrap',
                'type' => SkillType::Frontend->value,
                'display_order' => 4,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-002',
                'name' => 'Laravel',
                'type' => SkillType::Backend->value,
                'display_order' => 1,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-003',
                'name' => 'MySQL',
                'type' => SkillType::Database->value,
                'display_order' => 11,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-004',
                'name' => 'PHP',
                'type' => SkillType::Backend->value,
                'display_order' => 9,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-005',
                'name' => 'HTML',
                'type' => SkillType::Frontend->value,
                'display_order' => 5,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-006',
                'name' => 'TypeScript',
                'type' => SkillType::Frontend->value,
                'display_order' => 8,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-007',
                'name' => 'JavaScript',
                'type' => SkillType::Frontend->value,
                'display_order' => 7,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-008',
                'name' => 'React',
                'type' => SkillType::Frontend->value,
                'display_order' => 2,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-009',
                'name' => 'Git',
                'type' => SkillType::Tool->value,
                'display_order' => 12,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-010',
                'name' => 'GitHub',
                'type' => SkillType::Tool->value,
                'display_order' => 13,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-011',
                'name' => 'Postman',
                'type' => SkillType::Tool->value,
                'display_order' => 14,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-012',
                'name' => 'AI-Assisted Development',
                'type' => SkillType::Tool->value,
                'display_order' => 15,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-013',
                'name' => 'CSS',
                'type' => SkillType::Frontend->value,
                'display_order' => 6,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-014',
                'name' => 'Rest API',
                'type' => SkillType::Backend->value,
                'display_order' => 10,
                'profile_id' => $user->profile->id,
            ],
            [
                'id' => 'SKI-015',
                'name' => 'NextJS',
                'type' => SkillType::Frontend->value,
                'display_order' => 3,
                'profile_id' => $user->profile->id,
            ],
        ];

        // foreach ($skills as $skill) {
        //     // Skill::updateOrCreate(['id' => $skill['id']], $skill);
        // }

        Skill::upsert(
            $skills,
            uniqueBy: ['id'],
            update: ['name', 'type', 'display_order', 'profile_id']
        );
    }
}
