<?php

namespace App\Http\Requests;

use App\Enums\EducationDegree;
use App\Enums\EducationStatus;
use App\Enums\EducationType;
use App\Models\Education;
use App\Traits\NormalizesMultilingualFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEducationRequest extends FormRequest
{
    use NormalizesMultilingualFields;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $education = $this->route('education');

        return (bool) $this->user()?->can('update', $education);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $education = $this->route('education');
        $this->normalizeMultilingualUpdate(
            ['major', 'description'],
            $education instanceof Education ? $education : null
        );
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
            'degree' => ['sometimes', 'required', Rule::enum(EducationDegree::class)],
            'academic_title' => ['sometimes', 'nullable', 'string', 'max:20'],
            'major' => ['sometimes', 'required', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) && ! is_array($value)) {
                    $fail("The {$attribute} field must be a string or an array.");

                    return;
                }

                if (is_array($value) && empty($value['id']) && empty($value['en'])) {
                    $fail("The {$attribute} field must contain at least an 'id' or 'en' translation.");
                }
            }],
            'gpa' => ['sometimes', 'nullable', 'numeric', 'min:0', Rule::when($this->filled('max_gpa'), ['lte:max_gpa'])],
            'max_gpa' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', Rule::enum(EducationStatus::class)],
            'start_period' => ['sometimes', 'required', 'date'],
            'finish_period' => [
                'sometimes',
                'nullable',
                'date',
                Rule::when(
                    $this->filled('start_period'),
                    ['after_or_equal:start_period'],
                    function (): array {
                        $education = $this->route('education');
                        $start = $education instanceof Education ? $education->start_period?->format('Y-m-d') : null;

                        return $start ? ["after_or_equal:{$start}"] : [];
                    }
                ),
            ],
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
