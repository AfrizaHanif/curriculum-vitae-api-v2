<?php

namespace Database\Seeders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait SeedsLocalFiles
{
    /**
     * Seed a dummy file to the public storage if running in local environment.
     */
    protected function seedFile(?string $path, string $disk = 'public'): void
    {
        if (empty($path) || ! app()->environment('local')) {
            return;
        }

        $diskInstance = Storage::disk($disk);
        $cleanPath = ltrim($path, '/');

        if (! $diskInstance->exists($cleanPath)) {
            $filename = basename($cleanPath);

            // Check if a real file is placed in database/seeders/assets/
            $assetPath = database_path('seeders/assets/'.$cleanPath);

            if (file_exists($assetPath)) {
                $diskInstance->put($cleanPath, file_get_contents($assetPath));

                return;
            }

            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // Generate a fake image
                $fakeFile = UploadedFile::fake()->image($filename, 800, 600);
            } else {
                // Generate a fake document/PDF/etc.
                $fakeFile = UploadedFile::fake()->create($filename, 100);
            }

            $diskInstance->put($cleanPath, file_get_contents($fakeFile->getRealPath()));
        }
    }

    /**
     * Seed multiple dummy files to the public storage if running in local environment.
     */
    protected function seedFiles(array $paths, string $disk = 'public'): void
    {
        if (! app()->environment('local')) {
            return;
        }

        foreach ($paths as $path) {
            $this->seedFile($path, $disk);
        }
    }
}
