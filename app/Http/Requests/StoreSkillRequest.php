<?php

namespace App\Http\Requests;

use App\Enums\SkillType;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Skill::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'string', Rule::exists(Profile::class, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(SkillType::class)],
            'display_order' => ['required', 'integer', 'max:100'],
        ];
    }
}
