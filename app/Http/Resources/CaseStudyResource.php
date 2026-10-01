<?php

namespace App\Http\Resources;

use App\Models\CaseStudy;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin CaseStudy
 */
class CaseStudyResource extends JsonResource
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
            'role' => $this->role,
            'problems' => $this->problems,
            'goals' => $this->goals,
            'responsibilities' => $this->responsibilities,
            'diagrams' => is_array($this->diagrams)
                ? array_map(fn ($diagram) => is_array($diagram) ? [
                    ...$diagram,
                    'images' => ! empty($diagram['images'])
                        ? (filter_var($diagram['images'], FILTER_VALIDATE_URL) ? $diagram['images'] : $disk->url($diagram['images']))
                        : null,
                ] : $diagram, $this->diagrams)
                : $this->diagrams,
            'solutions' => is_array($this->solutions)
                ? array_map(fn ($solution) => is_array($solution) ? [
                    ...$solution,
                    'visual' => ! empty($solution['visual'])
                        ? (filter_var($solution['visual'], FILTER_VALIDATE_URL) ? $solution['visual'] : $disk->url($solution['visual']))
                        : null,
                ] : $solution, $this->solutions)
                : $this->solutions,
            'benefits' => $this->benefits,
            'results' => $this->results,
            'process' => $this->process,
            'challenges' => $this->challenges,
            'lessons' => $this->lessons,
            'portfolio' => new PortfolioResource($this->whenLoaded('portfolio')),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
