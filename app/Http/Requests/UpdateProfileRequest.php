<?php

namespace App\Http\Requests;

use App\Models\Profile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $profile = $this->route('profile');

        return (bool) $this->user()?->can('update', $profile);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $profile = $this->route('profile');
        $profileId = $profile instanceof Profile ? $profile->id : (is_string($profile) ? $profile : null);
        $isResumeFile = $this->file('resume') instanceof UploadedFile;

        return [
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'fullname' => ['sometimes', 'required', 'string', 'max:255', Rule::unique(Profile::class)->ignore($profileId)],
            'phone' => ['sometimes', 'required', 'string', 'max:15'],
            'current_city' => ['sometimes', 'nullable', 'string', 'max:40'],
            'current_province' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique(Profile::class)->ignore($profileId)],
            'birthday' => ['sometimes', 'required', 'date'],
            'tagline' => ['sometimes', 'nullable'],
            'description' => ['sometimes', 'nullable'],
            'philosophy' => ['sometimes', 'nullable'],
            'status' => ['sometimes', 'required', 'string', 'max:30'],
            'casual_photo' => ['sometimes', 'nullable', File::image()->types(['jpg', 'jpeg', 'png'])->max('2mb')],
            'formal_photo' => ['sometimes', 'nullable', File::image()->types(['jpg', 'jpeg', 'png'])->max('2mb')],
            'setup_image' => ['sometimes', 'nullable', File::image()->types(['jpg', 'jpeg', 'png'])->max('2mb')],
            'resume' => $isResumeFile
                ? ['sometimes', 'nullable', File::types(['pdf'])->max('2mb')]
                : ['sometimes', 'nullable', 'array'],
            'resume.*' => $isResumeFile ? [] : ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value instanceof UploadedFile) {
                    $validator = validator([$attribute => $value], [$attribute => File::types(['pdf'])->max('2mb')]);
                    if ($validator->fails()) {
                        $fail((string) $validator->errors()->first($attribute));
                    }
                } elseif (! is_string($value)) {
                    $fail("The {$attribute} must be a valid PDF file or path string.");
                }
            }],
        ];
    }
}
