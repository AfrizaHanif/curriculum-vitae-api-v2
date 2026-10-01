<?php

namespace App\Models;

use App\Enums\EducationStatus;
use App\Enums\EducationType;
use Database\Factories\EducationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
 * @property string $institution
 * @property EducationType $type
 * @property string|null $address
 * @property string $degree
 * @property string $major
 * @property float|null $gpa
 * @property EducationStatus $status
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
#[Table('education')]
#[Fillable([
    'id',
    'profile_id',
    'institution',
    'type',
    'address',
    'degree',
    'major',
    'gpa',
    'status',
    'start_period',
    'finish_period',
    'description',
    'latitude',
    'longitude',
])]
class Education extends BaseAPI
{
    /** @use HasFactory<EducationFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => EducationType::class,
            'status' => EducationStatus::class,
            'gpa' => 'float',
            'start_period' => 'date',
            'finish_period' => 'date',
            'description' => 'array',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'EDU-'; // HasCustomId

    protected int $idPadding = 3;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
