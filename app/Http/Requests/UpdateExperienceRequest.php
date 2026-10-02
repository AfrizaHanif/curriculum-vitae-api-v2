<?php

namespace App\Http\Requests;

use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $experience = $this->route('experience');

        return (bool) $this->user()?->can('update', $experience);
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
            'company' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::enum(ExperienceType::class)],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', Rule::enum(ExperienceStatus::class)],
            'start_period' => ['sometimes', 'required', 'date'],
            'finish_period' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_period'],
            'description' => ['sometimes', 'nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) && ! is_array($value)) {
                    $fail("The {$attribute} field must be a string or an array.");
                }
            }],
            'latitude' => ['sometimes', 'nullable', 'string', 'max:15'],
            'longitude' => ['sometimes', 'nullable', 'string', 'max:15'],
        ];
    }
}
