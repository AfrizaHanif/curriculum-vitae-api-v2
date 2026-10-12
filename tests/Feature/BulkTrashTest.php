<?php

/** @var TestCase $this */

use App\Models\CaseStudy;
use App\Models\Feature;
use App\Models\Portfolio;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
});

test('unauthenticated user cannot restore all or force delete all', function () {
    $this->postJson('/api/skills/restore-all')->assertUnauthorized();
    $this->deleteJson('/api/skills/force-delete-all')->assertUnauthorized();
});

test('user can restore all their soft-deleted skills', function () {
    Sanctum::actingAs($this->user);

    $skill1 = Skill::factory()->for($this->profile)->create();
    $skill2 = Skill::factory()->for($this->profile)->create();
    $skill1->delete();
    $skill2->delete();

    $this->assertSoftDeleted('skills', ['id' => $skill1->id]);
    $this->assertSoftDeleted('skills', ['id' => $skill2->id]);

    $response = $this->postJson('/api/skills/restore-all');

    $response->assertOk()
        ->assertJsonPath('count', 2)
        ->assertJsonPath('message', 'Successfully restored 2 soft-deleted record(s).');

    $this->assertNotSoftDeleted('skills', ['id' => $skill1->id]);
    $this->assertNotSoftDeleted('skills', ['id' => $skill2->id]);
});

test('user can restore specific soft-deleted skills by passing ids array', function () {
    Sanctum::actingAs($this->user);

    $skill1 = Skill::factory()->for($this->profile)->create();
    $skill2 = Skill::factory()->for($this->profile)->create();
    $skill1->delete();
    $skill2->delete();

    $response = $this->postJson('/api/skills/restore-all', [
        'ids' => [$skill1->id],
    ]);

    $response->assertOk()
        ->assertJsonPath('count', 1);

    $this->assertNotSoftDeleted('skills', ['id' => $skill1->id]);
    $this->assertSoftDeleted('skills', ['id' => $skill2->id]);
});

test('restore all does not restore another user soft-deleted skills', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSkill = Skill::factory()->for($otherProfile)->create();
    $otherSkill->delete();

    $mySkill = Skill::factory()->for($this->profile)->create();
    $mySkill->delete();

    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/skills/restore-all');

    $response->assertOk()
        ->assertJsonPath('count', 1);

    $this->assertNotSoftDeleted('skills', ['id' => $mySkill->id]);
    $this->assertSoftDeleted('skills', ['id' => $otherSkill->id]);
});

test('user can force delete all their soft-deleted skills', function () {
    Sanctum::actingAs($this->user);

    $skill1 = Skill::factory()->for($this->profile)->create();
    $skill2 = Skill::factory()->for($this->profile)->create();
    $activeSkill = Skill::factory()->for($this->profile)->create();

    $skill1->delete();
    $skill2->delete();

    $response = $this->deleteJson('/api/skills/force-delete-all');

    $response->assertOk()
        ->assertJsonPath('count', 2)
        ->assertJsonPath('message', 'Successfully permanently deleted 2 soft-deleted record(s).');

    $this->assertModelMissing($skill1);
    $this->assertModelMissing($skill2);
    $this->assertModelExists($activeSkill);
});

test('user can force delete specific soft-deleted skills by passing ids array', function () {
    Sanctum::actingAs($this->user);

    $skill1 = Skill::factory()->for($this->profile)->create();
    $skill2 = Skill::factory()->for($this->profile)->create();
    $skill1->delete();
    $skill2->delete();

    $response = $this->deleteJson('/api/skills/force-delete-all', [
        'ids' => [$skill1->id],
    ]);

    $response->assertOk()
        ->assertJsonPath('count', 1);

    $this->assertModelMissing($skill1);
    $this->assertSoftDeleted('skills', ['id' => $skill2->id]);
});

test('force delete all does not delete another user soft-deleted skills', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherSkill = Skill::factory()->for($otherProfile)->create();
    $otherSkill->delete();

    $mySkill = Skill::factory()->for($this->profile)->create();
    $mySkill->delete();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson('/api/skills/force-delete-all');

    $response->assertOk()
        ->assertJsonPath('count', 1);

    $this->assertModelMissing($mySkill);
    $this->assertSoftDeleted('skills', ['id' => $otherSkill->id]);
});

test('force delete all cleans up uploaded files from storage', function () {
    Sanctum::actingAs($this->user);
    Storage::fake('public');

    $imagePath = 'images/portfolios/cover_bulk_delete.jpg';
    Storage::disk('public')->put($imagePath, 'fake-binary');

    $portfolio = Portfolio::factory()->for($this->profile)->create([
        'image' => $imagePath,
    ]);
    $portfolio->delete();

    Storage::disk('public')->assertExists($imagePath);

    $this->deleteJson('/api/portfolios/force-delete-all')->assertOk();

    $this->assertModelMissing($portfolio);
    Storage::disk('public')->assertMissing($imagePath);
});

test('user can restore all and force delete all case studies and features', function () {
    Sanctum::actingAs($this->user);

    $portfolio = Portfolio::factory()->for($this->profile)->create();
    $caseStudy = CaseStudy::factory()->for($portfolio)->create();
    $caseStudy->delete();

    $feature = Feature::factory()->create([
        'featureable_type' => Portfolio::class,
        'featureable_id' => $portfolio->id,
    ]);
    $feature->delete();

    // Restore all case studies and features
    $this->postJson('/api/case-studies/restore-all')->assertOk()->assertJsonPath('count', 1);
    $this->assertNotSoftDeleted('case_studies', ['id' => $caseStudy->id]);

    $this->postJson('/api/features/restore-all')->assertOk()->assertJsonPath('count', 1);
    $this->assertNotSoftDeleted('features', ['id' => $feature->id]);

    // Delete again and force delete all
    $caseStudy->delete();
    $feature->delete();

    $this->deleteJson('/api/case-studies/force-delete-all')->assertOk()->assertJsonPath('count', 1);
    $this->assertModelMissing($caseStudy);

    $this->deleteJson('/api/features/force-delete-all')->assertOk()->assertJsonPath('count', 1);
    $this->assertModelMissing($feature);
});

