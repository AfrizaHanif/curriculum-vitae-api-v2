<?php

/** @var TestCase $this */

use App\Models\Profile;
use App\Models\Social;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
});

test('guest can list socials', function () {
    Social::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/socials');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'url', 'icon'],
            ],
        ]);
});

test('guest can view a single social', function () {
    $social = Social::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/socials/{$social->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $social->id)
        ->assertJsonPath('data.name', $social->name);
});

test('guest cannot create a social', function () {
    $response = $this->postJson('/api/socials', [
        'name' => 'GitHub',
        'url' => 'https://github.com/testuser',
        'icon' => 'github',
    ]);

    $response->assertUnauthorized();
});

test('user can create a social with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'name' => 'GitHub',
        'url' => 'https://github.com/testuser',
        'icon' => 'github',
    ];

    $response = $this->postJson('/api/socials', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'GitHub')
        ->assertJsonPath('data.url', 'https://github.com/testuser');

    $this->assertDatabaseHas('socials', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'name' => 'GitHub',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/socials', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'url', 'icon']);
});

test('user can update their own social', function () {
    Sanctum::actingAs($this->user);
    $social = Social::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/socials/{$social->id}", [
        'name' => 'LinkedIn',
        'url' => 'https://linkedin.com/in/testuser',
        'icon' => 'linkedin',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'LinkedIn');

    $this->assertDatabaseHas('socials', [
        'id' => $social->id,
        'name' => 'LinkedIn',
    ]);
});

test('user cannot update another user social', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSocial = Social::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/socials/{$otherSocial->id}", [
        'name' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own social', function () {
    Sanctum::actingAs($this->user);
    $social = Social::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/socials/{$social->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('socials', ['id' => $social->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/socials/{$social->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $social->id);
    $this->assertNotSoftDeleted('socials', ['id' => $social->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/socials/{$social->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($social);
});

test('user cannot delete another user social', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSocial = Social::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/socials/{$otherSocial->id}");
    $response->assertForbidden();
});
