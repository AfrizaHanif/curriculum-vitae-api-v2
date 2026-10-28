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
        $label = $this->getAttribute('name') ?? $this->getAttribute('title');

        if (is_array($label)) {
            $label = $label['en'] ?? $label['id'] ?? reset($label) ?: null;
        }

        return (string) ($label ?? $this->getKey());
    }
}
