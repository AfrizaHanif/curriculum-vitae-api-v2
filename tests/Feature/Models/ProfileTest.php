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
        ->assertJsonPath('data.fullname', $this->profile->fullname)
        ->assertJsonPath('data.phone', $this->profile->phone)
        ->assertJsonPath('data.email', $this->profile->email)
        ->assertJsonPath('data.age', $this->profile->birthday->age)
        ->assertJsonMissing(['birthday']);
});

test('authenticated user can view profile with exact birthday included', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson("/api/profiles/{$this->profile->id}");

    $response->assertOk()
        ->assertJsonPath('data.age', $this->profile->birthday->age)
        ->assertJsonPath('data.birthday', $this->profile->birthday->toISOString());
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
    Storage::disk('public')->put('pdfs/Resume_EN.pdf', 'old resume content');

    $this->profile->update([
        'resume' => ['en' => '/pdfs/Resume_EN.pdf'],
    ]);

    Sanctum::actingAs($this->user);

    $file = UploadedFile::fake()->create('my_new_resume.pdf', 100, 'application/pdf');

    $response = $this->putJson("/api/profiles/{$this->profile->id}", [
        'fullname' => $this->profile->fullname,
        'phone' => $this->profile->phone,
        'email' => $this->profile->email,
        'birthday' => $this->profile->birthday->format('Y-m-d'),
        'status' => $this->profile->status,
        'resume' => [
            'en' => $file,
        ],
    ]);

    $response->assertOk();

    // Verify file exists on public disk
    expect(Storage::disk('public')->exists('pdfs/CV_Muhammad_Afriza_Hanif_EN.pdf'))->toBeTrue();

    // Verify content is the new file, not deleted
    $this->profile->refresh();
    expect($this->profile->resume['en'])->toBe('pdfs/CV_Muhammad_Afriza_Hanif_EN.pdf');
});

test('profile resume can be uploaded for multiple languages', function () {
    Storage::fake('public');

    Sanctum::actingAs($this->user);

    $fileEn = UploadedFile::fake()->create('cv_en.pdf', 100, 'application/pdf');
    $fileId = UploadedFile::fake()->create('cv_id.pdf', 100, 'application/pdf');

    $response = $this->putJson("/api/profiles/{$this->profile->id}", [
        'fullname' => $this->profile->fullname,
        'phone' => $this->profile->phone,
        'email' => $this->profile->email,
        'birthday' => $this->profile->birthday->format('Y-m-d'),
        'status' => $this->profile->status,
        'resume' => [
            'en' => $fileEn,
            'id' => $fileId,
        ],
    ]);

    $response->assertOk();

    expect(Storage::disk('public')->exists('pdfs/CV_Muhammad_Afriza_Hanif_EN.pdf'))->toBeTrue()
        ->and(Storage::disk('public')->exists('pdfs/CV_Muhammad_Afriza_Hanif_ID.pdf'))->toBeTrue();

    $this->profile->refresh();
    expect($this->profile->resume)->toBe([
        'en' => 'pdfs/CV_Muhammad_Afriza_Hanif_EN.pdf',
        'id' => 'pdfs/CV_Muhammad_Afriza_Hanif_ID.pdf',
    ]);
});

test('updating profile without file does not delete existing resume', function () {
    Storage::fake('public');

    Storage::disk('public')->put('pdfs/Resume_EN.pdf', 'resume content');
    Storage::disk('public')->put('pdfs/Resume_ID.pdf', 'resume content');

    $this->profile->update([
        'resume' => [
            'en' => 'pdfs/Resume_EN.pdf',
            'id' => 'pdfs/Resume_ID.pdf',
        ],
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

    expect(Storage::disk('public')->exists('pdfs/Resume_EN.pdf'))->toBeTrue()
        ->and(Storage::disk('public')->exists('pdfs/Resume_ID.pdf'))->toBeTrue();
    $this->profile->refresh();
    expect($this->profile->resume)->toBe([
        'en' => 'pdfs/Resume_EN.pdf',
        'id' => 'pdfs/Resume_ID.pdf',
    ])
        ->and($this->profile->fullname)->toBe('Updated Name');
});

test('profile resume can be uploaded as a single file', function () {
    Storage::fake('public');

    Sanctum::actingAs($this->user);

    $file = UploadedFile::fake()->create('single_resume.pdf', 100, 'application/pdf');

    $response = $this->putJson("/api/profiles/{$this->profile->id}", [
        'fullname' => $this->profile->fullname,
        'phone' => $this->profile->phone,
        'email' => $this->profile->email,
        'birthday' => $this->profile->birthday->format('Y-m-d'),
        'status' => $this->profile->status,
        'resume' => $file,
    ]);

    $response->assertOk();

    expect(Storage::disk('public')->exists('pdfs/CV_Muhammad_Afriza_Hanif_EN.pdf'))->toBeTrue();

    $this->profile->refresh();
    expect($this->profile->resume['en'])->toBe('pdfs/CV_Muhammad_Afriza_Hanif_EN.pdf');
});

test('profile resource transforms localized resume into storage urls', function () {
    $this->profile->update([
        'resume' => [
            'en' => 'pdfs/CV_EN.pdf',
            'id' => 'pdfs/CV_ID.pdf',
        ],
    ]);

    $response = $this->getJson("/api/profiles/{$this->profile->id}");

    $response->assertOk();
    $data = $response->json('data.resume');
    expect($data)->toBeArray()
        ->and($data['en'])->toContain('pdfs/CV_EN.pdf')
        ->and($data['id'])->toContain('pdfs/CV_ID.pdf');
});
