<?php

namespace App\Http\Requests;

use App\Models\Portfolio;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdatePortfolioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $portfolio = $this->route('portfolio');

        return (bool) $this->user()?->can('update', $portfolio);
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
        $isVideoFile = $this->file('video') instanceof UploadedFile;

        return [
            // 'parent_id' => ['sometimes', 'nullable', 'exists:portfolios,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('portfolios', 'title')->ignore($portfolio?->id)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('portfolios', 'slug')->ignore($portfolio?->id)],
            'type' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', File::image()->max('5mb')],
            'gallery' => ['sometimes', 'nullable', 'array'],
            'gallery.*' => [File::image()->max('5mb')],
            'video' => $isVideoFile
                ? ['sometimes', 'nullable', File::types(['mp4', 'mov', 'avi', 'webm', 'mkv'])->max('100mb')]
                : ['sometimes', 'nullable', 'string', 'url', 'max:500'],
            'remaining_gallery' => ['sometimes', 'nullable', 'array'],
            'start_period' => ['sometimes', 'required', 'date'],
            'finish_period' => ['sometimes', 'required', 'date', 'after_or_equal:start_period'],
            'description' => ['sometimes', 'required', 'string'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'technology' => ['sometimes', 'nullable', 'array'],
            'repositories' => ['sometimes', 'nullable', 'array'],
            'demo_url' => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
    }
}
