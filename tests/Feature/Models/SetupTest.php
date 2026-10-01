<?php

/** @var TestCase $this */

use App\Models\Profile;
use App\Models\Setup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
});

test('guest can list setups', function () {
    Setup::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/setups');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'category', 'description'],
            ],
        ]);
});

test('guest can view a single setup', function () {
    $setup = Setup::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/setups/{$setup->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $setup->id)
        ->assertJsonPath('data.name', $setup->name);
});

test('guest cannot create a setup', function () {
    $response = $this->postJson('/api/setups', [
        'name' => 'MacBook Pro M3 Max',
        'category' => 'Hardware',
        'description' => 'Main development laptop',
    ]);

    $response->assertUnauthorized();
});

test('user can create a setup with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'name' => 'MacBook Pro M3 Max',
        'category' => 'Hardware',
        'description' => 'Main development laptop with 64GB RAM',
        'reason' => 'Handles heavy local workloads smoothly',
    ];

    $response = $this->postJson('/api/setups', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'MacBook Pro M3 Max');

    $this->assertDatabaseHas('setups', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'name' => 'MacBook Pro M3 Max',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/setups', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'category', 'description']);
});

test('user can update their own setup', function () {
    Sanctum::actingAs($this->user);
    $setup = Setup::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/setups/{$setup->id}", [
        'name' => 'Custom Mechanical Keyboard',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Custom Mechanical Keyboard');

    $this->assertDatabaseHas('setups', [
        'id' => $setup->id,
        'name' => 'Custom Mechanical Keyboard',
    ]);
});

test('user cannot update another user setup', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSetup = Setup::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/setups/{$otherSetup->id}", [
        'name' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own setup', function () {
    Sanctum::actingAs($this->user);
    $setup = Setup::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/setups/{$setup->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('setups', ['id' => $setup->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/setups/{$setup->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $setup->id);
    $this->assertNotSoftDeleted('setups', ['id' => $setup->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/setups/{$setup->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($setup);
});

test('user cannot delete another user setup', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSetup = Setup::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/setups/{$otherSetup->id}");
    $response->assertForbidden();
});
