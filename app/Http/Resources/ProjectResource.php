<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
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
            'portfolio_id' => $this->portfolio_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'category' => $this->category,
            'image' => $this->image ? $disk->url($this->image) : null,
            'gallery' => is_array($this->gallery)
                ? array_map(fn ($path) => is_string($path) ? $disk->url($path) : $path, $this->gallery)
                : null,
            'video' => match (true) {
                empty($this->video) => null,
                filter_var($this->video, FILTER_VALIDATE_URL) !== false => $this->video,
                default => $disk->url($this->video),
            },
            'start_period' => $this->start_period?->format('Y-m-d') ?? $this->start_period,
            'finish_period' => $this->finish_period?->format('Y-m-d') ?? $this->finish_period,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'description' => $this->description,
            'delay_reason' => $this->delay_reason,
            'resume_date' => $this->resume_date?->format('Y-m-d') ?? $this->resume_date,
            'tags' => $this->tags,
            'technology' => $this->technology,
            'source_code' => $this->source_code,
            'demo_url' => $this->demo_url,
            'is_private' => (bool) $this->is_private,
            'portfolio' => new PortfolioResource($this->whenLoaded('portfolio')),
            'features' => FeatureResource::collection($this->whenLoaded('features')),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
