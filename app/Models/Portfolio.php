<?php

namespace App\Models;

use Database\Factories\PortfolioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
//  * @property string|null $parent_id
 * @property string $title
 * @property string $slug
 * @property string $type
 * @property string $category
 * @property string|null $image
 * @property array|null $gallery
 * @property string|null $video
 * @property Carbon $start_period
 * @property Carbon $finish_period
 * @property array|null $description
 * @property array|null $tags
 * @property array|null $technology
 * @property array|null $repositories
 * @property string|null $demo_url
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
//  * @property-read Portfolio|null $parentPortfolio
//  * @property-read Collection<int, Portfolio> $childPortfolios
 * @property-read Collection<int, Project> $projects
 * @property-read Collection<int, CaseStudy> $caseStudies
 * @property-read Collection<int, Feature> $features
 * @property-read Collection<int, Expertise> $expertises
 */
#[Table('portfolios')]
#[Fillable([
    'id',
    'profile_id',
    // 'parent_id',
    'title',
    'slug',
    'type',
    'category',
    'image',
    'gallery',
    'video',
    'start_period',
    'finish_period',
    'description',
    'tags',
    'technology',
    'repositories',
    'demo_url',
])]
class Portfolio extends BaseAPI
{
    /** @use HasFactory<PortfolioFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Default model attributes.
     */
    protected $attributes = [
        'repositories' => '[{"name": "", "url": "", "icon": ""}]',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'description' => 'array',
            'repositories' => 'array',
            'gallery' => 'array',
            'tags' => 'array',
            'technology' => 'array',
            'start_period' => 'date',
            'finish_period' => 'date',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'POR-'; // HasCustomId

    public const STORAGE_PATH = 'images/portfolios';

    /** @var array<int, string> Fields that contain file paths to be deleted on model deletion */
    public array $fileAttributes = ['image', 'gallery', 'video'];

    public bool $deleteFilesOnSoftDelete = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Portfolio $portfolio) {
            if ($portfolio->isForceDeleting()) {
                $portfolio->features->each->forceDelete();
                $portfolio->projects->each->forceDelete();
            }
        });
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    // public function parentPortfolio(): BelongsTo
    // {
    //     return $this->belongsTo(self::class, 'parent_id');
    // }

    // public function childPortfolios(): HasMany
    // {
    //     return $this->hasMany(self::class, 'parent_id');
    // }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function caseStudies(): HasMany
    {
        return $this->hasMany(CaseStudy::class);
    }

    /**
     * Get all of the features for the portfolio.
     */
    public function features(): MorphMany
    {
        return $this->morphMany(Feature::class, 'featureable');
    }

    public function expertises(): BelongsToMany
    {
        return $this->belongsToMany(Expertise::class, 'expertise_portfolio', 'portfolio_id', 'expertise_id');
    }
}
