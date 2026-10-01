<?php

/** @var TestCase $this */

use App\Models\Expertise;
use App\Models\Portfolio;
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

test('guest can list expertises', function () {
    Expertise::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/expertises');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'description', 'icon'],
            ],
        ]);
});

test('guest can view a single expertise', function () {
    $expertise = Expertise::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/expertises/{$expertise->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $expertise->id)
        ->assertJsonPath('data.title', $expertise->title);
});

test('guest cannot create an expertise', function () {
    $response = $this->postJson('/api/expertises', [
        'title' => 'Web Development',
        'description' => 'Building modern websites',
    ]);

    $response->assertUnauthorized();
});

test('user can create an expertise with valid data and attach portfolios', function () {
    Sanctum::actingAs($this->user);
    $portfolio = Portfolio::factory()->for($this->profile)->create();

    $payload = [
        'title' => 'Backend Architecture',
        'description' => 'High-throughput distributed systems',
        'icon' => 'server',
        'portfolio_ids' => [$portfolio->id],
    ];

    $response = $this->postJson('/api/expertises', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Backend Architecture');

    $this->assertDatabaseHas('expertises', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'title' => 'Backend Architecture',
    ]);

    $this->assertDatabaseHas('expertise_portfolio', [
        'expertise_id' => $response->json('data.id'),
        'portfolio_id' => $portfolio->id,
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/expertises', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'description']);
});

test('user can update their own expertise', function () {
    Sanctum::actingAs($this->user);
    $expertise = Expertise::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/expertises/{$expertise->id}", [
        'title' => 'Cloud Native Engineering',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Cloud Native Engineering');

    $this->assertDatabaseHas('expertises', [
        'id' => $expertise->id,
        'title' => 'Cloud Native Engineering',
    ]);
});

test('user cannot update another user expertise', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherExpertise = Expertise::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/expertises/{$otherExpertise->id}", [
        'title' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own expertise', function () {
    Sanctum::actingAs($this->user);
    $expertise = Expertise::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/expertises/{$expertise->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('expertises', ['id' => $expertise->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/expertises/{$expertise->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $expertise->id);
    $this->assertNotSoftDeleted('expertises', ['id' => $expertise->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/expertises/{$expertise->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($expertise);
});

test('user cannot delete another user expertise', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherExpertise = Expertise::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/expertises/{$otherExpertise->id}");
    $response->assertForbidden();
});
