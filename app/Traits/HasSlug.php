<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin Model
 * @mixin Builder
 *
 * @method static void saving(\Closure|string $callback)
 * @method static Builder where(string|array|\Closure $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method Builder orWhere(string|array|\Closure $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 */
trait HasSlug
{
    /**
     * Automatically boot the trait to hook into model events.
     */
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            $source = method_exists($model, 'getSlugSource') ? $model->getSlugSource() : 'title';
            $prefix = method_exists($model, 'getSlugPrefix') ? $model->getSlugPrefix() : '';

            if (empty($model->slug) || ($model->isDirty($source) && ! $model->isDirty('slug'))) {
                $baseSlug = Str::slug($model->{$source});
                $slug = $prefix ? "{$prefix}-{$baseSlug}" : $baseSlug;

                $originalSlug = $slug;
                $count = 1;

                while (static::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }

                $model->slug = $slug;
            }
        });
    }

    /**
     * Determine the column used to generate the slug.
     * Defaults to 'title' if not overridden in the model.
     */
    public function getSlugSource(): string
    {
        return 'title';
    }

    /**
     * Determine the prefix used for the slug.
     * Defaults to empty string.
     */
    public function getSlugPrefix(): string
    {
        return '';
    }

    /**
     * Retrieve the model for a bound value (ID or Slug).
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }
}
