<?php

namespace App\Http\Requests;

use App\Enums\SkillType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $skill = $this->route('skill');

        return (bool) $this->user()?->can('update', $skill);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::enum(SkillType::class)],
            'display_order' => ['sometimes', 'required', 'integer', 'max:100'],
        ];
    }
}
