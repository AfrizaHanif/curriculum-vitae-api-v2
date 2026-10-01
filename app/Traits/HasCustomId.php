<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @method static void creating(\Closure|string $callback)
 * @method static Builder query()
 * @method string getKeyName()
 */
trait HasCustomId
{
    protected static function bootHasCustomId(): void
    {
        static::creating(function (self $model) {
            $keyName = $model->getKeyName();

            // If ID is already set, do nothing
            if (! empty($model->{$keyName})) {
                return;
            }

            $model->{$keyName} = $model->generateCustomId();
        });
    }

    /**
     * Generate a new custom ID.
     */
    public function generateCustomId(): string
    {
        $keyName = $this->getKeyName();
        $prefix = property_exists($this, 'idPrefix') ? $this->idPrefix : 'ID-';
        $padding = property_exists($this, 'idPadding') ? $this->idPadding : 3;

        $latest = static::query()
            ->orderBy($keyName, 'desc')
            ->first();

        $number = 1;

        if ($latest) {
            // Extract the number part from the ID
            $number = (int) substr($latest->{$keyName}, strlen($prefix)) + 1;
        }

        return $prefix.str_pad((string) $number, $padding, '0', STR_PAD_LEFT);
    }
}
