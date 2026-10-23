<?php

/** @var TestCase $this */

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Portfolio;
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
});

test('guest can list projects', function () {
    Project::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/projects');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'type', 'type_label', 'type_labels', 'category', 'status'],
            ],
        ]);
});

test('guest can view a single project', function () {
    $project = Project::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/projects/{$project->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $project->id)
        ->assertJsonPath('data.title', $project->title)
        ->assertJsonPath('data.type', $project->type->value)
        ->assertJsonPath('data.type_label', $project->type->label());
});

test('guest cannot create a project', function () {
    $response = $this->postJson('/api/projects', [
        'title' => 'Open Source CMS',
        'type' => ProjectType::Personal->value,
        'category' => 'Fullstack',
        'status' => ProjectStatus::Ongoing->value,
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'A modern CMS',
    ]);

    $response->assertUnauthorized();
});

test('user can create a project with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'title' => 'Open Source CMS',
        'type' => ProjectType::Personal->value,
        'category' => 'Fullstack',
        'status' => ProjectStatus::Ongoing->value,
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'A modern CMS built with Laravel and React',
        'tags' => ['CMS', 'React'],
        'technology' => ['Laravel', 'Inertia', 'React'],
    ];

    $response = $this->postJson('/api/projects', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Open Source CMS')
        ->assertJsonPath('data.type', ProjectType::Personal->value)
        ->assertJsonPath('data.type_label', ProjectType::Personal->label());

    $this->assertDatabaseHas('projects', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'title' => 'Open Source CMS',
        'type' => ProjectType::Personal->value,
    ]);
});

test('validation fails when type is invalid on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/projects', [
        'title' => 'Invalid Project Type',
        'type' => 'invalid-type',
        'category' => 'Fullstack',
        'status' => ProjectStatus::Ongoing->value,
        'start_period' => '2024-01-01',
        'description' => 'A modern CMS',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/projects', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'type', 'category', 'status', 'start_period', 'description']);
});

test('user can update their own project', function () {
    Sanctum::actingAs($this->user);
    $project = Project::factory()->for($this->profile)->create([
        'type' => ProjectType::Personal->value,
    ]);

    $response = $this->putJson("/api/projects/{$project->id}", [
        'title' => 'Updated Project Title',
        'type' => ProjectType::Academic->value,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Updated Project Title')
        ->assertJsonPath('data.type', ProjectType::Academic->value)
        ->assertJsonPath('data.type_label', ProjectType::Academic->label());

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'title' => 'Updated Project Title',
        'type' => ProjectType::Academic->value,
    ]);
});

test('user cannot update another user project', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherProject = Project::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/projects/{$otherProject->id}", [
        'title' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user cannot assign project to another user portfolio on create or update', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPortfolio = Portfolio::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $createResponse = $this->postJson('/api/projects', [
        'portfolio_id' => $otherPortfolio->id,
        'title' => 'Stolen Portfolio Project',
        'type' => ProjectType::Personal->value,
        'category' => 'Fullstack',
        'status' => ProjectStatus::Ongoing->value,
        'start_period' => '2024-01-01',
        'description' => 'A project test',
    ]);
    $createResponse->assertNotFound();

    $project = Project::factory()->for($this->profile)->create();
    $updateResponse = $this->putJson("/api/projects/{$project->id}", [
        'portfolio_id' => $otherPortfolio->id,
    ]);
    $updateResponse->assertNotFound();
});

test('user can soft delete, restore, and force delete their own project', function () {
    Sanctum::actingAs($this->user);
    $project = Project::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/projects/{$project->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('projects', ['id' => $project->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/projects/{$project->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $project->id);
    $this->assertNotSoftDeleted('projects', ['id' => $project->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/projects/{$project->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($project);
});

test('user cannot delete another user project', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherProject = Project::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/projects/{$otherProject->id}");
    $response->assertForbidden();
});
