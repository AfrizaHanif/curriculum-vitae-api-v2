<?php

/** @var TestCase $this */

use App\Enums\ProjectType;
use App\Models\Portfolio;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
    Sanctum::actingAs($this->user);
});

test('user can upload portfolio image and it is stored on public disk', function () {
    $image = UploadedFile::fake()->image('project_cover.jpg', 600, 400);

    $response = $this->postJson('/api/portfolios', [
        'title' => 'Innovative SaaS Platform',
        'type' => ProjectType::Commercial->value,
        'category' => 'Web App',
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'A robust cloud platform',
        'image' => $image,
    ]);

    $response->assertCreated();

    $portfolio = Portfolio::findOrFail($response->json('data.id'));
    expect($portfolio->image)->not->toBeNull();
    Storage::disk('public')->assertExists($portfolio->image);
});

test('updating portfolio image deletes old file from storage disk', function () {
    $firstImage = UploadedFile::fake()->image('first.jpg');

    $response = $this->postJson('/api/portfolios', [
        'title' => 'Analytics Dashboard',
        'type' => ProjectType::Commercial->value,
        'category' => 'Data',
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'Dashboard for metrics',
        'image' => $firstImage,
    ]);

    $portfolio = Portfolio::findOrFail($response->json('data.id'));
    $oldPath = $portfolio->image;
    Storage::disk('public')->assertExists($oldPath);

    // Upload replacement image
    $secondImage = UploadedFile::fake()->image('second.png');

    $updateResponse = $this->putJson("/api/portfolios/{$portfolio->id}", [
        'image' => $secondImage,
    ]);

    $updateResponse->assertOk();
    $portfolio->refresh();

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($portfolio->image);
});

test('user can upload multiple gallery images', function () {
    $gallery = [
        UploadedFile::fake()->image('slide1.jpg'),
        UploadedFile::fake()->image('slide2.jpg'),
    ];

    $response = $this->postJson('/api/portfolios', [
        'title' => 'Mobile Banking App',
        'type' => ProjectType::Commercial->value,
        'category' => 'Fintech',
        'start_period' => '2024-02-01',
        'finish_period' => '2024-08-01',
        'description' => 'Secure mobile banking application',
        'gallery' => $gallery,
    ]);

    $response->assertCreated();

    $portfolio = Portfolio::findOrFail($response->json('data.id'));
    expect($portfolio->gallery)->toBeArray()->toHaveCount(2);

    foreach ($portfolio->gallery as $storedPath) {
        Storage::disk('public')->assertExists($storedPath);
    }
});

test('updating gallery removes deleted files and keeps remaining ones', function () {
    // Seed existing portfolio with 2 stored gallery images
    $path1 = 'images/portfolios/gallery/IMG-001_1.jpg';
    $path2 = 'images/portfolios/gallery/IMG-001_2.jpg';
    Storage::disk('public')->put($path1, 'content 1');
    Storage::disk('public')->put($path2, 'content 2');

    $portfolio = Portfolio::factory()->for($this->profile)->create([
        'gallery' => [$path1, $path2],
    ]);

    $newImage = UploadedFile::fake()->image('slide3.jpg');

    // Keep path1, drop path2, add slide3
    $response = $this->putJson("/api/portfolios/{$portfolio->id}", [
        'remaining_gallery' => [$path1],
        'gallery' => [$newImage],
    ]);

    $response->assertOk();
    $portfolio->refresh();

    // path1 must still exist, path2 must be deleted, new image must exist
    Storage::disk('public')->assertExists($path1);
    Storage::disk('public')->assertMissing($path2);
    expect($portfolio->gallery)->toHaveCount(2);
});

test('user can create portfolio with gallery containing string paths or URLs', function () {
    $response = $this->postJson('/api/portfolios', [
        'title' => 'String Gallery Portfolio',
        'type' => ProjectType::Commercial->value,
        'category' => 'Web',
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'A portfolio with string gallery items',
        'gallery' => [
            'https://example.com/slide1.jpg',
            'images/portfolios/gallery/slide2.jpg',
        ],
    ]);

    $response->assertCreated();
    $portfolio = Portfolio::findOrFail($response->json('data.id'));
    expect($portfolio->gallery)->toBe([
        'https://example.com/slide1.jpg',
        'images/portfolios/gallery/slide2.jpg',
    ]);
});

test('user can update portfolio gallery by passing existing strings in gallery field', function () {
    $path1 = 'images/portfolios/gallery/existing1.jpg';
    $path2 = 'images/portfolios/gallery/existing2.jpg';
    Storage::disk('public')->put($path1, 'img1');
    Storage::disk('public')->put($path2, 'img2');

    $portfolio = Portfolio::factory()->for($this->profile)->create([
        'gallery' => [$path1, $path2],
    ]);

    // Keep path1, remove path2 by only including path1 in gallery
    $response = $this->putJson("/api/portfolios/{$portfolio->id}", [
        'gallery' => [$path1],
    ]);

    $response->assertOk();
    $portfolio->refresh();

    Storage::disk('public')->assertExists($path1);
    Storage::disk('public')->assertMissing($path2);
    expect($portfolio->gallery)->toBe([$path1]);
});

test('validation fails when gallery item is invalid type', function () {
    $response = $this->postJson('/api/portfolios', [
        'title' => 'Invalid Gallery Portfolio',
        'type' => ProjectType::Commercial->value,
        'category' => 'Web',
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'A portfolio with invalid gallery items',
        'gallery' => [
            ['nested' => 'array'],
        ],
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['gallery.0']);
});

test('force deleting portfolio cleans up files from storage disk via file observer', function () {
    $imagePath = 'images/portfolios/cover_to_delete.jpg';
    $galleryPath = 'images/portfolios/gallery/item_to_delete.jpg';

    Storage::disk('public')->put($imagePath, 'fake-image-binary');
    Storage::disk('public')->put($galleryPath, 'fake-gallery-binary');

    $portfolio = Portfolio::factory()->for($this->profile)->create([
        'image' => $imagePath,
        'gallery' => [$galleryPath],
    ]);

    Storage::disk('public')->assertExists($imagePath);
    Storage::disk('public')->assertExists($galleryPath);

    // Force delete portfolio
    $this->deleteJson("/api/portfolios/{$portfolio->id}?force=1")->assertNoContent();

    Storage::disk('public')->assertMissing($imagePath);
    Storage::disk('public')->assertMissing($galleryPath);
});

test('soft deleting portfolio retains files in storage disk', function () {
    $imagePath = 'images/portfolios/cover_retained.jpg';
    Storage::disk('public')->put($imagePath, 'binary-data');

    $portfolio = Portfolio::factory()->for($this->profile)->create([
        'image' => $imagePath,
    ]);

    $this->deleteJson("/api/portfolios/{$portfolio->id}")->assertNoContent();

    // Soft delete should NOT remove the file because deleteFilesOnSoftDelete is false
    Storage::disk('public')->assertExists($imagePath);
});

test('image validation rejects invalid file format', function () {
    $fakeScript = UploadedFile::fake()->create('malicious.php', 100, 'text/x-php');

    $response = $this->postJson('/api/portfolios', [
        'title' => 'Security Audit',
        'type' => 'Security',
        'category' => 'Audit',
        'start_period' => '2024-01-01',
        'finish_period' => '2024-06-01',
        'description' => 'Security assessment',
        'image' => $fakeScript,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['image']);
});
