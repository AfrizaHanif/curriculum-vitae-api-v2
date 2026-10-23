<?php

/** @var TestCase $this */

use App\Enums\PostCategory;
use App\Models\Post;
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

test('guest can list posts', function () {
    Post::factory()->for($this->profile)->count(3)->create();

    $response = $this->getJson('/api/posts');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'category', 'category_label', 'category_labels', 'author'],
            ],
        ]);
});

test('guest can view a single post', function () {
    $post = Post::factory()->for($this->profile)->create();

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $post->id)
        ->assertJsonPath('data.title', $post->title)
        ->assertJsonPath('data.category', $post->category->value)
        ->assertJsonPath('data.category_label', $post->category->label());
});

test('guest cannot create a post', function () {
    $response = $this->postJson('/api/posts', [
        'title' => 'Getting Started with Pest',
        'category' => PostCategory::Tech->value,
        'author' => 'John Doe',
        'summary' => 'Introduction to testing with Pest',
        'content' => 'Full article body goes here...',
    ]);

    $response->assertUnauthorized();
});

test('user can create a post with valid data', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'title' => 'Getting Started with Pest',
        'category' => PostCategory::Tech->value,
        'author' => 'John Doe',
        'summary' => 'Introduction to testing with Pest in Laravel',
        'content' => 'Full article body goes here and explains how to test...',
        'tags' => ['PHP', 'Pest', 'Laravel'],
    ];

    $response = $this->postJson('/api/posts', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Getting Started with Pest')
        ->assertJsonPath('data.category', PostCategory::Tech->value)
        ->assertJsonPath('data.category_label', PostCategory::Tech->label());

    $this->assertDatabaseHas('posts', [
        'id' => $response->json('data.id'),
        'profile_id' => $this->profile->id,
        'title' => 'Getting Started with Pest',
        'category' => PostCategory::Tech->value,
    ]);
});

test('validation fails when category is invalid on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/posts', [
        'title' => 'Invalid Category Post',
        'category' => 'invalid-category',
        'author' => 'John Doe',
        'summary' => 'Some summary',
        'content' => 'Some content',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['category']);
});

test('validation fails when required fields are missing on store', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/posts', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'category', 'author', 'summary', 'content']);
});

test('user can update their own post', function () {
    Sanctum::actingAs($this->user);
    $post = Post::factory()->for($this->profile)->create([
        'category' => PostCategory::Announcement->value,
    ]);

    $response = $this->putJson("/api/posts/{$post->id}", [
        'title' => 'Updated Post Title',
        'category' => PostCategory::Tutorial->value,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Updated Post Title')
        ->assertJsonPath('data.category', PostCategory::Tutorial->value)
        ->assertJsonPath('data.category_label', PostCategory::Tutorial->label());

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Post Title',
        'category' => PostCategory::Tutorial->value,
    ]);
});

test('user cannot update another user post', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPost = Post::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/posts/{$otherPost->id}", [
        'title' => 'Unauthorized Update',
    ]);

    $response->assertForbidden();
});

test('user can soft delete, restore, and force delete their own post', function () {
    Sanctum::actingAs($this->user);
    $post = Post::factory()->for($this->profile)->create();

    // 1. Soft delete
    $deleteResponse = $this->deleteJson("/api/posts/{$post->id}");
    $deleteResponse->assertNoContent();
    $this->assertSoftDeleted('posts', ['id' => $post->id]);

    // 2. Restore
    $restoreResponse = $this->postJson("/api/posts/{$post->id}/restore");
    $restoreResponse->assertOk()
        ->assertJsonPath('data.id', $post->id);
    $this->assertNotSoftDeleted('posts', ['id' => $post->id]);

    // 3. Force delete
    $forceDeleteResponse = $this->deleteJson("/api/posts/{$post->id}?force=1");
    $forceDeleteResponse->assertNoContent();
    $this->assertModelMissing($post);
});

test('user cannot delete another user post', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();
    $otherPost = Post::factory()->for($otherProfile)->create();

    Sanctum::actingAs($this->user);

    $response = $this->deleteJson("/api/posts/{$otherPost->id}");
    $response->assertForbidden();
});
