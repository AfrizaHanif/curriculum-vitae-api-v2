<?php

/** @var TestCase $this */

use App\Models\Hobby;
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

test('guest can list hobbies', function () {
    Hobby::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/hobbies');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'icon'],
            ],
        ]);
});

test('guest can view a single hobby', function () {
    $hobby = Hobby::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/hobbies/{$hobby->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $hobby->id)
        ->assertJsonPath('data.name', $hobby->name);
});

test('guest cannot create a hobby', function () {
    $response = $this->postJson('/api/hobbies', [
        'name' => 'Photography',
    ]);

    $response->assertUnauthorized();
});

test('user can create a hobby with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'name' => 'Photography',
        'icon' => 'camera',
    ];

    $response = $this->postJson('/api/hobbies', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Photography')
        ->assertJsonPath('data.icon', 'camera');

    $this->assertDatabaseHas('hobbies', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'name' => 'Photography',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/hobbies', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('user can update their own hobby', function () {
    Sanctum::actingAs($this->user);
    $hobby = Hobby::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/hobbies/{$hobby->id}", [
        'name' => 'Astronomy',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Astronomy');

    $this->assertDatabaseHas('hobbies', [
        'id' => $hobby->id,
        'name' => 'Astronomy',
    ]);
});

test('user cannot update another user hobby', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherHobby = Hobby::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/hobbies/{$otherHobby->id}", [
        'name' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own hobby', function () {
    Sanctum::actingAs($this->user);
    $hobby = Hobby::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/hobbies/{$hobby->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('hobbies', ['id' => $hobby->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/hobbies/{$hobby->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $hobby->id);
    $this->assertNotSoftDeleted('hobbies', ['id' => $hobby->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/hobbies/{$hobby->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($hobby);
});

test('user cannot delete another user hobby', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherHobby = Hobby::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/hobbies/{$otherHobby->id}");
    $response->assertForbidden();
});
