<?php

namespace App\Http\Resources;

use App\Models\Portfolio;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Portfolio
 */
class PortfolioResource extends JsonResource
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
            // 'parent_id' => $this->parent_id,
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
            'description' => $this->description,
            'tags' => $this->tags,
            'technology' => $this->technology,
            'repositories' => $this->repositories,
            'demo_url' => $this->demo_url,
            'case_studies' => CaseStudyResource::collection($this->whenLoaded('caseStudies')),
            'features' => FeatureResource::collection($this->whenLoaded('features')),
            'expertises' => ExpertiseResource::collection($this->whenLoaded('expertises')),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
