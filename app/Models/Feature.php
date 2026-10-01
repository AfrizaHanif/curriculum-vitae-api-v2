<?php

namespace App\Models;

use Database\Factories\FeatureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $featureable_type
 * @property string $featureable_id
 * @property string $title
 * @property string|null $description
 * @property int|null $progress
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model $featureable
 */
#[Table('features')]
#[Fillable([
    'id',
    'featureable_type',
    'featureable_id',
    'title',
    'description',
    'progress',
])]
class Feature extends BaseAPI
{
    /** @use HasFactory<FeatureFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'FEA-'; // HasCustomId

    protected int $idPadding = 3;

    public function featureable(): MorphTo
    {
        return $this->morphTo();
    }
}
