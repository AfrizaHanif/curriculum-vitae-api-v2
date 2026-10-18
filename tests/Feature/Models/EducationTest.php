<?php

/** @var TestCase $this */

use App\Enums\EducationDegree;
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
        'degree' => EducationDegree::Bachelor->value,
        'major' => [
            'id' => 'Ilmu Komputer',
            'en' => 'Computer Science',
        ],
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
        'degree' => EducationDegree::Bachelor->value,
        'academic_title' => 'B.Sc',
        'major' => [
            'id' => 'Ilmu Komputer',
            'en' => 'Computer Science',
        ],
        'gpa' => 3.95,
        'status' => EducationStatus::Graduated->value,
        'start_period' => '2019-09-01',
        'finish_period' => '2023-06-01',
        'description' => ['Graduated with honors'],
    ];

    $response = $this->postJson('/api/educations', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.institution', 'Stanford University')
        ->assertJsonPath('data.degree', EducationDegree::Bachelor->value)
        ->assertJsonPath('data.academic_title', 'B.Sc')
        ->assertJsonPath('data.major.en', 'Computer Science');

    $this->assertDatabaseHas('education', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'institution' => 'Stanford University',
        'degree' => EducationDegree::Bachelor->value,
        'academic_title' => 'B.Sc',
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

test('user can create education with custom max_gpa', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'institution' => 'University of Oxford',
        'type' => EducationType::Formal->value,
        'degree' => EducationDegree::Bachelor->value,
        'major' => [
            'id' => 'Filsafat, Politik dan Ekonomi',
            'en' => 'Philosophy, Politics and Economics',
        ],
        'gpa' => 88.5,
        'max_gpa' => 100.0,
        'status' => EducationStatus::Graduated->value,
        'start_period' => '2019-10-01',
    ];

    $response = $this->postJson('/api/educations', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.gpa', 88.5)
        ->assertJsonPath('data.max_gpa', 100);

    $this->assertDatabaseHas('education', [
        'id' => $response->json('data.id'),
        'gpa' => 88.5,
        'max_gpa' => 100.0,
    ]);
});

test('validation fails when gpa exceeds max_gpa', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'institution' => 'Invalid University',
        'type' => EducationType::Formal->value,
        'degree' => EducationDegree::Bachelor->value,
        'major' => [
            'id' => 'Ilmu Komputer',
            'en' => 'Computer Science',
        ],
        'gpa' => 4.5,
        'max_gpa' => 4.0,
        'status' => EducationStatus::Graduated->value,
        'start_period' => '2020-09-01',
    ];

    $response = $this->postJson('/api/educations', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['gpa']);
});
