<?php

namespace App\Traits;

use App\Observers\FileObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @method static void registerModelEvent(string $event, \Closure|string|array $callback)
 */
trait HasFileCleanup
{
    /**
     * Automatically boot the trait to hook into model events.
     * Laravel calls methods following the boot[TraitName] pattern automatically.
     */
    protected static function bootHasFileCleanup(): void
    {
        static::registerModelEvent('updating', function (Model $model): void {
            app(FileObserver::class)->updating($model);
        });

        static::registerModelEvent('deleted', function (Model $model): void {
            app(FileObserver::class)->deleted($model);
        });

        static::registerModelEvent('forceDeleted', function (Model $model): void {
            app(FileObserver::class)->forceDeleted($model);
        });
    }
}
