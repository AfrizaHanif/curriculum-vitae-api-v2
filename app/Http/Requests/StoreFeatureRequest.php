<?php

namespace App\Http\Requests;

use App\Models\Feature;
use App\Models\Portfolio;
use App\Models\Project;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeatureRequest extends FormRequest
{
    /**
     * Allowed polymorphic types for features.
     *
     * @var array<int, class-string>
     */
    public const ALLOWED_TYPES = [
        Portfolio::class,
        Project::class,
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Feature::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'featureable_type' => ['required', 'string', Rule::in(self::ALLOWED_TYPES)],
            'featureable_id' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $type = $this->input('featureable_type');
                    if (! in_array($type, self::ALLOWED_TYPES, true)) {
                        return;
                    }

                    $parent = $type::find($value);
                    if (! $parent) {
                        $fail('The selected parent resource does not exist.');

                        return;
                    }

                    if ($parent->profile?->user_id !== $this->user()?->id) {
                        $fail('You do not have permission to attach a feature to this resource.');
                    }
                },
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'progress' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
