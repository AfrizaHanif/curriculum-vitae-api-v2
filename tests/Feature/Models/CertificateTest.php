<?php

/** @var TestCase $this */

use App\Enums\CertificateType;
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
                '*' => ['id', 'title', 'type', 'type_label', 'type_labels', 'issuer'],
            ],
        ]);
});

test('guest can view a single certificate', function () {
    $certificate = Certificate::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/certificates/{$certificate->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $certificate->id)
        ->assertJsonPath('data.title', $certificate->title)
        ->assertJsonPath('data.type', $certificate->type->value)
        ->assertJsonPath('data.type_label', $certificate->type->label());
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
        'type' => CertificateType::Course->value,
        'issuer' => 'Amazon Web Services',
        'issued_date' => '2024-01-15',
        'expired_date' => '2027-01-15',
        'credential_id' => 'AWS-12345',
        'credential_url' => 'https://aws.amazon.com/verify/12345',
        'description' => 'Comprehensive cloud architecture certification.',
        'is_featured' => true,
    ];

    $response = $this->postJson('/api/certificates', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'AWS Certified Solutions Architect')
        ->assertJsonPath('data.type', CertificateType::Course->value)
        ->assertJsonPath('data.type_label', CertificateType::Course->label())
        ->assertJsonPath('data.credential_id', 'AWS-12345')
        ->assertJsonPath('data.expired_date', '2027-01-15')
        ->assertJsonPath('data.description', 'Comprehensive cloud architecture certification.')
        ->assertJsonPath('data.is_featured', true);

    $this->assertDatabaseHas('certificates', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'title' => 'AWS Certified Solutions Architect',
        'type' => CertificateType::Course->value,
        'credential_id' => 'AWS-12345',
        'is_featured' => true,
    ]);
});

test('validation fails when type is invalid on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/certificates', [
        'title' => 'Invalid Certificate Type',
        'type' => 'invalid-type',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/certificates', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'type']);
});

test('user can update their own certificate', function () {
    Sanctum::actingAs($this->user);
    $certificate = Certificate::factory()->for($this->profile)->create([
        'type' => CertificateType::Course->value,
    ]);

    $response = $this->putJson("/api/certificates/{$certificate->id}", [
        'title' => 'AWS Certified DevOps Engineer Professional',
        'type' => CertificateType::Certification->value,
        'credential_id' => 'AWS-DEV-999',
        'description' => 'Updated description.',
        'is_featured' => true,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'AWS Certified DevOps Engineer Professional')
        ->assertJsonPath('data.type', CertificateType::Certification->value)
        ->assertJsonPath('data.credential_id', 'AWS-DEV-999')
        ->assertJsonPath('data.description', 'Updated description.')
        ->assertJsonPath('data.is_featured', true);

    $this->assertDatabaseHas('certificates', [
        'id' => $certificate->id,
        'title' => 'AWS Certified DevOps Engineer Professional',
        'type' => CertificateType::Certification->value,
        'credential_id' => 'AWS-DEV-999',
        'is_featured' => true,
    ]);
});

test('validation fails when type is invalid on update', function () {
    Sanctum::actingAs($this->user);
    $certificate = Certificate::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/certificates/{$certificate->id}", [
        'type' => 'invalid-type',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
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
