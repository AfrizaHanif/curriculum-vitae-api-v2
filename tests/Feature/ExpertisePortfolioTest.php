<?php

use App\Models\Expertise;
use App\Models\Portfolio;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('expertise can have many portfolios via pivot table', function () {
    $user = User::factory()->create();
    $profile = Profile::forceCreate([
        'id' => 'PRO-001',
        'user_id' => $user->id,
        'fullname' => 'Test User',
        'phone' => '123456789',
        'email' => 'test@example.com',
        'birthday' => '2000-01-01',
        'status' => 'Active',
    ]);

    $expertise = Expertise::forceCreate([
        'id' => 'XPT-001',
        'profile_id' => $profile->id,
        'title' => 'Backend Development',
        'description' => 'Building scalable APIs',
    ]);

    $portfolioA = Portfolio::forceCreate([
        'id' => 'POR-001',
        'profile_id' => $profile->id,
        'title' => 'Project Alpha',
        'slug' => 'project-alpha',
        'type' => 'Commercial',
        'category' => 'Backend',
        'start_period' => '2025-01-01',
        'finish_period' => '2025-06-01',
        'description' => 'First project',
    ]);

    $portfolioB = Portfolio::forceCreate([
        'id' => 'POR-002',
        'profile_id' => $profile->id,
        'title' => 'Project Beta',
        'slug' => 'project-beta',
        'type' => 'Personal',
        'category' => 'Fullstack',
        'start_period' => '2025-07-01',
        'finish_period' => '2025-12-01',
        'description' => 'Second project',
    ]);

    $expertise->portfolios()->sync([$portfolioA->id, $portfolioB->id]);

    expect($expertise->portfolios)->toHaveCount(2)
        ->and($expertise->portfolios->pluck('id')->all())->toContain($portfolioA->id, $portfolioB->id);

    // Inverse relation
    expect($portfolioA->expertises)->toHaveCount(1)
        ->and($portfolioA->expertises->first()->id)->toBe($expertise->id);
});
