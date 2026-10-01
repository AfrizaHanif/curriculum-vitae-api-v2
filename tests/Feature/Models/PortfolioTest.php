<?php

/** @var TestCase $this */

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

test('guest can list portfolios', function () {
    Portfolio::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/portfolios');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'type', 'category'],
            ],
        ]);
});

test('guest can view a single portfolio', function () {
    $portfolio = Portfolio::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/portfolios/{$portfolio->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $portfolio->id)
        ->assertJsonPath('data.title', $portfolio->title);
});

test('guest cannot create a portfolio', function () {
    $response = $this->postJson('/api/portfolios', [
        'title' => 'E-Commerce Platform',
        'type' => 'Academic Project',
        'category' => 'Backend',
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'Scalable shop API',
    ]);

    $response->assertUnauthorized();
});

test('user can create a portfolio with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'title' => 'E-Commerce Platform',
        'type' => 'Academic Project',
        'category' => 'Backend',
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'Scalable shop API built with Laravel',
        'tags' => ['API', 'Laravel'],
        'technology' => ['PHP', 'MySQL', 'Redis'],
    ];

    $response = $this->postJson('/api/portfolios', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'E-Commerce Platform');

    $this->assertDatabaseHas('portfolios', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'title' => 'E-Commerce Platform',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/portfolios', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'type', 'category', 'start_period', 'finish_period', 'description']);
});

test('user can update their own portfolio', function () {
    Sanctum::actingAs($this->user);
    $portfolio = Portfolio::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/portfolios/{$portfolio->id}", [
        'title' => 'Updated Portfolio Title',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Updated Portfolio Title');

    $this->assertDatabaseHas('portfolios', [
        'id' => $portfolio->id,
        'title' => 'Updated Portfolio Title',
    ]);
});

test('user cannot update another user portfolio', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPortfolio = Portfolio::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/portfolios/{$otherPortfolio->id}", [
        'title' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own portfolio', function () {
    Sanctum::actingAs($this->user);
    $portfolio = Portfolio::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/portfolios/{$portfolio->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('portfolios', ['id' => $portfolio->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/portfolios/{$portfolio->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $portfolio->id);
    $this->assertNotSoftDeleted('portfolios', ['id' => $portfolio->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/portfolios/{$portfolio->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($portfolio);
});

test('user cannot delete another user portfolio', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPortfolio = Portfolio::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/portfolios/{$otherPortfolio->id}");
    $response->assertForbidden();
});
