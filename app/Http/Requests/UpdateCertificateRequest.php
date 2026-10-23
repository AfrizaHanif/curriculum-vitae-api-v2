<?php

namespace App\Http\Requests;

use App\Enums\CertificateType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $certificate = $this->route('certificate');

        return (bool) $this->user()?->can('update', $certificate);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::enum(CertificateType::class)],
            'issuer' => ['sometimes', 'nullable', 'string', 'max:255'],
            'issued_date' => ['sometimes', 'nullable', 'date'],
            'expired_date' => ['sometimes', 'nullable', 'date', Rule::when($this->filled('issued_date'), ['after_or_equal:issued_date'])],
            'credential_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'credential_url' => ['sometimes', 'nullable', 'url'],
            'file' => ['sometimes', 'nullable', File::types(['jpg', 'jpeg', 'png', 'pdf'])->max('5mb')],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
