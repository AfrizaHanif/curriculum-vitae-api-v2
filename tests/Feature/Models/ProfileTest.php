<?php

/** @var TestCase $this */

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profile = Profile::factory()->for($this->user)->create();
});

test('guest can list profiles', function () {
    $response = $this->getJson('/api/profiles');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'fullname', 'email', 'phone'],
            ],
        ]);
});

test('guest can view a single profile', function () {
    $response = $this->getJson("/api/profiles/{$this->profile->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $this->profile->id)
        ->assertJsonPath('data.fullname', $this->profile->fullname);
});

test('guest cannot update a profile', function () {
    $response = $this->putJson("/api/profiles/{$this->profile->id}", [
        'fullname' => 'Updated Name',
    ]);

    $response->assertUnauthorized();
});

test('user can update their own profile', function () {
    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/profiles/{$this->profile->id}", [
        'fullname' => 'John Doe Updated',
        'phone' => '1234567890',
        'email' => 'updated@example.com',
        'birthday' => '1995-05-15',
        'status' => 'Available',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.fullname', 'John Doe Updated');

    $this->assertDatabaseHas('profiles', [
        'id' => $this->profile->id,
        'fullname' => 'John Doe Updated',
        'email' => 'updated@example.com',
    ]);
});

test('user cannot update another user profile', function () {
    $otherUser = User::factory()->create();
    $otherProfile = Profile::factory()->for($otherUser)->create();

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/profiles/{$otherProfile->id}", [
        'fullname' => 'Unauthorized Change',
    ]);

    $response->assertForbidden();
});

test('profile resume can be uploaded and is not deleted on update', function () {
    Storage::fake('public');

    // Simulate seeded file with leading slash
    Storage::disk('public')->put('pdfs/Resume.pdf', 'old resume content');

    $this->profile->update([
        'resume' => '/pdfs/Resume.pdf',
    ]);

    Sanctum::actingAs($this->user);

    $file = UploadedFile::fake()->create('my_new_resume.pdf', 100, 'application/pdf');

    $response = $this->putJson("/api/profiles/{$this->profile->id}", [
        'fullname' => $this->profile->fullname,
        'phone' => $this->profile->phone,
        'email' => $this->profile->email,
        'birthday' => $this->profile->birthday->format('Y-m-d'),
        'status' => $this->profile->status,
        'resume' => $file,
    ]);

    $response->assertOk();

    // Verify file exists on public disk
    expect(Storage::disk('public')->exists('pdfs/CV_Muhammad_Afriza_Hanif.pdf'))->toBeTrue();

    // Verify content is the new file, not deleted
    $this->profile->refresh();
    expect($this->profile->resume)->toBe('pdfs/CV_Muhammad_Afriza_Hanif.pdf');
});

test('updating profile without file does not delete existing resume', function () {
    Storage::fake('public');

    Storage::disk('public')->put('pdfs/Resume.pdf', 'resume content');

    $this->profile->update([
        'resume' => 'pdfs/Resume.pdf',
    ]);

    Sanctum::actingAs($this->user);

    $response = $this->putJson("/api/profiles/{$this->profile->id}", [
        'fullname' => 'Updated Name',
        'phone' => $this->profile->phone,
        'email' => $this->profile->email,
        'birthday' => $this->profile->birthday->format('Y-m-d'),
        'status' => $this->profile->status,
    ]);

    $response->assertOk();

    expect(Storage::disk('public')->exists('pdfs/Resume.pdf'))->toBeTrue();
    $this->profile->refresh();
    expect($this->profile->resume)->toBe('pdfs/Resume.pdf')
        ->and($this->profile->fullname)->toBe('Updated Name');
});
