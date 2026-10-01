<?php

/** @var TestCase $this */

use App\Enums\SkillType;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
});

test('guest can list skills', function () {
    Skill::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/skills');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'type', 'display_order'],
            ],
        ]);
});

test('guest can view a single skill', function () {
    $skill = Skill::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/skills/{$skill->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $skill->id)
        ->assertJsonPath('data.name', $skill->name);
});

test('guest cannot create a skill', function () {
    $response = $this->postJson('/api/skills', [
        'profile_id' => $this->profile->id,
        'name' => 'Laravel',
        'type' => SkillType::Backend->value,
        'display_order' => 1,
    ]);

    $response->assertUnauthorized();
});

test('user can create a skill with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'profile_id' => $this->profile->id,
        'name' => 'Laravel',
        'type' => SkillType::Backend->value,
        'display_order' => 1,
    ];

    $response = $this->postJson('/api/skills', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Laravel')
        ->assertJsonPath('data.type', SkillType::Backend->value);

    $this->assertDatabaseHas('skills', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'name' => 'Laravel',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/skills', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['profile_id', 'name', 'type', 'display_order']);
});

test('user can update their own skill', function () {
    Sanctum::actingAs($this->user);
    $skill = Skill::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/skills/{$skill->id}", [
        'name' => 'PHP 8.5',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'PHP 8.5');

    $this->assertDatabaseHas('skills', [
        'id' => $skill->id,
        'name' => 'PHP 8.5',
    ]);
});

test('user cannot update another user skill', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSkill = Skill::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/skills/{$otherSkill->id}", [
        'name' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own skill', function () {
    Sanctum::actingAs($this->user);
    $skill = Skill::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/skills/{$skill->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('skills', ['id' => $skill->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/skills/{$skill->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $skill->id);
    $this->assertNotSoftDeleted('skills', ['id' => $skill->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/skills/{$skill->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($skill);
});

test('user cannot delete another user skill', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSkill = Skill::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/skills/{$otherSkill->id}");
    $response->assertForbidden();
});
