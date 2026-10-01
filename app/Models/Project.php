<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
 * @property string|null $portfolio_id
 * @property string $title
 * @property string $slug
 * @property string $type
 * @property string $category
 * @property string|null $image
 * @property array|null $gallery
 * @property string|null $video
 * @property Carbon $start_period
 * @property Carbon|null $finish_period
 * @property ProjectStatus $status
 * @property array|null $description
 * @property string|null $delay_reason
 * @property Carbon|null $resume_date
 * @property array|null $tags
 * @property array|null $technology
 * @property string|null $source_code
 * @property string|null $demo_url
 * @property bool $is_private
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
 * @property-read Portfolio|null $portfolio
 * @property-read Collection<int, Feature> $features
 */
#[Table('projects')]
#[Fillable([
    'id',
    'profile_id',
    'portfolio_id',
    'title',
    'slug',
    'type',
    'category',
    'image',
    'gallery',
    'video',
    'start_period',
    'finish_period',
    'status',
    'description',
    'delay_reason',
    'resume_date',
    'tags',
    'technology',
    'source_code',
    'is_private',
    'demo_url',
])]
class Project extends BaseAPI
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'description' => 'array',
            'status' => ProjectStatus::class,
            'tags' => 'array',
            'technology' => 'array',
            'gallery' => 'array',
            'is_private' => 'boolean',
            'start_period' => 'date',
            'finish_period' => 'date',
            'resume_date' => 'date',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'PRJ-'; // HasCustomId

    public const STORAGE_PATH = 'images/projects';

    /** @var array<int, string> Fields that contain file paths to be deleted on model deletion */
    public array $fileAttributes = ['image', 'gallery', 'video'];

    public bool $deleteFilesOnSoftDelete = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Project $project) {
            if ($project->isForceDeleting()) {
                $project->features->each->forceDelete();
            }
        });
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function features(): MorphMany
    {
        return $this->morphMany(Feature::class, 'featureable');
    }
}
