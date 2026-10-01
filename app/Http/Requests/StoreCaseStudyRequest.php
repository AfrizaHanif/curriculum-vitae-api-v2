<?php

namespace App\Http\Requests;

use App\Models\CaseStudy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCaseStudyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', CaseStudy::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'portfolio_id' => ['required', 'string', 'exists:portfolios,id'],
            'role' => ['required', 'string', 'max:255'],
            'problems' => ['required', 'array'],
            'goals' => ['required', 'array'],
            'responsibilities' => ['sometimes', 'nullable', 'array'],
            'diagrams' => ['sometimes', 'nullable', 'array'],
            'solutions' => ['sometimes', 'nullable', 'array'],
            'benefits' => ['sometimes', 'nullable', 'array'],
            'results' => ['sometimes', 'nullable', 'array'],
            'process' => ['sometimes', 'nullable', 'array'],
            'challenges' => ['sometimes', 'nullable', 'array'],
            'lessons' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
