<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileObserver
{
    /**
     * Handle the Model "updating" event.
     */
    public function updating(Model $model): void
    {
        if (! property_exists($model, 'fileAttributes')) {
            return;
        }

        foreach ($model->fileAttributes as $field) {
            // isDirty() is used in the 'updating' event to catch changes before they save
            if ($model->isDirty($field)) {
                $oldValue = $model->getOriginal($field) ?? [];
                $newValue = $model->{$field} ?? [];

                $this->runAfterCommit(function () use ($oldValue, $newValue) {
                    if (is_array($oldValue) && is_array($newValue)) {
                        $oldNormalized = array_map(fn ($p) => ltrim($p, '/'), $oldValue);
                        $newNormalized = array_map(fn ($p) => ltrim($p, '/'), $newValue);
                        $removedClean = array_diff($oldNormalized, $newNormalized);

                        $removedFiles = [];
                        foreach ($oldValue as $oldPath) {
                            if (in_array(ltrim($oldPath, '/'), $removedClean)) {
                                $removedFiles[] = $oldPath;
                            }
                        }
                        $this->processDeletion($removedFiles);
                    } else {
                        $oldClean = is_string($oldValue) ? ltrim($oldValue, '/') : null;
                        $newClean = is_string($newValue) ? ltrim($newValue, '/') : null;

                        if ($oldClean && $oldClean !== $newClean) {
                            $this->processDeletion($oldValue);
                        }
                    }
                });
            }
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $shouldDeleteFiles = false;

        if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
            $shouldDeleteFiles = true;
        } elseif (property_exists($model, 'deleteFilesOnSoftDelete') && $model->deleteFilesOnSoftDelete) {
            $shouldDeleteFiles = true;
        }

        if (! $shouldDeleteFiles) {
            return;
        }

        if (! property_exists($model, 'fileAttributes')) {
            return;
        }

        foreach ($model->fileAttributes as $field) {
            $this->runAfterCommit(fn () => $this->processDeletion($model->{$field}));
        }
    }

    /**
     * Handle the Model "forceDeleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        if (! property_exists($model, 'fileAttributes')) {
            return;
        }

        foreach ($model->fileAttributes as $field) {
            $this->runAfterCommit(fn () => $this->processDeletion($model->{$field}));
        }
    }

    /**
     * Helper to ensure code runs after DB commit.
     * In testing environments (like Pest/RefreshDatabase), DB::afterCommit never fires
     * because the transaction is never committed. We bypass it during tests.
     */
    private function runAfterCommit(callable $callback): void
    {
        if (app()->environment('testing') || app()->runningUnitTests()) {
            $callback();

            return;
        }

        DB::afterCommit($callback);
    }

    /**
     * Internal helper to handle the deletion of strings or arrays of file paths.
     */
    private function processDeletion(mixed $value): void
    {
        if (empty($value)) {
            return;
        }

        $paths = is_array($value) ? $value : [$value];

        foreach ($paths as $path) {
            if (is_string($path) && ! filter_var($path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete(ltrim($path, '/'));
            }
        }
    }
}
