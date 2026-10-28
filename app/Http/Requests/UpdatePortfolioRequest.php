<?php

namespace App\Http\Requests;

use App\Enums\ProjectType;
use App\Models\Portfolio;
use App\Traits\NormalizesMultilingualFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdatePortfolioRequest extends FormRequest
{
    use NormalizesMultilingualFields;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $portfolio = $this->route('portfolio');

        return (bool) $this->user()?->can('update', $portfolio);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $portfolio = $this->route('portfolio');
        $this->normalizeMultilingualUpdate(
            ['title', 'description'],
            $portfolio instanceof Portfolio ? $portfolio : null
        );
        $this->normalizeJsonArrayFields(['tags', 'technology', 'repositories', 'gallery', 'remaining_gallery']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Portfolio|null $portfolio */
        $portfolio = $this->route('portfolio');
        $isImageFile = $this->file('image') instanceof UploadedFile;
        $isVideoFile = $this->file('video') instanceof UploadedFile;

        return [
            'title' => ['sometimes', 'required', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) && ! is_array($value)) {
                    $fail("The {$attribute} field must be a string or an array.");
                }
            }],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('portfolios', 'slug')->ignore($portfolio?->id)],
            'type' => ['sometimes', 'required', Rule::enum(ProjectType::class)],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => $isImageFile
                ? ['sometimes', 'nullable', File::image()->max('5mb')]
                : ['sometimes', 'nullable', 'string', 'max:500'],
            'gallery' => ['sometimes', 'nullable', 'array'],
            'gallery.*' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value instanceof UploadedFile) {
                    $validator = Validator::make(['file' => $value], ['file' => [File::image()->max('5mb')]]);
                    if ($validator->fails()) {
                        $fail((string) str_replace('file', $attribute, $validator->errors()->first('file')));
                    }
                } elseif (is_string($value)) {
                    if (strlen($value) > 500) {
                        $fail("The {$attribute} field must not be greater than 500 characters.");
                    }
                } else {
                    $fail("The {$attribute} field must be an image file or a string.");
                }
            }],
            'video' => $isVideoFile
                ? ['sometimes', 'nullable', File::types(['mp4', 'mov', 'avi', 'webm', 'mkv'])->max('100mb')]
                : ['sometimes', 'nullable', 'string', 'url', 'max:500'],
            'remaining_gallery' => ['sometimes', 'nullable', 'array'],
            'start_period' => ['sometimes', 'required', 'date'],
            'finish_period' => [
                'sometimes',
                'required',
                'date',
                Rule::when(
                    $this->filled('start_period'),
                    ['after_or_equal:start_period'],
                    function (): array {
                        $portfolio = $this->route('portfolio');
                        $start = $portfolio instanceof Portfolio ? $portfolio->start_period?->format('Y-m-d') : null;

                        return $start ? ["after_or_equal:{$start}"] : [];
                    }
                ),
            ],
            'description' => ['sometimes', 'required', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) && ! is_array($value)) {
                    $fail("The {$attribute} field must be a string or an array.");
                }
            }],
            'tags' => ['sometimes', 'nullable', 'array'],
            'technology' => ['sometimes', 'nullable', 'array'],
            'repositories' => ['sometimes', 'nullable', 'array'],
            'demo_url' => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
    }
}
