<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Post::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:posts,title'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:posts,slug'],
            'category' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'summary' => ['required', 'string'],
            'image' => ['sometimes', 'nullable', File::image()->max('5mb')],
            'content' => ['required', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
