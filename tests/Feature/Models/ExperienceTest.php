<?php

/** @var TestCase $this */

use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
});

test('guest can list experiences', function () {
    Experience::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/experiences');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'company', 'type', 'status'],
            ],
        ]);
});

test('guest can view a single experience', function () {
    $experience = Experience::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/experiences/{$experience->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $experience->id)
        ->assertJsonPath('data.title', $experience->title);
});

test('guest cannot create an experience', function () {
    $response = $this->postJson('/api/experiences', [
        'title' => 'Software Engineer',
        'company' => 'Google',
        'type' => ExperienceType::FullTime->value,
        'status' => ExperienceStatus::Active->value,
        'start_period' => '2022-01-01',
    ]);

    $response->assertUnauthorized();
});

test('user can create an experience with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'title' => 'Senior Backend Engineer',
        'company' => 'Tech Corp',
        'type' => ExperienceType::FullTime->value,
        'status' => ExperienceStatus::Active->value,
        'start_period' => '2022-01-01',
        'finish_period' => '2024-01-01',
        'description' => ['Designed microservices architecture'],
    ];

    $response = $this->postJson('/api/experiences', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Senior Backend Engineer')
        ->assertJsonPath('data.company', 'Tech Corp');

    $this->assertDatabaseHas('experiences', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'title' => 'Senior Backend Engineer',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/experiences', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'company', 'type', 'status', 'start_period']);
});

test('user can update their own experience', function () {
    Sanctum::actingAs($this->user);
    $experience = Experience::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/experiences/{$experience->id}", [
        'title' => 'Staff Engineer',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Staff Engineer');

    $this->assertDatabaseHas('experiences', [
        'id' => $experience->id,
        'title' => 'Staff Engineer',
    ]);
});

test('user cannot update another user experience', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherExperience = Experience::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/experiences/{$otherExperience->id}", [
        'title' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own experience', function () {
    Sanctum::actingAs($this->user);
    $experience = Experience::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/experiences/{$experience->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('experiences', ['id' => $experience->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/experiences/{$experience->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $experience->id);
    $this->assertNotSoftDeleted('experiences', ['id' => $experience->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/experiences/{$experience->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($experience);
});

test('user cannot delete another user experience', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherExperience = Experience::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/experiences/{$otherExperience->id}");
    $response->assertForbidden();
});
