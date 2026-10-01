<?php

/** @var TestCase $this */

use App\Traits\HandlesFilePaths;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->handler = new class
    {
        use HandlesFilePaths;

        public function remove(?string $path, string $disk = 'public'): void
        {
            $this->deleteFile($path, $disk);
        }
    };
});

test('deletes file with standard relative path', function () {
    Storage::disk('public')->put('images/pic.png', 'test-data');
    Storage::disk('public')->assertExists('images/pic.png');

    $this->handler->remove('images/pic.png');

    Storage::disk('public')->assertMissing('images/pic.png');
});

test('normalizes and deletes file given full absolute url', function () {
    Storage::disk('public')->put('images/pic.png', 'test-data');

    $this->handler->remove('http://localhost:8000/storage/images/pic.png');

    Storage::disk('public')->assertMissing('images/pic.png');
});

test('normalizes and deletes file given leading slash and storage prefix', function () {
    Storage::disk('public')->put('documents/sample.pdf', 'pdf-bytes');

    $this->handler->remove('/storage/documents/sample.pdf');

    Storage::disk('public')->assertMissing('documents/sample.pdf');
});

test('handles empty and null paths gracefully without errors', function () {
    $this->handler->remove(null);
    $this->handler->remove('');

    expect(true)->toBeTrue();
});
