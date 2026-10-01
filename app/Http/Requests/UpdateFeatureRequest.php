<?php

namespace App\Http\Requests;

use App\Models\Feature;
use App\Models\Portfolio;
use App\Models\Project;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $feature = $this->route('feature');

        return (bool) $this->user()?->can('update', $feature);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'featureable_type' => ['sometimes', 'required', 'string', Rule::in([Portfolio::class, Project::class])],
            'featureable_id' => [
                'sometimes',
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    /** @var Feature|null $feature */
                    $feature = $this->route('feature');
                    $type = $this->input('featureable_type', $feature?->featureable_type);

                    if (! in_array($type, [Portfolio::class, Project::class], true)) {
                        return;
                    }

                    $parent = $type::find($value);
                    if (! $parent || $parent->profile?->user_id !== $this->user()?->id) {
                        $fail('The selected parent resource is invalid or not owned by you.');
                    }
                },
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'progress' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
