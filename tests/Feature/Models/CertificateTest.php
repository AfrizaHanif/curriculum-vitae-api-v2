<?php

/** @var TestCase $this */

use App\Models\Certificate;
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

test('guest can list certificates', function () {
    Certificate::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/certificates');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'type', 'issuer'],
            ],
        ]);
});

test('guest can view a single certificate', function () {
    $certificate = Certificate::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/certificates/{$certificate->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $certificate->id)
        ->assertJsonPath('data.title', $certificate->title);
});

test('guest cannot create a certificate', function () {
    $response = $this->postJson('/api/certificates', [
        'title' => 'AWS Certified Solutions Architect',
        'issuer' => 'Amazon Web Services',
    ]);

    $response->assertUnauthorized();
});

test('user can create a certificate with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'title' => 'AWS Certified Solutions Architect',
        'type' => 'Kursus',
        'issuer' => 'Amazon Web Services',
        'issued_date' => '2024-01-15',
        'credential_url' => 'https://aws.amazon.com/verify/12345',
    ];

    $response = $this->postJson('/api/certificates', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'AWS Certified Solutions Architect');

    $this->assertDatabaseHas('certificates', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'title' => 'AWS Certified Solutions Architect',
        'type' => 'Kursus',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/certificates', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'type']);
});

test('user can update their own certificate', function () {
    Sanctum::actingAs($this->user);
    $certificate = Certificate::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/certificates/{$certificate->id}", [
        'title' => 'AWS Certified DevOps Engineer Professional',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'AWS Certified DevOps Engineer Professional');

    $this->assertDatabaseHas('certificates', [
        'id' => $certificate->id,
        'title' => 'AWS Certified DevOps Engineer Professional',
    ]);
});

test('user cannot update another user certificate', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherCertificate = Certificate::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/certificates/{$otherCertificate->id}", [
        'title' => 'Unauthorized Certificate Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own certificate', function () {
    Sanctum::actingAs($this->user);
    $certificate = Certificate::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/certificates/{$certificate->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('certificates', ['id' => $certificate->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/certificates/{$certificate->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $certificate->id);
    $this->assertNotSoftDeleted('certificates', ['id' => $certificate->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/certificates/{$certificate->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($certificate);
});

test('user cannot delete another user certificate', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherCertificate = Certificate::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/certificates/{$otherCertificate->id}");
    $response->assertForbidden();
});
