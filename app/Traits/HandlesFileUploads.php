<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesFileUploads
{
    /**
     * Handle file upload and optional deletion of the old file.
     *
     * @param  Request  $request  The HTTP request instance.
     * @param  string  $key  The input field name for the file.
     * @param  string  $folder  The storage folder where the file will be stored.
     * @param  string|null  $oldPath  The path to the old file to be deleted.
     * @param  string  $disk  The storage disk to use ('public' for visible, 'local' for private).
     * @param  string|null  $customFilename  An optional custom filename (without extension). If null, a hashed name is used.
     * @param  bool  $slug  Whether to slugify and lowercase the custom filename.
     * @return string|null The path to the stored file, or the old path if no new file was uploaded.
     */
    protected function uploadFile(
        Request $request,
        string $key,
        string $folder,
        ?string $oldPath = null,
        string $disk = 'public',
        ?string $customFilename = null,
        bool $slug = true
    ): ?string {
        if ($request->hasFile($key)) {
            $file = $request->file($key);

            if ($customFilename) {
                $baseName = $slug
                    ? Str::slug($customFilename)
                    : preg_replace('/[^\w\s\.-]/u', '', trim($customFilename));

                $filename = $baseName.'.'.$file->getClientOriginalExtension();
            } else {
                $filename = $file->hashName();
            }

            $path = $file->storeAs($folder, $filename, $disk);

            $cleanPath = $path ? ltrim($path, '/') : null;
            $cleanOldPath = $oldPath ? ltrim($oldPath, '/') : null;

            if ($cleanPath && $cleanOldPath && $cleanPath !== $cleanOldPath && Storage::disk($disk)->exists($cleanOldPath)) {
                Storage::disk($disk)->delete($cleanOldPath);
            }

            return $path;
        }

        return $oldPath;
    }

    /**
     * Handle multiple file uploads (e.g., gallery).
     *
     * @param  Request  $request  The HTTP request instance.
     * @param  string  $key  The input field name for the files (array of files).
     * @param  string  $folder  The storage folder where the files will be stored.
     * @param  array  $oldPaths  An array of paths to old files to be deleted.
     * @param  string  $disk  The storage disk to use (e.g., 'public').
     * @param  string|null  $baseCustomFilename  An optional base custom filename for each file (without extension). If null, hashed names are used.
     * @return array An array of paths to the stored files, or the old paths if no new files were uploaded.
     */
    protected function uploadFiles(
        Request $request,
        string $key,
        string $folder,
        array $oldPaths = [],
        string $disk = 'public',
        ?string $baseCustomFilename = null,
        int $startIndex = 1
    ): array {
        if ($request->hasFile($key)) {
            $paths = [];
            $index = $startIndex;
            $files = $request->file($key);
            $fileArray = is_array($files) ? $files : [$files];

            foreach ($fileArray as $file) {
                $filename = $baseCustomFilename
                    ? Str::slug($baseCustomFilename.'_'.sprintf('%03d', $index)).'.'.$file->getClientOriginalExtension()
                    : $file->hashName();
                $stored = $file->storeAs($folder, $filename, $disk);
                $paths[] = $stored;
                $index++;
            }

            // Cleanup old files if new ones were successfully uploaded
            if (! empty($paths) && ! empty($oldPaths)) {
                $cleanNewPaths = array_map(fn ($p) => ltrim($p, '/'), $paths);
                foreach ($oldPaths as $oldPath) {
                    $cleanOld = ltrim($oldPath, '/');
                    if (! in_array($cleanOld, $cleanNewPaths) && Storage::disk($disk)->exists($cleanOld)) {
                        Storage::disk($disk)->delete($cleanOld);
                    }
                }
            }

            return $paths;
        }

        return $oldPaths;
    }

    /**
     * Handle updating a gallery (multiple file upload with retention of remaining files and deletion of removed ones).
     *
     * @param  Request  $request  The HTTP request instance.
     * @param  string  $key  The input field name for the files (array of files).
     * @param  string  $folder  The storage folder where the files will be stored.
     * @param  array  $currentGallery  The current array of gallery file paths.
     * @param  string  $remainingKey  The input key for remaining files (URLs/paths).
     * @param  string  $disk  The storage disk to use (e.g., 'public').
     * @param  string|null  $baseCustomFilename  An optional base custom filename for each file (without extension). If null, hashed names are used.
     * @return array The updated array of gallery file paths.
     */
    protected function updateGallery(
        Request $request,
        string $key,
        string $folder,
        array $currentGallery = [],
        string $remainingKey = 'remaining_gallery',
        string $disk = 'public',
        ?string $baseCustomFilename = null
    ): array {
        if ($request->has($remainingKey) || $request->hasFile($key)) {
            $remainingGallery = $request->input($remainingKey, []);
            if (is_string($remainingGallery)) {
                $remainingGallery = json_decode($remainingGallery, true) ?? [];
            }

            $cleanPath = function (?string $raw): string {
                if (! $raw) {
                    return '';
                }
                $parsed = parse_url($raw, PHP_URL_PATH) ?? $raw;
                $clean = ltrim($parsed, '/');
                if (str_starts_with($clean, 'api/storage/')) {
                    $clean = substr($clean, strlen('api/storage/'));
                } elseif (str_starts_with($clean, 'storage/')) {
                    $clean = substr($clean, strlen('storage/'));
                }

                return ltrim($clean, '/');
            };

            $remainingPaths = [];
            foreach ($remainingGallery as $url) {
                if ($url) {
                    $normalized = $cleanPath($url);
                    if ($normalized !== '') {
                        $remainingPaths[] = $normalized;
                    }
                }
            }

            // Normalize leading slashes and storage prefixes for comparison
            $currentNormalized = array_map($cleanPath, $currentGallery);
            $remainingNormalized = array_map($cleanPath, $remainingPaths);

            $deletedNormalized = array_diff($currentNormalized, $remainingNormalized);

            // Find matching original paths for deletion
            $deletedPaths = [];
            foreach ($currentGallery as $currPath) {
                if (in_array($cleanPath($currPath), $deletedNormalized, true)) {
                    $deletedPaths[] = $currPath;
                }
            }

            foreach ($deletedPaths as $delPath) {
                if (Storage::disk($disk)->exists($delPath)) {
                    Storage::disk($disk)->delete($delPath);
                }
            }

            $newPaths = $this->uploadFiles(
                $request,
                $key,
                $folder,
                [],
                $disk,
                $baseCustomFilename,
                count($remainingPaths) + 1
            );

            // Build final gallery array preserving relative paths
            $finalGallery = [];
            $normalizedCurrentMap = [];
            foreach ($currentGallery as $currPath) {
                $normalizedCurrentMap[$cleanPath($currPath)] = $currPath;
            }

            foreach ($remainingPaths as $remPath) {
                $cleanRem = $cleanPath($remPath);
                if (isset($normalizedCurrentMap[$cleanRem])) {
                    $finalGallery[] = $normalizedCurrentMap[$cleanRem];
                } else {
                    $finalGallery[] = $cleanRem;
                }
            }

            return array_merge($finalGallery, $newPaths);
        }

        return $currentGallery;
    }
}
