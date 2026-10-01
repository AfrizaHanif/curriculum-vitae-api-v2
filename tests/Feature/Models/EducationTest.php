<?php

/** @var TestCase $this */

use App\Enums\EducationStatus;
use App\Enums\EducationType;
use App\Models\Education;
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

test('guest can list educations', function () {
    Education::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/educations');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'institution', 'type', 'degree', 'major', 'status'],
            ],
        ]);
});

test('guest can view a single education', function () {
    $education = Education::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/educations/{$education->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $education->id)
        ->assertJsonPath('data.institution', $education->institution);
});

test('guest cannot create an education', function () {
    $response = $this->postJson('/api/educations', [
        'institution' => 'MIT',
        'type' => EducationType::Formal->value,
        'degree' => 'B.Sc',
        'major' => 'Computer Science',
        'status' => EducationStatus::Graduated->value,
        'start_period' => '2020-09-01',
    ]);

    $response->assertUnauthorized();
});

test('user can create an education with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'institution' => 'Stanford University',
        'type' => EducationType::Formal->value,
        'degree' => 'B.Sc',
        'major' => 'Computer Science',
        'gpa' => 3.95,
        'status' => EducationStatus::Graduated->value,
        'start_period' => '2019-09-01',
        'finish_period' => '2023-06-01',
        'description' => ['Graduated with honors'],
    ];

    $response = $this->postJson('/api/educations', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.institution', 'Stanford University')
        ->assertJsonPath('data.degree', 'B.Sc');

    $this->assertDatabaseHas('education', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'institution' => 'Stanford University',
        'degree' => 'B.Sc',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/educations', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['institution', 'type', 'degree', 'major', 'status', 'start_period']);
});

test('user can update their own education', function () {
    Sanctum::actingAs($this->user);
    $education = Education::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/educations/{$education->id}", [
        'institution' => 'Harvard University',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.institution', 'Harvard University');

    $this->assertDatabaseHas('education', [
        'id' => $education->id,
        'institution' => 'Harvard University',
    ]);
});

test('user cannot update another user education', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherEducation = Education::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/educations/{$otherEducation->id}", [
        'institution' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own education', function () {
    Sanctum::actingAs($this->user);
    $education = Education::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/educations/{$education->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('education', ['id' => $education->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/educations/{$education->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $education->id);
    $this->assertNotSoftDeleted('education', ['id' => $education->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/educations/{$education->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($education);
});

test('user cannot delete another user education', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherEducation = Education::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/educations/{$otherEducation->id}");
    $response->assertForbidden();
});
