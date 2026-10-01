<?php

/** @var TestCase $this */

use App\Models\Profile;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
});

test('guest can list testimonials', function () {
    Testimonial::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/testimonials');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'role', 'content'],
            ],
        ]);
});

test('guest can view a single testimonial', function () {
    $testimonial = Testimonial::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/testimonials/{$testimonial->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $testimonial->id)
        ->assertJsonPath('data.name', $testimonial->name);
});

test('guest cannot create a testimonial', function () {
    $response = $this->postJson('/api/testimonials', [
        'name' => 'Jane Smith',
        'role' => 'CTO at Acme Corp',
        'content' => 'Exceptional developer who delivers on time.',
    ]);

    $response->assertUnauthorized();
});

test('user can create a testimonial with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'name' => 'Jane Smith',
        'role' => 'CTO at Acme Corp',
        'content' => 'Exceptional developer who delivers on time and with high quality code.',
    ];

    $response = $this->postJson('/api/testimonials', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Jane Smith');

    $this->assertDatabaseHas('testimonials', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'name' => 'Jane Smith',
    ]);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/testimonials', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'role', 'content']);
});

test('user can update their own testimonial', function () {
    Sanctum::actingAs($this->user);
    $testimonial = Testimonial::factory()->for($this->profile)->create();

    $response = $this->putJson("/api/testimonials/{$testimonial->id}", [
        'name' => 'Jane Doe',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Jane Doe');

    $this->assertDatabaseHas('testimonials', [
        'id' => $testimonial->id,
        'name' => 'Jane Doe',
    ]);
});

test('user cannot update another user testimonial', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherTestimonial = Testimonial::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/testimonials/{$otherTestimonial->id}", [
        'name' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own testimonial', function () {
    Sanctum::actingAs($this->user);
    $testimonial = Testimonial::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/testimonials/{$testimonial->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('testimonials', ['id' => $testimonial->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/testimonials/{$testimonial->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $testimonial->id);
    $this->assertNotSoftDeleted('testimonials', ['id' => $testimonial->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/testimonials/{$testimonial->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($testimonial);
});

test('user cannot delete another user testimonial', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherTestimonial = Testimonial::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/testimonials/{$otherTestimonial->id}");
    $response->assertForbidden();
});
