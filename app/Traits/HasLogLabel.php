<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @method mixed getKey()
 */
trait HasLogLabel
{
    /**
     * Get the log label for the model.
     * Models using this trait should override this method.
     */
    public function getLogLabel(): string
    {
        return $this->name ?? $this->title ?? (string) $this->getKey();
    }
}
