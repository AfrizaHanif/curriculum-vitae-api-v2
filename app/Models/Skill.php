<?php

namespace App\Models;

use App\Enums\SkillType;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $profile_id
 * @property string $name
 * @property SkillType $type
 * @property int $display_order
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Profile $profile
 */
#[Table('skills')]
#[Fillable([
    'profile_id',
    'name',
    'type',
    'display_order',
])]
class Skill extends BaseAPI
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => SkillType::class,
        ];
    }

    // Custom Properties
    protected string $idPrefix = 'SKI-'; // HasCustomId

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }
}
