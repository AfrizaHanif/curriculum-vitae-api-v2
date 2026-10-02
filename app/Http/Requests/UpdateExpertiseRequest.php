<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExpertiseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $expertise = $this->route('expertise');

        return (bool) $this->user()?->can('update', $expertise);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) && ! is_array($value)) {
                    $fail("The {$attribute} field must be a string or an array.");
                }
            }],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'portfolio_ids' => ['sometimes', 'array'],
            'portfolio_ids.*' => ['string', 'exists:portfolios,id'],
        ];
    }
}
