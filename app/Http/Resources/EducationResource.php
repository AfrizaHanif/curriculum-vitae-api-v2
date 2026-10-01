<?php

namespace App\Http\Resources;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Education
 */
class EducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institution' => $this->institution,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'address' => $this->address,
            'degree' => $this->degree,
            'major' => $this->major,
            'gpa' => $this->gpa,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'start_period' => $this->start_period?->format('Y-m-d') ?? $this->start_period,
            'finish_period' => $this->finish_period?->format('Y-m-d') ?? $this->finish_period,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
