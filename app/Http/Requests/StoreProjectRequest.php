<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Project::class);
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
            'portfolio_id' => ['sometimes', 'nullable', 'exists:portfolios,id'],
            'title' => ['required', 'string', 'max:100', 'unique:projects,title'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:projects,slug'],
            'type' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', File::image()->max('5mb')],
            'gallery' => ['sometimes', 'nullable', 'array'],
            'gallery.*' => [File::image()->max('5mb')],
            'video' => $isVideoFile
                ? ['sometimes', 'nullable', File::types(['mp4', 'mov', 'avi', 'webm', 'mkv'])->max('100mb')]
                : ['sometimes', 'nullable', 'string', 'url', 'max:500'],
            'start_period' => ['required', 'date'],
            'finish_period' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_period'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'description' => ['required', 'string'],
            'delay_reason' => ['sometimes', 'nullable', 'string'],
            'resume_date' => ['sometimes', 'nullable', 'date'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'technology' => ['sometimes', 'nullable', 'array'],
            'source_code' => ['sometimes', 'nullable', 'string', 'max:255'],
            'demo_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'is_private' => ['sometimes', 'boolean'],
        ];
    }
}
