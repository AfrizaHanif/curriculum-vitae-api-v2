<?php

/** @var TestCase $this */

use App\Models\Feature;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
    $this->project = Project::factory()->for($this->profile)->create();
});

test('guest can list features', function () {
    Feature::factory()->for($this->project, 'featureable')->count(3)->create();

    $response = $this->getJson('/api/features');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'featureable_type', 'featureable_id', 'title'],
            ],
        ]);
});

test('guest can view a single feature', function () {
    $feature = Feature::factory()->for($this->project, 'featureable')->create();

    $response = $this->getJson("/api/features/{$feature->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $feature->id)
        ->assertJsonPath('data.title', $feature->title);
});

test('guest cannot create a feature', function () {
    $response = $this->postJson('/api/features', [
        'featureable_type' => Project::class,
        'featureable_id' => $this->project->id,
        'title' => 'Dark Mode Support',
    ]);

    $response->assertUnauthorized();
});

test('user can create a feature with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'featureable_type' => Project::class,
        'featureable_id' => $this->project->id,
        'title' => 'Dark Mode Support',
        'description' => 'Automatic theme switcher based on system preferences',
        'progress' => 80,
    ];

    $response = $this->postJson('/api/features', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Dark Mode Support')
        ->assertJsonPath('data.progress', 80);

    $this->assertDatabaseHas('features', [
        'id' => $response->json('data.id'),
        'featureable_id' => $this->project->id,
        'title' => 'Dark Mode Support',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/features', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['featureable_type', 'featureable_id', 'title']);
});

test('user can update their own feature', function () {
    Sanctum::actingAs($this->user);
    $feature = Feature::factory()->for($this->project, 'featureable')->create();

    $response = $this->putJson("/api/features/{$feature->id}", [
        'title' => 'Light & Dark Mode Support',
        'progress' => 100,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Light & Dark Mode Support')
        ->assertJsonPath('data.progress', 100);

    $this->assertDatabaseHas('features', [
        'id' => $feature->id,
        'title' => 'Light & Dark Mode Support',
    ]);
});

test('user cannot update another user feature', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherProject = Project::factory()->for($otherProfile)->create();
    $otherFeature = Feature::factory()->for($otherProject, 'featureable')->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/features/{$otherFeature->id}", [
        'title' => 'Unauthorized Feature Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own feature', function () {
    Sanctum::actingAs($this->user);
    $feature = Feature::factory()->for($this->project, 'featureable')->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/features/{$feature->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('features', ['id' => $feature->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/features/{$feature->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $feature->id);
    $this->assertNotSoftDeleted('features', ['id' => $feature->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/features/{$feature->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($feature);
});

test('user cannot delete another user feature', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherProject = Project::factory()->for($otherProfile)->create();
    $otherFeature = Feature::factory()->for($otherProject, 'featureable')->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/features/{$otherFeature->id}");
    $response->assertForbidden();
});

test('user cannot create a feature for another user project or invalid type', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherProject = Project::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    // 1. Invalid parent ownership
    $response = $this->postJson('/api/features', [
        'featureable_type' => Project::class,
        'featureable_id' => $otherProject->id,
        'title' => 'Sneaky Feature',
    ]);
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['featureable_id']);

    // 2. Disallowed polymorphic type
    $responseInvalidType = $this->postJson('/api/features', [
        'featureable_type' => User::class,
        'featureable_id' => (string) $this->user->id,
        'title' => 'Invalid Type Feature',
    ]);
    $responseInvalidType->assertUnprocessable()
        ->assertJsonValidationErrors(['featureable_type']);
});
