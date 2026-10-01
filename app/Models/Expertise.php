<?php

namespace App\Models;

use Database\Factories\ExpertiseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
 * @property string $title
 * @property array|null $description
 * @property string|null $icon
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
 * @property-read Collection<int, Portfolio> $portfolios
 */
#[Table('expertises')]
#[Fillable([
    'id',
    'profile_id',
    'title',
    'description',
    'icon',
])]
class Expertise extends BaseAPI
{
    /** @use HasFactory<ExpertiseFactory> */
    use HasFactory, SoftDeletes;

    // Custom Properties
    protected string $idPrefix = 'XPT-'; // HasCustomId

    protected int $idPadding = 3;

    protected function casts(): array
    {
        return [
            'description' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * @return BelongsToMany<Portfolio, $this>
     */
    public function portfolios(): BelongsToMany
    {
        return $this->belongsToMany(Portfolio::class, 'expertise_portfolio', 'expertise_id', 'portfolio_id');
    }
}
