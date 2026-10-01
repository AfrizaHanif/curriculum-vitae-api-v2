<?php

/** @var TestCase $this */

use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('generates formatted custom id with prefix and zero-padding on creation', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->for($user)->create();

    $skill = Skill::factory()->for($profile)->create([
        'id' => null,
    ]);

    expect($skill->id)->toBe('SKI-001');
});

test('increments custom id sequentially', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->for($user)->create();

    $skill1 = Skill::factory()->for($profile)->create(['id' => null]);
    $skill2 = Skill::factory()->for($profile)->create(['id' => null]);

    expect($skill1->id)->toBe('SKI-001')
        ->and($skill2->id)->toBe('SKI-002');
});

test('calculates next custom id considering soft-deleted records', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->for($user)->create();

    $skill1 = Skill::factory()->for($profile)->create(['id' => null]);
    expect($skill1->id)->toBe('SKI-001');

    $skill1->delete(); // Soft delete

    $skill2 = Skill::factory()->for($profile)->create(['id' => null]);
    expect($skill2->id)->toBe('SKI-002');
});

test('preserves manually assigned custom id if provided', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->for($user)->create();

    $skill = Skill::factory()->for($profile)->create([
        'id' => 'SKI-999',
    ]);

    expect($skill->id)->toBe('SKI-999');
});
