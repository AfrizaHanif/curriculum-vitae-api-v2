<?php

use App\Enums\EducationStatus;
use App\Enums\EducationType;
use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
use App\Enums\ProjectStatus;
use App\Enums\SkillType;

test('education status enum has valid values and labels', function () {
    expect(EducationStatus::Enrolled->value)->toBe('enrolled')
        ->and(EducationStatus::Enrolled->label())->toBe('Enrolled')
        ->and(EducationStatus::Graduated->value)->toBe('graduated')
        ->and(EducationStatus::Graduated->label())->toBe('Graduated')
        ->and(EducationStatus::DroppedOut->value)->toBe('dropped_out')
        ->and(EducationStatus::DroppedOut->label())->toBe('Dropped Out');
});

test('education type enum has valid values and labels', function () {
    expect(EducationType::Formal->value)->toBe('formal')
        ->and(EducationType::Formal->label())->toBe('Formal')
        ->and(EducationType::NonFormal->value)->toBe('non_formal')
        ->and(EducationType::NonFormal->label())->toBe('Non-Formal');
});

test('experience status enum has valid values', function () {
    expect(ExperienceStatus::Active->value)->toBe('active')
        ->and(ExperienceStatus::Finished->value)->toBe('finished');
});

test('experience type enum has valid values and labels', function () {
    expect(ExperienceType::FullTime->value)->toBe('full_time')
        ->and(ExperienceType::FullTime->label())->toBe('Full Time')
        ->and(ExperienceType::Freelance->value)->toBe('freelance')
        ->and(ExperienceType::Freelance->label())->toBe('Freelance')
        ->and(ExperienceType::Internship->value)->toBe('internship')
        ->and(ExperienceType::Internship->label())->toBe('Internship');
});

test('project status enum has valid values and labels', function () {
    expect(ProjectStatus::Planning->value)->toBe('planning')
        ->and(ProjectStatus::Planning->label())->toBe('Planning')
        ->and(ProjectStatus::Ongoing->value)->toBe('ongoing')
        ->and(ProjectStatus::Ongoing->label())->toBe('Ongoing')
        ->and(ProjectStatus::Completed->value)->toBe('completed')
        ->and(ProjectStatus::Completed->label())->toBe('Completed')
        ->and(ProjectStatus::Live->value)->toBe('live')
        ->and(ProjectStatus::Live->label())->toBe('Live')
        ->and(ProjectStatus::OnHold->value)->toBe('on_hold')
        ->and(ProjectStatus::OnHold->label())->toBe('On Hold');
});

test('skill type enum has valid values and labels', function () {
    expect(SkillType::Frontend->value)->toBe('frontend')
        ->and(SkillType::Frontend->label())->toBe('Frontend')
        ->and(SkillType::Backend->value)->toBe('backend')
        ->and(SkillType::Backend->label())->toBe('Backend')
        ->and(SkillType::Fullstack->value)->toBe('fullstack')
        ->and(SkillType::Fullstack->label())->toBe('Full-Stack')
        ->and(SkillType::Database->value)->toBe('database')
        ->and(SkillType::Database->label())->toBe('Database')
        ->and(SkillType::Devops->value)->toBe('devops')
        ->and(SkillType::Devops->label())->toBe('DevOps')
        ->and(SkillType::Tool->value)->toBe('tool')
        ->and(SkillType::Tool->label())->toBe('Tools')
        ->and(SkillType::SoftSkill->value)->toBe('soft_skill')
        ->and(SkillType::SoftSkill->label())->toBe('Soft Skills');
});
