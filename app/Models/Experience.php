<?php

namespace App\Models;

use App\Enums\ExperienceStatus;
use App\Enums\ExperienceType;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
 * @property string $title
 * @property string $company
 * @property ExperienceType $type
 * @property string|null $address
 * @property ExperienceStatus $status
 * @property Carbon $start_period
 * @property Carbon|null $finish_period
 * @property array|null $description
 * @property string|null $latitude
 * @property string|null $longitude
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
 */
#[Table('experiences')]
#[Fillable([
    'id',
    'profile_id',
    'title',
    'company',
    'type',
    'address',
    'status',
    'start_period',
    'finish_period',
    'description',
    'latitude',
    'longitude',
])]
class Experience extends BaseAPI
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => ExperienceType::class,
            'status' => ExperienceStatus::class,
            'start_period' => 'date',
            'finish_period' => 'date',
            'description' => 'array',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'EXP-'; // HasCustomId

    protected int $idPadding = 3;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
