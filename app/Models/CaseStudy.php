<?php

namespace App\Models;

use Database\Factories\CaseStudyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $portfolio_id
 * @property string $role
 * @property array $problems
 * @property array $goals
 * @property array|null $responsibilities
 * @property array|null $diagrams
 * @property array|null $solutions
 * @property array|null $benefits
 * @property array|null $results
 * @property array|null $process
 * @property array|null $challenges
 * @property array|null $lessons
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Portfolio $portfolio
 */
#[Table('case_studies')]
#[Fillable([
    'portfolio_id',
    'role',
    'problems',
    'goals',
    'responsibilities',
    'diagrams',
    'solutions',
    'benefits',
    'results',
    'process',
    'challenges',
    'lessons',
])]
class CaseStudy extends BaseAPI
{
    /** @use HasFactory<CaseStudyFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'problems' => 'array',
            'goals' => 'array',
            'responsibilities' => 'array',
            'diagrams' => 'array',
            'solutions' => 'array',
            'benefits' => 'array',
            'results' => 'array',
            'process' => 'array',
            'challenges' => 'array',
            'lessons' => 'array',
        ];
    }

    protected string $idPrefix = 'CAS-';

    /**
     * Default model attributes.
     */
    protected $attributes = [
        'diagrams' => '[{"name": "", "images": ""}]',
        'solutions' => '[{"title": "", "context": "", "visual": ""}]',

    ];

    /**
     * @return BelongsTo<Portfolio, $this>
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
