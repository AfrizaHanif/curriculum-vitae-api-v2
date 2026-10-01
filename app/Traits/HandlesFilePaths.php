<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HandlesFilePaths
{
    /**
     * Delete a file from storage, handling absolute URLs and relative paths.
     */
    protected function deleteFile(?string $path, string $disk = 'public'): void
    {
        if (empty($path)) {
            return;
        }

        // Extract path from URL if it's an absolute URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = parse_url($path, PHP_URL_PATH);
        }

        // Remove '/storage/' or 'storage/' prefix if present
        // This is necessary because Storage::disk('public') root is already 'storage/app/public'
        $path = preg_replace('/^\/?storage\//', '', $path);

        // Remove any leading slashes
        $path = ltrim($path, '/');

        Storage::disk($disk)->delete($path);
    }
}
