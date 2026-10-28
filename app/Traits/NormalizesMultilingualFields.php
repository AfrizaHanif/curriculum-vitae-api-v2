<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait NormalizesMultilingualFields
{
    /**
     * Normalize multilingual fields on store requests.
     *
     * @param  array<int, string>  $fields
     */
    protected function normalizeMultilingualStore(array $fields): void
    {
        $merged = [];

        foreach ($fields as $field) {
            if ($this->has("{$field}.id") || $this->has("{$field}.en")) {
                $merged[$field] = [
                    'id' => $this->input("{$field}.id"),
                    'en' => $this->input("{$field}.en"),
                ];

                continue;
            }

            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $merged[$field] = $decoded;
                } elseif (trim($value) !== '') {
                    $merged[$field] = [
                        'id' => $value,
                        'en' => null,
                    ];
                }
            } elseif (is_array($value) && isset($value[0]) && ! isset($value['id'])) {
                $merged[$field] = [
                    'id' => $value[0],
                    'en' => $value[1] ?? null,
                ];
            }
        }

        if (! empty($merged)) {
            $this->merge($merged);
        }
    }

    /**
     * Normalize multilingual fields on update requests, merging with existing model translations.
     *
     * @param  array<int, string>  $fields
     */
    protected function normalizeMultilingualUpdate(array $fields, ?Model $model): void
    {
        $merged = [];

        foreach ($fields as $field) {
            $existing = $model instanceof Model ? $model->{$field} : null;
            $existing = is_array($existing) ? $existing : [];

            if ($this->has("{$field}.id") || $this->has("{$field}.en")) {
                $merged[$field] = array_merge($existing, [
                    'id' => $this->input("{$field}.id", $existing['id'] ?? null),
                    'en' => $this->input("{$field}.en", $existing['en'] ?? null),
                ]);

                continue;
            }

            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $value = $decoded;
                } elseif (trim($value) !== '') {
                    $value = array_merge($existing, [
                        'id' => $value,
                    ]);
                }
            }

            if (is_array($value)) {
                if (isset($value[0]) && ! isset($value['id'])) {
                    $value = [
                        'id' => $value[0],
                        'en' => $value[1] ?? null,
                    ];
                }
                $merged[$field] = array_merge($existing, $value);
            }
        }

        if (! empty($merged)) {
            $this->merge($merged);
        }
    }

    /**
     * Decode stringified JSON array fields (e.g. from FormData).
     *
     * @param  array<int, string>  $fields
     */
    protected function normalizeJsonArrayFields(array $fields): void
    {
        $merged = [];

        foreach ($fields as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $merged[$field] = $decoded;
                }
            }
        }

        if (! empty($merged)) {
            $this->merge($merged);
        }
    }
}
