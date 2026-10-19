<?php

use App\Enums\EducationDegree;
use App\Enums\EducationStatus;
use App\Enums\EducationType;
use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
use App\Enums\ProjectStatus;
use App\Enums\SkillType;

test('education degree enum has valid values, labels, and localized labels', function () {
    expect(EducationDegree::NonDegree->value)->toBe('non_degree')
        ->and(EducationDegree::NonDegree->label())->toBe('Non-Degree')
        ->and(EducationDegree::NonDegree->labels())->toBe(['id' => 'Non-Gelar / Kursus', 'en' => 'Non-Degree / Certificate'])
        ->and(EducationDegree::HighSchool->value)->toBe('high_school')
        ->and(EducationDegree::HighSchool->label())->toBe('High School')
        ->and(EducationDegree::HighSchool->labels())->toBe(['id' => 'SMA / SMK', 'en' => 'High School'])
        ->and(EducationDegree::Associate->value)->toBe('associate')
        ->and(EducationDegree::Associate->label())->toBe('Associate Degree')
        ->and(EducationDegree::Associate->labels())->toBe(['id' => 'Diploma (D1 - D3)', 'en' => 'Associate Degree'])
        ->and(EducationDegree::Bachelor->value)->toBe('bachelor')
        ->and(EducationDegree::Bachelor->label())->toBe("Bachelor's Degree")
        ->and(EducationDegree::Bachelor->labels())->toBe(['id' => 'Sarjana (S1)', 'en' => "Bachelor's Degree"])
        ->and(EducationDegree::AppliedBachelor->value)->toBe('applied_bachelor')
        ->and(EducationDegree::AppliedBachelor->label())->toBe("Applied Bachelor's Degree")
        ->and(EducationDegree::AppliedBachelor->labels())->toBe(['id' => 'Sarjana Terapan (D4)', 'en' => "Applied Bachelor's Degree"])
        ->and(EducationDegree::Master->value)->toBe('master')
        ->and(EducationDegree::Master->label())->toBe("Master's Degree")
        ->and(EducationDegree::Master->labels())->toBe(['id' => 'Magister (S2)', 'en' => "Master's Degree"])
        ->and(EducationDegree::Doctorate->value)->toBe('doctorate')
        ->and(EducationDegree::Doctorate->label())->toBe('Doctoral Degree')
        ->and(EducationDegree::Doctorate->labels())->toBe(['id' => 'Doktor (S3)', 'en' => 'Doctoral Degree (Ph.D)']);
});

test('education status enum has valid values, labels, and localized labels', function () {
    expect(EducationStatus::Enrolled->value)->toBe('enrolled')
        ->and(EducationStatus::Enrolled->label())->toBe('Enrolled')
        ->and(EducationStatus::Enrolled->labels())->toBe(['id' => 'Sedang Menempuh', 'en' => 'Enrolled'])
        ->and(EducationStatus::Graduated->value)->toBe('graduated')
        ->and(EducationStatus::Graduated->label())->toBe('Graduated')
        ->and(EducationStatus::Graduated->labels())->toBe(['id' => 'Lulus', 'en' => 'Graduated'])
        ->and(EducationStatus::DroppedOut->value)->toBe('dropped_out')
        ->and(EducationStatus::DroppedOut->label())->toBe('Dropped Out')
        ->and(EducationStatus::DroppedOut->labels())->toBe(['id' => 'Dikeluarkan / Drop Out', 'en' => 'Dropped Out'])
        ->and(EducationStatus::OnLeave->value)->toBe('on_leave')
        ->and(EducationStatus::OnLeave->label())->toBe('On Leave')
        ->and(EducationStatus::OnLeave->labels())->toBe(['id' => 'Cuti Akademik', 'en' => 'On Leave'])
        ->and(EducationStatus::Withdrawn->value)->toBe('withdrawn')
        ->and(EducationStatus::Withdrawn->label())->toBe('Withdrawn')
        ->and(EducationStatus::Withdrawn->labels())->toBe(['id' => 'Mengundurkan Diri', 'en' => 'Withdrawn']);
});

test('education type enum has valid values, labels, and localized labels', function () {
    expect(EducationType::Formal->value)->toBe('formal')
        ->and(EducationType::Formal->label())->toBe('Formal')
        ->and(EducationType::Formal->labels())->toBe(['id' => 'Formal', 'en' => 'Formal'])
        ->and(EducationType::NonFormal->value)->toBe('non_formal')
        ->and(EducationType::NonFormal->label())->toBe('Non-Formal')
        ->and(EducationType::NonFormal->labels())->toBe(['id' => 'Non-Formal (Kursus / Pelatihan)', 'en' => 'Non-Formal'])
        ->and(EducationType::Informal->value)->toBe('informal')
        ->and(EducationType::Informal->label())->toBe('Informal')
        ->and(EducationType::Informal->labels())->toBe(['id' => 'Informal (Otodidak)', 'en' => 'Informal (Self-taught)']);
});

test('experience status enum has valid values, labels, and localized labels', function () {
    expect(ExperienceStatus::Active->value)->toBe('active')
        ->and(ExperienceStatus::Active->label())->toBe('Currently Employed')
        ->and(ExperienceStatus::Active->labels())->toBe(['id' => 'Masih Bekerja', 'en' => 'Currently Employed'])
        ->and(ExperienceStatus::Finished->value)->toBe('finished')
        ->and(ExperienceStatus::Finished->label())->toBe('Finished')
        ->and(ExperienceStatus::Finished->labels())->toBe(['id' => 'Selesai', 'en' => 'Finished']);
});

test('experience type enum has valid values, labels, and localized labels', function () {
    expect(ExperienceType::FullTime->value)->toBe('full_time')
        ->and(ExperienceType::FullTime->label())->toBe('Full Time')
        ->and(ExperienceType::FullTime->labels())->toBe(['id' => 'Penuh Waktu', 'en' => 'Full Time'])
        ->and(ExperienceType::PartTime->value)->toBe('part_time')
        ->and(ExperienceType::PartTime->label())->toBe('Part Time')
        ->and(ExperienceType::PartTime->labels())->toBe(['id' => 'Paruh Waktu', 'en' => 'Part Time'])
        ->and(ExperienceType::Freelance->value)->toBe('freelance')
        ->and(ExperienceType::Freelance->label())->toBe('Freelance')
        ->and(ExperienceType::Freelance->labels())->toBe(['id' => 'Pekerja Lepas', 'en' => 'Freelance'])
        ->and(ExperienceType::Contract->value)->toBe('contract')
        ->and(ExperienceType::Contract->label())->toBe('Contract')
        ->and(ExperienceType::Contract->labels())->toBe(['id' => 'Kontrak', 'en' => 'Contract'])
        ->and(ExperienceType::Internship->value)->toBe('internship')
        ->and(ExperienceType::Internship->label())->toBe('Internship')
        ->and(ExperienceType::Internship->labels())->toBe(['id' => 'Magang', 'en' => 'Internship'])
        ->and(ExperienceType::Volunteer->value)->toBe('volunteer')
        ->and(ExperienceType::Volunteer->label())->toBe('Volunteer')
        ->and(ExperienceType::Volunteer->labels())->toBe(['id' => 'Sukarelawan', 'en' => 'Volunteer'])
        ->and(ExperienceType::SelfEmployed->value)->toBe('self_employed')
        ->and(ExperienceType::SelfEmployed->label())->toBe('Self-Employed')
        ->and(ExperienceType::SelfEmployed->labels())->toBe(['id' => 'Wirausaha / Mandiri', 'en' => 'Self-Employed']);
});

test('project status enum has valid values, labels, and localized labels', function () {
    expect(ProjectStatus::Planning->value)->toBe('planning')
        ->and(ProjectStatus::Planning->label())->toBe('Planning')
        ->and(ProjectStatus::Planning->labels())->toBe(['id' => 'Perencanaan', 'en' => 'Planning'])
        ->and(ProjectStatus::Ongoing->value)->toBe('ongoing')
        ->and(ProjectStatus::Ongoing->label())->toBe('Ongoing')
        ->and(ProjectStatus::Ongoing->labels())->toBe(['id' => 'Sedang Berjalan', 'en' => 'Ongoing'])
        ->and(ProjectStatus::Completed->value)->toBe('completed')
        ->and(ProjectStatus::Completed->label())->toBe('Completed')
        ->and(ProjectStatus::Completed->labels())->toBe(['id' => 'Selesai', 'en' => 'Completed'])
        ->and(ProjectStatus::Live->value)->toBe('live')
        ->and(ProjectStatus::Live->label())->toBe('Live')
        ->and(ProjectStatus::Live->labels())->toBe(['id' => 'Rilis / Aktif', 'en' => 'Live'])
        ->and(ProjectStatus::OnHold->value)->toBe('on_hold')
        ->and(ProjectStatus::OnHold->label())->toBe('On Hold')
        ->and(ProjectStatus::OnHold->labels())->toBe(['id' => 'Ditunda', 'en' => 'On Hold'])
        ->and(ProjectStatus::Archived->value)->toBe('archived')
        ->and(ProjectStatus::Archived->label())->toBe('Archived')
        ->and(ProjectStatus::Archived->labels())->toBe(['id' => 'Diarsipkan', 'en' => 'Archived'])
        ->and(ProjectStatus::Cancelled->value)->toBe('cancelled')
        ->and(ProjectStatus::Cancelled->label())->toBe('Cancelled')
        ->and(ProjectStatus::Cancelled->labels())->toBe(['id' => 'Dibatalkan', 'en' => 'Cancelled']);
});

test('skill type enum has valid values, labels, and localized labels', function () {
    expect(SkillType::Frontend->value)->toBe('frontend')
        ->and(SkillType::Frontend->label())->toBe('Frontend')
        ->and(SkillType::Frontend->labels())->toBe(['id' => 'Frontend', 'en' => 'Frontend'])
        ->and(SkillType::Backend->value)->toBe('backend')
        ->and(SkillType::Backend->label())->toBe('Backend')
        ->and(SkillType::Backend->labels())->toBe(['id' => 'Backend', 'en' => 'Backend'])
        ->and(SkillType::Fullstack->value)->toBe('fullstack')
        ->and(SkillType::Fullstack->label())->toBe('Full-Stack')
        ->and(SkillType::Fullstack->labels())->toBe(['id' => 'Full-Stack', 'en' => 'Full-Stack'])
        ->and(SkillType::Mobile->value)->toBe('mobile')
        ->and(SkillType::Mobile->label())->toBe('Mobile')
        ->and(SkillType::Mobile->labels())->toBe(['id' => 'Aplikasi Seluler', 'en' => 'Mobile'])
        ->and(SkillType::Database->value)->toBe('database')
        ->and(SkillType::Database->label())->toBe('Database')
        ->and(SkillType::Database->labels())->toBe(['id' => 'Basis Data', 'en' => 'Database'])
        ->and(SkillType::Devops->value)->toBe('devops')
        ->and(SkillType::Devops->label())->toBe('DevOps')
        ->and(SkillType::Devops->labels())->toBe(['id' => 'DevOps', 'en' => 'DevOps'])
        ->and(SkillType::Tool->value)->toBe('tool')
        ->and(SkillType::Tool->label())->toBe('Tools')
        ->and(SkillType::Tool->labels())->toBe(['id' => 'Alat & Perkakas', 'en' => 'Tools'])
        ->and(SkillType::Design->value)->toBe('design')
        ->and(SkillType::Design->label())->toBe('UI/UX & Design')
        ->and(SkillType::Design->labels())->toBe(['id' => 'Desain & UI/UX', 'en' => 'UI/UX & Design'])
        ->and(SkillType::AiMl->value)->toBe('ai_ml')
        ->and(SkillType::AiMl->label())->toBe('AI / ML')
        ->and(SkillType::AiMl->labels())->toBe(['id' => 'Kecerdasan Buatan (AI/ML)', 'en' => 'AI / ML'])
        ->and(SkillType::Testing->value)->toBe('testing')
        ->and(SkillType::Testing->label())->toBe('QA & Testing')
        ->and(SkillType::Testing->labels())->toBe(['id' => 'Pengujian & QA', 'en' => 'QA & Testing'])
        ->and(SkillType::SoftSkill->value)->toBe('soft_skill')
        ->and(SkillType::SoftSkill->label())->toBe('Soft Skills')
        ->and(SkillType::SoftSkill->labels())->toBe(['id' => 'Keterampilan Interpersonal', 'en' => 'Soft Skills']);
});
