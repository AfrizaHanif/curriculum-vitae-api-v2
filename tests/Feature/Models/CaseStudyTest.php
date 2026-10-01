<?php

/** @var TestCase $this */

use App\Models\CaseStudy;
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
    $this->portfolio = Portfolio::factory()->for($this->profile)->create();
});

test('guest can list case studies', function () {
    CaseStudy::factory()->for($this->portfolio)->count(3)->create();

    $response = $this->getJson('/api/case-studies');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'portfolio_id', 'role', 'problems', 'goals'],
            ],
        ]);
});

test('guest can view a single case study', function () {
    $caseStudy = CaseStudy::factory()->for($this->portfolio)->create();

    $response = $this->getJson("/api/case-studies/{$caseStudy->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $caseStudy->id)
        ->assertJsonPath('data.role', $caseStudy->role);
});

test('guest cannot create a case study', function () {
    $response = $this->postJson('/api/case-studies', [
        'portfolio_id' => $this->portfolio->id,
        'role' => 'Tech Lead',
        'problems' => ['Database bottleneck'],
        'goals' => ['Improve latency'],
    ]);

    $response->assertUnauthorized();
});

test('user can create a case study with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'portfolio_id' => $this->portfolio->id,
        'role' => 'Lead Engineer',
        'problems' => ['Slow response times', 'High server load'],
        'goals' => ['Achieve sub-100ms response time'],
        'solutions' => [['title' => 'Caching', 'context' => 'Redis cache', 'visual' => '']],
    ];

    $response = $this->postJson('/api/case-studies', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.role', 'Lead Engineer');

    $this->assertDatabaseHas('case_studies', [
        'id' => $response->json('data.id'),
        'portfolio_id' => $this->portfolio->id,
        'role' => 'Lead Engineer',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/case-studies', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['portfolio_id', 'role', 'problems', 'goals']);
});

test('user cannot create case study for another user portfolio', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPortfolio = Portfolio::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/case-studies', [
        'portfolio_id' => $otherPortfolio->id,
        'role' => 'Attacker',
        'problems' => ['Unauthorized'],
        'goals' => ['Hacking'],
    ]);

    $response->assertNotFound();
});

test('user can update their own case study', function () {
    Sanctum::actingAs($this->user);
    $caseStudy = CaseStudy::factory()->for($this->portfolio)->create();

    $response = $this->putJson("/api/case-studies/{$caseStudy->id}", [
        'role' => 'Principal Architect',
        'problems' => ['New problem'],
        'goals' => ['New goal'],
    ]);

    $response->assertOk()
        ->assertJsonPath('data.role', 'Principal Architect');

    $this->assertDatabaseHas('case_studies', [
        'id' => $caseStudy->id,
        'role' => 'Principal Architect',
    ]);
});

test('user cannot update another user case study', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPortfolio = Portfolio::factory()->for($otherProfile)->create();
    $otherCaseStudy = CaseStudy::factory()->for($otherPortfolio)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/case-studies/{$otherCaseStudy->id}", [
        'role' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own case study', function () {
    Sanctum::actingAs($this->user);
    $caseStudy = CaseStudy::factory()->for($this->portfolio)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/case-studies/{$caseStudy->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('case_studies', ['id' => $caseStudy->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/case-studies/{$caseStudy->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $caseStudy->id);
    $this->assertNotSoftDeleted('case_studies', ['id' => $caseStudy->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/case-studies/{$caseStudy->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($caseStudy);
});

test('user cannot delete another user case study', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPortfolio = Portfolio::factory()->for($otherProfile)->create();
    $otherCaseStudy = CaseStudy::factory()->for($otherPortfolio)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/case-studies/{$otherCaseStudy->id}");
    $response->assertForbidden();
});
