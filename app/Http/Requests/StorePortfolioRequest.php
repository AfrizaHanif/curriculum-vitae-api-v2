<?php

namespace App\Http\Requests;

use App\Models\Portfolio;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

class StorePortfolioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Portfolio::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isVideoFile = $this->file('video') instanceof UploadedFile;

        return [
            // 'parent_id' => ['sometimes', 'nullable', 'exists:portfolios,id'],
            'title' => ['required', 'string', 'max:255', 'unique:portfolios,title'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:portfolios,slug'],
            'type' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', File::image()->max('5mb')],
            'gallery' => ['sometimes', 'nullable', 'array'],
            'gallery.*' => [File::image()->max('5mb')],
            'video' => $isVideoFile
                ? ['sometimes', 'nullable', File::types(['mp4', 'mov', 'avi', 'webm', 'mkv'])->max('100mb')]
                : ['sometimes', 'nullable', 'string', 'url', 'max:500'],
            'start_period' => ['required', 'date'],
            'finish_period' => ['required', 'date', 'after_or_equal:start_period'],
            'description' => ['required', function (string $attribute, mixed $value, \Closure $fail): void {
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
