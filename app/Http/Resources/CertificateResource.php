<?php

namespace App\Http\Resources;

use App\Models\Certificate;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Certificate
 */
class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'issuer' => $this->issuer,
            'issued_date' => $this->issued_date?->format('Y-m-d') ?? $this->issued_date,
            'credential_id' => $this->credential_id,
            'credential_url' => $this->credential_url,
            'file' => $this->file ? $disk->url($this->file) : null,
            'description' => $this->description,
            'is_featured' => $this->is_featured,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
