<?php

namespace App\Http\Requests;

use App\Enums\EducationStatus;
use App\Enums\EducationType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEducationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $education = $this->route('education');

        return (bool) $this->user()?->can('update', $education);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'institution' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::enum(EducationType::class)],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'degree' => ['sometimes', 'required', 'string', 'max:10'],
            'major' => ['sometimes', 'required', 'string', 'max:50'],
            'gpa' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:4.0'],
            'status' => ['sometimes', 'required', Rule::enum(EducationStatus::class)],
            'start_period' => ['sometimes', 'required', 'date'],
            'finish_period' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_period'],
            'description' => ['sometimes', 'nullable', 'array'],
            'latitude' => ['sometimes', 'nullable', 'string', 'max:15'],
            'longitude' => ['sometimes', 'nullable', 'string', 'max:15'],
        ];
    }
}
