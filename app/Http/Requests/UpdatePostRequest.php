<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $post = $this->route('post');

        return (bool) $this->user()?->can('update', $post);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Post|null $post */
        $post = $this->route('post');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('posts', 'title')->ignore($post?->id)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($post?->id)],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'summary' => ['sometimes', 'required', 'string'],
            'image' => ['sometimes', 'nullable', File::image()->max('5mb')],
            'content' => ['sometimes', 'required', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
