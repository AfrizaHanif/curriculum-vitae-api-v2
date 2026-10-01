<?php

namespace App\Models;

use Database\Factories\CaseStudyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('case_studies')]
#[Fillable([
    'id',
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

    // Custom Properties
    protected string $idPrefix = 'CAS-'; // HasCustomId

    /**
     * Default model attributes.
     */
    protected $attributes = [
        'diagrams' => '[{"name": "", "images": ""}]',
        'solutions' => '[{"title": "", "context": "", "visual": ""}]',

    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
