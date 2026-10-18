<?php

namespace App\Models;

use App\Enums\EducationDegree;
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
 * @property EducationDegree $degree
 * @property string|null $academic_title
 * @property array<string, string>|string $major
 * @property float|null $gpa
 * @property float|null $max_gpa
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
    'academic_title',
    'major',
    'gpa',
    'max_gpa',
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
            'degree' => EducationDegree::class,
            'major' => 'array',
            'status' => EducationStatus::class,
            'gpa' => 'float',
            'max_gpa' => 'float',
            'start_period' => 'date',
            'finish_period' => 'date',
            'description' => 'array',
        ];
    }

    protected string $idPrefix = 'EDU-';

    protected int $idPadding = 3;

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
