<?php

/** @var TestCase $this */

use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
    Sanctum::actingAs($this->user);
});

test('per_page parameter customizes pagination count', function () {
    Skill::factory()->for($this->profile)->count(5)->create();

    $response = $this->getJson('/api/skills?per_page=2');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('meta.per_page', 2)
        ->assertJsonPath('meta.total', 5);
});

test('per_page parameter is capped at maximum of 100', function () {
    Skill::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/skills?per_page=500');

    $response->assertOk()
        ->assertJsonPath('meta.per_page', 100);
});

test('all=true parameter returns unpaginated results', function () {
    Skill::factory()->for($this->profile)->count(5)->create();

    $response = $this->getJson('/api/skills?all=true');

    $response->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonMissing(['meta' => ['current_page']]);
});

test('paginate=false returns unpaginated results', function () {
    Skill::factory()->for($this->profile)->count(4)->create();

    $response = $this->getJson('/api/skills?paginate=false');

    $response->assertOk()
        ->assertJsonCount(4, 'data')
        ->assertJsonMissing(['meta']);
});

test('trashed query parameter allows filtering soft-deleted records', function () {
    $activeSkill = Skill::factory()->for($this->profile)->create(['name' => 'Active PHP']);
    $deletedSkill = Skill::factory()->for($this->profile)->create(['name' => 'Deleted Python']);
    $deletedSkill->delete();

    // 1. Default (no trashed param): only active records
    $defaultResponse = $this->getJson('/api/skills');
    $defaultResponse->assertOk()->assertJsonCount(1, 'data');
    expect(collect($defaultResponse->json('data'))->pluck('name'))->toContain('Active PHP')
        ->not->toContain('Deleted Python');

    // 2. trashed=with: returns both active and deleted
    $withResponse = $this->getJson('/api/skills?trashed=with');
    $withResponse->assertOk()->assertJsonCount(2, 'data');

    // 3. trashed=only: returns only soft-deleted
    $onlyResponse = $this->getJson('/api/skills?trashed=only');
    $onlyResponse->assertOk()->assertJsonCount(1, 'data');
    expect(collect($onlyResponse->json('data'))->pluck('name'))->toContain('Deleted Python')
        ->not->toContain('Active PHP');
});

test('handles resource filtering returns unpaginated collection when perPage is null and no page parameter is present', function () {
    Skill::factory()->for($this->profile)->count(3)->create();

    $filterHelper = new class
    {
        use HandlesResourceFiltering;

        public function filter(Request $request, $query, ?int $perPage = null): mixed
        {
            return $this->getFilteredResults($request, $query, '', null, $perPage);
        }
    };

    // When perPage is null and no page param is passed, it should return unpaginated collection
    $requestWithoutPage = Request::create('/api/skills');
    $results = $filterHelper->filter($requestWithoutPage, Skill::query(), null);
    expect($results)->toBeInstanceOf(Collection::class)
        ->and($results)->toHaveCount(3);

    // When perPage is null but page param is passed, it should paginate using default fallback of 15
    $requestWithPage = Request::create('/api/skills', 'GET', ['page' => 1]);
    $paginatedResults = $filterHelper->filter($requestWithPage, Skill::query(), null);
    expect($paginatedResults)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($paginatedResults->perPage())->toBe(15);
});
