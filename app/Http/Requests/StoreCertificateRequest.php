<?php

namespace App\Http\Requests;

use App\Enums\CertificateType;
use App\Models\Certificate;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Certificate::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(CertificateType::class)],
            'issuer' => ['sometimes', 'nullable', 'string', 'max:255'],
            'issued_date' => ['sometimes', 'nullable', 'date'],
            'credential_url' => ['sometimes', 'nullable', 'url'],
            'file' => ['sometimes', 'nullable', File::types(['jpg', 'jpeg', 'png', 'pdf'])->max('5mb')],
        ];
    }
}
