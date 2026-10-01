<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCaseStudyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $caseStudy = $this->route('case_study') ?? $this->route('caseStudy');

        return (bool) $this->user()?->can('update', $caseStudy);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'portfolio_id' => ['sometimes', 'required', 'string', 'exists:portfolios,id'],
            'role' => ['sometimes', 'required', 'string', 'max:255'],
            'problems' => ['sometimes', 'required', 'array'],
            'goals' => ['sometimes', 'required', 'array'],
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
