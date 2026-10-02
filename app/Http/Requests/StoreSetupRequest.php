<?php

namespace App\Http\Requests;

use App\Models\Setup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Setup::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reason' => ['sometimes', 'nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) && ! is_array($value)) {
                    $fail("The {$attribute} field must be a string or an array.");
                }
            }],
        ];
    }
}
