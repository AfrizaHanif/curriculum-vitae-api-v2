<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return (bool) $this->user()?->can('update', $project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Project|null $project */
        $project = $this->route('project');
        $isVideoFile = $this->file('video') instanceof UploadedFile;

        return [
            'portfolio_id' => ['sometimes', 'nullable', 'exists:portfolios,id'],
            'title' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('projects', 'title')->ignore($project?->id)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($project?->id)],
            'type' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', File::image()->max('5mb')],
            'gallery' => ['sometimes', 'nullable', 'array'],
            'gallery.*' => [File::image()->max('5mb')],
            'remaining_gallery' => ['sometimes', 'nullable', 'array'],
            'video' => $isVideoFile
                ? ['sometimes', 'nullable', File::types(['mp4', 'mov', 'avi', 'webm', 'mkv'])->max('100mb')]
                : ['sometimes', 'nullable', 'string', 'url', 'max:500'],
            'start_period' => ['sometimes', 'required', 'date'],
            'finish_period' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_period'],
            'status' => ['sometimes', 'required', Rule::enum(ProjectStatus::class)],
            'description' => ['sometimes', 'required', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) && ! is_array($value)) {
                    $fail("The {$attribute} field must be a string or an array.");
                }
            }],
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
