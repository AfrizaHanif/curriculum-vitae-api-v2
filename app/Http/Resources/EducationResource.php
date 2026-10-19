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
            'type_labels' => $this->type?->labels(),
            'address' => $this->address,
            'degree' => $this->degree?->value,
            'degree_label' => $this->degree?->label(),
            'degree_labels' => $this->degree?->labels(),
            'academic_title' => $this->academic_title,
            'major' => $this->major,
            'gpa' => $this->gpa,
            'max_gpa' => $this->max_gpa,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'status_labels' => $this->status?->labels(),
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
